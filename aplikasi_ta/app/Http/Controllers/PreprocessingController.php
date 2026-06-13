<?php

namespace App\Http\Controllers;

use App\Models\PermintaanDarah;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PreprocessingController extends Controller
{
    public function index()
    {
        $preprocessed = session('preprocessed_data');
        $outliers = session('outliers');

        return view('admin.preprocessing.index', compact('preprocessed', 'outliers'));
    }

    // Server-side DataTables endpoint untuk data mentah
    public function data()
    {
        $query = PermintaanDarah::with(['rumahSakit', 'komponenDarah'])
            ->orderBy('tanggal', 'asc');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('tanggal_formatted', function ($row) {
                return $row->tanggal->format('d/m/Y');
            })
            ->addColumn('rumah_sakit_nama', function ($row) {
                return $row->rumahSakit->nama;
            })
            ->addColumn('komponen_kode', function ($row) {
                return $row->komponenDarah->kode;
            })
            ->make(true);
    }

    // AJAX: Cek jumlah data tersedia berdasarkan filter
    public function cekData(Request $request)
    {
        $query = PermintaanDarah::where('golongan_darah', $request->golongan_darah)
            ->where('komponen_darah_id', $request->komponen_darah_id);

        if ($request->tanggal_mulai) {
            $query->where('tanggal', '>=', $request->tanggal_mulai);
        }
        if ($request->tanggal_selesai) {
            $query->where('tanggal', '<=', $request->tanggal_selesai);
        }

        $count = (clone $query)->count();
        $hariUnik = (clone $query)->distinct('tanggal')->count('tanggal');

        $aggregateValues = (clone $query)
            ->selectRaw('tanggal, SUM(jumlah) as total')
            ->groupBy('tanggal')
            ->pluck('total')
            ->map(fn($value) => (float) $value)
            ->toArray();

        $mean = count($aggregateValues) > 0 ? array_sum($aggregateValues) / count($aggregateValues) : 0;
        $median = count($aggregateValues) > 0 ? $this->calculateMedian($aggregateValues) : 0;

        // Hitung total hari dalam range
        $totalHari = 0;
        if ($request->tanggal_mulai && $request->tanggal_selesai) {
            $totalHari = Carbon::parse($request->tanggal_mulai)->diffInDays(Carbon::parse($request->tanggal_selesai)) + 1;
        }

        return response()->json([
            'total_record' => $count,
            'hari_ada_data' => $hariUnik,
            'total_hari_range' => $totalHari,
            'hari_kosong' => $totalHari > 0 ? $totalHari - $hariUnik : 0,
            'mean' => round($mean, 2),
            'median' => round($median, 2),
        ]);
    }

    // Melakukan preprocessing: aggregate harian, imputasi missing values, dan deteksi outlier dengan IQR
    public function proses(Request $request)
    {
        $request->validate([
            'golongan_darah' => 'required|in:A,B,AB,O',
            'komponen_darah_id' => 'required|exists:komponen_darah,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'metode_imputasi' => 'required|in:zero,mean,median',
        ]);

        $golongan = $request->golongan_darah;
        $komponenId = $request->komponen_darah_id;

        // Ambil data permintaan sesuai filter dan range tanggal
        $permintaan = PermintaanDarah::where('golongan_darah', $golongan)
            ->where('komponen_darah_id', $komponenId)
            ->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_selesai])
            ->orderBy('tanggal', 'asc')
            ->get();

        if ($permintaan->isEmpty()) {
            return redirect()->route('preprocessing.index')
                ->with('error', 'Tidak ada data permintaan dalam range tanggal yang dipilih.');
        }

        // Aggregate per hari (jumlahkan semua permintaan di tanggal yang sama)
        $aggregated = $permintaan->groupBy(fn($item) => $item->tanggal->format('Y-m-d'))
            ->map(fn($group) => $group->sum('jumlah'));

        $availableValues = $aggregated->values()->map(fn($value) => (float) $value)->toArray();
        $mean = count($availableValues) > 0 ? array_sum($availableValues) / count($availableValues) : 0;
        $median = count($availableValues) > 0 ? $this->calculateMedian($availableValues) : 0;
        $imputationValue = match ($request->metode_imputasi) {
            'mean' => $mean,
            'median' => $median,
            default => 0,
        };

        // Gunakan range tanggal yang dipilih user
        $startDate = Carbon::parse($request->tanggal_mulai);
        $endDate = Carbon::parse($request->tanggal_selesai);
        $period = CarbonPeriod::create($startDate, $endDate);

        // Isi missing values sesuai metode yang dipilih user
        $filledData = [];
        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $isMissing = !isset($aggregated[$dateStr]);
            $filledData[] = [
                'tanggal' => $dateStr,
                'jumlah' => $isMissing ? round($imputationValue, 2) : $aggregated[$dateStr],
                'is_missing' => $isMissing,
            ];
        }

        // Deteksi outlier hanya dari data asli yang tersedia, bukan dari data hasil imputasi.
        // Ini mencegah nilai imputasi yang dominan membuat data normal ikut dianggap outlier.
        $originalValues = array_values($availableValues);
        [$lowerBound, $upperBound] = $this->calculateOutlierBounds($originalValues);

        // Tandai dan handle outlier (ganti dengan median dari data asli)
        $median = $this->calculateMedian($originalValues);
        $outliers = [];

        foreach ($filledData as $index => $item) {
            if (!$item['is_missing'] && ($item['jumlah'] < $lowerBound || $item['jumlah'] > $upperBound)) {
                $outliers[] = [
                    'tanggal' => $item['tanggal'],
                    'nilai_asli' => $item['jumlah'],
                    'nilai_pengganti' => $median,
                ];
                $filledData[$index]['jumlah'] = $median;
            }
        }

        // Simpan hasil preprocessing ke session
        session([
            'preprocessed_data' => $filledData,
            'outliers' => $outliers,
            'preprocessing_config' => [
                'golongan_darah' => $golongan,
                'komponen_darah_id' => $komponenId,
                'metode_imputasi' => $request->metode_imputasi,
                'nilai_imputasi' => round($imputationValue, 2),
            ],
        ]);

        return redirect()->route('preprocessing.index')
            ->with('success', 'Preprocessing berhasil. ' . count($filledData) . ' data diproses, ' . count($outliers) . ' outlier terdeteksi dan diganti.');
    }

    // Deteksi outlier menggunakan metode IQR (Interquartile Range)
    private function detectOutliers(array $values): array
    {
        [$lowerBound, $upperBound] = $this->calculateOutlierBounds($values);

        $outlierIndices = [];
        foreach ($values as $index => $value) {
            if ($value < $lowerBound || $value > $upperBound) {
                $outlierIndices[] = $index;
            }
        }

        return $outlierIndices;
    }

    // Hitung batas outlier IQR dari data asli
    private function calculateOutlierBounds(array $values): array
    {
        if (count($values) < 4) {
            return [-PHP_FLOAT_MAX, PHP_FLOAT_MAX];
        }

        $sorted = $values;
        sort($sorted);

        $q1 = $this->calculatePercentile($sorted, 25);
        $q3 = $this->calculatePercentile($sorted, 75);
        $iqr = $q3 - $q1;

        if ($iqr == 0) {
            return [-PHP_FLOAT_MAX, PHP_FLOAT_MAX];
        }

        return [
            $q1 - (1.5 * $iqr),
            $q3 + (1.5 * $iqr),
        ];
    }

    // Hitung percentile dari array yang sudah di-sort
    private function calculatePercentile(array $sorted, float $percentile): float
    {
        $count = count($sorted);
        $index = ($percentile / 100) * ($count - 1);
        $lower = (int) floor($index);
        $upper = (int) ceil($index);
        $fraction = $index - $lower;

        if ($lower == $upper) {
            return $sorted[$lower];
        }

        return $sorted[$lower] + ($fraction * ($sorted[$upper] - $sorted[$lower]));
    }

    // Hitung median dari array
    private function calculateMedian(array $values): float
    {
        $sorted = $values;
        sort($sorted);
        $count = count($sorted);
        $middle = (int) floor($count / 2);

        if ($count % 2 == 0) {
            return ($sorted[$middle - 1] + $sorted[$middle]) / 2;
        }

        return $sorted[$middle];
    }
}
