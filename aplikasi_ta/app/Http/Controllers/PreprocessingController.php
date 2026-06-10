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

        $count = $query->count();
        $hariUnik = $query->distinct('tanggal')->count('tanggal');

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
        ]);
    }

    // Melakukan preprocessing: aggregate harian, mengisi missing values (hari tanpa permintaan = 0), dan deteksi outlier dengan IQR
    public function proses(Request $request)
    {
        $request->validate([
            'golongan_darah' => 'required|in:A,B,AB,O',
            'komponen_darah_id' => 'required|exists:komponen_darah,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
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

        // Gunakan range tanggal yang dipilih user
        $startDate = Carbon::parse($request->tanggal_mulai);
        $endDate = Carbon::parse($request->tanggal_selesai);
        $period = CarbonPeriod::create($startDate, $endDate);

        // Isi missing values dengan 0 untuk hari yang tidak ada permintaan
        $filledData = [];
        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $filledData[] = [
                'tanggal' => $dateStr,
                'jumlah' => $aggregated[$dateStr] ?? 0,
            ];
        }

        // Deteksi outlier menggunakan metode IQR
        $values = array_column($filledData, 'jumlah');
        $outlierIndices = $this->detectOutliers($values);

        // Tandai dan handle outlier (ganti dengan median)
        $median = $this->calculateMedian($values);
        $outliers = [];

        foreach ($outlierIndices as $index) {
            $outliers[] = [
                'tanggal' => $filledData[$index]['tanggal'],
                'nilai_asli' => $filledData[$index]['jumlah'],
                'nilai_pengganti' => $median,
            ];
            $filledData[$index]['jumlah'] = $median;
        }

        // Simpan hasil preprocessing ke session
        session([
            'preprocessed_data' => $filledData,
            'outliers' => $outliers,
            'preprocessing_config' => [
                'golongan_darah' => $golongan,
                'komponen_darah_id' => $komponenId,
            ],
        ]);

        return redirect()->route('preprocessing.index')
            ->with('success', 'Preprocessing berhasil. ' . count($filledData) . ' data diproses, ' . count($outliers) . ' outlier terdeteksi dan diganti.');
    }

    // Deteksi outlier menggunakan metode IQR (Interquartile Range)
    private function detectOutliers(array $values): array
    {
        $sorted = $values;
        sort($sorted);
        $count = count($sorted);

        $q1 = $this->calculatePercentile($sorted, 25);
        $q3 = $this->calculatePercentile($sorted, 75);
        $iqr = $q3 - $q1;

        $lowerBound = $q1 - (1.5 * $iqr);
        $upperBound = $q3 + (1.5 * $iqr);

        $outlierIndices = [];
        foreach ($values as $index => $value) {
            if ($value < $lowerBound || $value > $upperBound) {
                $outlierIndices[] = $index;
            }
        }

        return $outlierIndices;
    }

    // Hitung percentile dari array yang sudah di-sort
    private function calculatePercentile(array $sorted, float $percentile): float
    {
        $count = count($sorted);

        $index = ($percentile / 100) * ($count - 1);

        $lower = (int) floor($index);
        $upper = (int) ceil($index);

        $fraction = $index - $lower;

        if ($lower === $upper) {
            return (float) $sorted[$lower];
        }

        return (float) (
            $sorted[$lower] +
            ($fraction * ($sorted[$upper] - $sorted[$lower]))
        );
    }

    // Hitung median dari array
    private function calculateMedian(array $values): float
    {
        $sorted = $values;
        sort($sorted);

        $count = count($sorted);
        $middle = intdiv($count, 2);

        if ($count % 2 === 0) {
            return ($sorted[$middle - 1] + $sorted[$middle]) / 2;
        }

        return (float) $sorted[$middle];
    }
}
