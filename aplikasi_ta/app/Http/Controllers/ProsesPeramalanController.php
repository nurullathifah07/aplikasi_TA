<?php

namespace App\Http\Controllers;

use App\Models\KomponenDarah;
use App\Models\PermintaanDarah;
use App\Models\Prediksi;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class ProsesPeramalanController extends Controller
{
    public function index()
    {
        $komponenDarah = KomponenDarah::all();
        $hasil = session('prediksi_hasil');

        return view('admin.peramalan.index', compact('komponenDarah', 'hasil'));
    }

    public function proses(Request $request)
    {
        $request->validate([
            'golongan_darah'    => 'required|in:A,B,AB,O',
            'komponen_darah_id' => 'required|exists:komponen_darah,id',
            'tanggal_mulai'     => 'required|date',
            'tanggal_selesai'   => 'required|date|after_or_equal:tanggal_mulai',
            'rasio_split'       => 'required|in:70:30,80:20,90:10',

            // ubah ini
            'periode_prediksi' => 'required|in:1,7,custom',

            'tanggal_prediksi' => 'nullable|date',
        ]);

        $golongan   = $request->golongan_darah;
        $komponenId = $request->komponen_darah_id;

        // ==================== PREPROCESSING ====================

        // 1. Ambil data permintaan sesuai filter
        $permintaan = PermintaanDarah::where('golongan_darah', $golongan)
            ->where('komponen_darah_id', $komponenId)
            ->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_selesai])
            ->orderBy('tanggal')
            ->get();

        if ($permintaan->isEmpty()) {
            return redirect()->route('peramalan.index')
                ->with('error', 'Tidak ada data permintaan pada rentang tanggal yang dipilih.');
        }

        // 2. Agregasi per hari
        $aggregated = $permintaan
            ->groupBy(fn($item) => $item->tanggal->format('Y-m-d'))
            ->map(fn($group) => (float) $group->sum('jumlah'));

        // 3. Hitung median dari data asli (untuk imputasi & pengganti outlier)
        $availableValues = $aggregated->values()->toArray();
        $median          = $this->calculateMedian($availableValues);

        // 4. Isi missing value (hari tanpa data → median)
        $period      = CarbonPeriod::create($request->tanggal_mulai, $request->tanggal_selesai);
        $filledData  = [];
        $missingCount = 0;

        foreach ($period as $date) {
            $dateStr   = $date->format('Y-m-d');
            $isMissing = !isset($aggregated[$dateStr]);
            if ($isMissing) $missingCount++;
            $filledData[] = [
                'tanggal'    => $dateStr,
                'jumlah'     => $isMissing ? round($median, 2) : $aggregated[$dateStr],
                'is_missing' => $isMissing,
            ];
        }

        // 5. Deteksi & ganti outlier dengan IQR (hanya dari data asli)
        [$lowerBound, $upperBound] = $this->calculateOutlierBounds($availableValues);
        $outliers = [];

        foreach ($filledData as &$item) {
            if (!$item['is_missing'] && ($item['jumlah'] < $lowerBound || $item['jumlah'] > $upperBound)) {
                $outliers[] = [
                    'tanggal'        => $item['tanggal'],
                    'nilai_asli'     => $item['jumlah'],
                    'nilai_pengganti' => $median,
                ];
                $item['jumlah'] = $median;
            }
        }
        unset($item);

        // ==================== HOLT'S LINEAR ====================

        $data       = array_column($filledData, 'jumlah');
        $tanggal    = array_column($filledData, 'tanggal');
        $totalData  = count($data);

        if ($totalData < 4) {
            return redirect()->route('peramalan.index')
                ->with('error', 'Data terlalu sedikit. Minimal 4 hari data untuk menjalankan prediksi.');
        }

        // 6. Split data latih & uji
        [$trainRatio] = explode(':', $request->rasio_split);
        $trainSize = (int) round($totalData * ($trainRatio / 100));
        $testSize  = $totalData - $trainSize;

        if ($testSize < 2) {
            return redirect()->route('peramalan.index')
                ->with('error', 'Data uji terlalu sedikit. Gunakan rasio split yang lebih kecil atau perluas rentang tanggal.');
        }

        $trainData   = array_slice($data, 0, $trainSize);
        $testData    = array_slice($data, $trainSize);
        $trainTgl    = array_slice($tanggal, 0, $trainSize);
        $testTgl     = array_slice($tanggal, $trainSize);

        // 7. Tentukan alpha & beta
        $optimasiOtomatis = $request->boolean('optimasi_otomatis');

        if ($optimasiOtomatis) {
            [$alpha, $beta] = $this->optimasiParameter($trainData, $testData);
        } else {
            $request->validate([
                'alpha' => 'required|numeric|min:0.01|max:0.99',
                'beta'  => 'required|numeric|min:0.01|max:0.99',
            ]);
            $alpha = (float) $request->alpha;
            $beta  = (float) $request->beta;
        }

        // 8. Hitung jumlah periode forecast yang dibutuhkan
        if ($request->periode_prediksi == 'custom') {

            $tanggalSelesai = Carbon::parse($request->tanggal_selesai);

            $tanggalPrediksi = Carbon::parse($request->tanggal_prediksi);

            $periode = $tanggalSelesai->diffInDays($tanggalPrediksi);

            if ($periode < 1 || $periode > 7) {

                return back()
                    ->withErrors([
                        'tanggal_prediksi' => 'Tanggal prediksi maksimal 7 hari setelah tanggal selesai data.'
                    ])
                    ->withInput();
            }

        } else {

            $periode = (int) $request->periode_prediksi;

        }
        $lastDate      = Carbon::parse(end($tanggal));
        $totalForecast = $testSize + $periode; // uji + prediksi

        // 9. Jalankan Holt's Linear
        $hasilHolts = $this->holtsLinearDetail($trainData, $alpha, $beta, $totalForecast);
        $detail     = $hasilHolts['detail'];
        $forecast   = $hasilHolts['forecast'];

        // 10. Evaluasi pada data uji
        $forecastTest = array_slice($forecast, 0, $testSize);
        $rmse = round($this->hitungRMSE($testData, $forecastTest), 4);
        $mape = round($this->hitungMAPE($testData, $forecastTest), 4);
        $mae  = round($this->hitungMAE($testData, $forecastTest), 4);

        // 11. Hasil prediksi (periode_prediksi hari setelah data terakhir)
        $forecastPrediksi = array_slice($forecast, $testSize, $periode);
        $prediksiHari = [];
        for ($i = 0; $i < $periode; $i++) {
            $prediksiHari[] = [
                'tanggal' => $lastDate->copy()->addDays($i + 1)->format('Y-m-d'),
                'nilai'   => max(0, round($forecastPrediksi[$i] ?? 0, 2)),
            ];
        }

        // Jika mode "Tanggal Sendiri", tampilkan hanya tanggal yang dipilih
        if ($request->periode_prediksi == 'custom') {

            $prediksiHari = [[
                'tanggal' => $request->tanggal_prediksi,
                'nilai'   => max(0, round(end($forecastPrediksi), 2)),
            ]];

        }

        // 12. Simpan ke database
        Prediksi::where('golongan_darah', $golongan)
            ->where('komponen_darah_id', $komponenId)
            ->delete();

        foreach ($prediksiHari as $p) {
            Prediksi::create([
                'tanggal_prediksi' => Carbon::today(),
                'golongan_darah'   => $golongan,
                'komponen_darah_id' => $komponenId,
                'tanggal_target'   => $p['tanggal'],
                'nilai_prediksi'   => $p['nilai'],
                'alpha'            => $alpha,
                'beta'             => $beta,
                'rmse'             => $rmse,
                'mape'             => $mape,
                'mae'              => $mae,
                'rasio_split'      => $request->rasio_split,
            ]);
        }

        // 13. Susun rekapitulasi
        $rekapitulasi = [];

        foreach ($detail as $d) {
            $rekapitulasi[] = [
                'hari'       => $d['i'],
                'tanggal'    => $trainTgl[$d['i'] - 1] ?? '-',
                'permintaan' => $d['yt'],
                'level'      => round($d['lt'], 4),
                'trend'      => round($d['tt'], 4),
                'forecast'   => $d['ft'] !== null ? round($d['ft'], 4) : '-',
                'tipe'       => 'latih',
            ];
        }

        for ($i = 0; $i < $testSize; $i++) {
            $rekapitulasi[] = [
                'hari'       => $trainSize + $i + 1,
                'tanggal'    => $testTgl[$i],
                'permintaan' => $testData[$i],
                'level'      => '-',
                'trend'      => '-',
                'forecast'   => round($forecastTest[$i], 4),
                'tipe'       => 'uji',
            ];
        }

        foreach ($prediksiHari as $i => $p) {
            $rekapitulasi[] = [
                'hari'       => $totalData + $i + 1,
                'tanggal'    => $p['tanggal'],
                'permintaan' => '?',
                'level'      => '-',
                'trend'      => '-',
                'forecast'   => $p['nilai'],
                'tipe'       => 'prediksi',
            ];
        }

        // 14. Simpan ke session
        session([
            'prediksi_hasil' => [

                // ======== DATA PREPROCESSING ========
                'preprocessed_data' => $filledData,
                'outliers'          => $outliers,
                'missing_count'     => $missingCount,
                'nilai_imputasi'    => round($median,2),

                // ======== PARAMETER ========
                'golongan_darah'    => $golongan,
                'komponen_darah_id' => $komponenId,
                'tanggal_mulai'     => $request->tanggal_mulai,
                'tanggal_selesai'   => $request->tanggal_selesai,

                'rasio_split'       => $request->rasio_split,
                'periode_prediksi'  => $periode,

                'optimasi_otomatis' => $optimasiOtomatis,
                'alpha'             => $alpha,
                'beta'              => $beta,

                // ======== EVALUASI ========
                'rmse'              => $rmse,
                'mape'              => $mape,
                'mae'               => $mae,

                // ======== DATA ========
                'total_data'        => $totalData,
                'train_size'        => $trainSize,
                'test_size'         => $testSize,

                'outlier_count'     => count($outliers),

                // ======== GRAFIK ========
                'data_aktual'       => $testData,
                'data_forecast'     => $forecastTest,
                'test_tanggal'      => $testTgl,

                // ======== PREDIKSI ========
                'prediksi_hari'     => $prediksiHari,

                // ======== REKAP ========
                'rekapitulasi'      => $rekapitulasi,
            ]
        ]);

        return redirect()->route('peramalan.index')
            ->with('success', 'Prediksi berhasil. MAPE: ' . $mape . '% | ' . $periode . ' hari ke depan telah dihitung.');
    }

    // ==================== HELPER METHODS ====================

    private function holtsLinearDetail(array $data, float $alpha, float $beta, int $forecastPeriods): array
    {
        $n     = count($data);
        $level = $data[0];
        $trend = $data[1] - $data[0];
        $detail = [];
        $forecasts = [];

        $detail[] = ['i' => 1, 'yt' => $data[0], 'lt' => $level, 'tt' => $trend, 'ft' => null];

        for ($t = 1; $t < $n; $t++) {
            $prevLevel = $level;
            $prevTrend = $trend;
            $ft        = $prevLevel + $prevTrend;
            $level     = $alpha * $data[$t] + (1 - $alpha) * ($prevLevel + $prevTrend);
            $trend     = $beta * ($level - $prevLevel) + (1 - $beta) * $prevTrend;

            $detail[] = ['i' => $t + 1, 'yt' => $data[$t], 'lt' => $level, 'tt' => $trend, 'ft' => $ft];
        }

        for ($m = 1; $m <= $forecastPeriods; $m++) {
            $forecasts[] = $level + ($m * $trend);
        }

        return ['detail' => $detail, 'forecast' => $forecasts];
    }

    private function optimasiParameter(array $trainData, array $testData): array
    {
        $bestAlpha = 0.1;
        $bestBeta  = 0.1;
        $bestError = PHP_FLOAT_MAX;
        $testSize  = count($testData);

        for ($a = 1; $a <= 9; $a++) {
            for ($b = 1; $b <= 9; $b++) {
                $alpha = round($a / 10, 1);
                $beta  = round($b / 10, 1);
                $hasil = $this->holtsLinearDetail($trainData, $alpha, $beta, $testSize);
                $mse   = $this->hitungMSE($testData, $hasil['forecast']);

                if ($mse < $bestError) {
                    $bestError = $mse;
                    $bestAlpha = $alpha;
                    $bestBeta  = $beta;
                }
            }
        }

        return [$bestAlpha, $bestBeta];
    }

    private function hitungMSE(array $actual, array $forecast): float
    {
        $n   = min(count($actual), count($forecast));
        $sum = 0;
        for ($i = 0; $i < $n; $i++) $sum += pow($actual[$i] - $forecast[$i], 2);
        return $n > 0 ? $sum / $n : 0;
    }

    private function hitungRMSE(array $actual, array $forecast): float
    {
        return sqrt($this->hitungMSE($actual, $forecast));
    }

    private function hitungMAE(array $actual, array $forecast): float
    {
        $n   = min(count($actual), count($forecast));
        $sum = 0;
        for ($i = 0; $i < $n; $i++) $sum += abs($actual[$i] - $forecast[$i]);
        return $n > 0 ? $sum / $n : 0;
    }

    private function hitungMAPE(array $actual, array $forecast): float
    {
        $n     = min(count($actual), count($forecast));
        $sum   = 0;
        $count = 0;
        for ($i = 0; $i < $n; $i++) {
            if ($actual[$i] != 0) {
                $sum += abs(($actual[$i] - $forecast[$i]) / $actual[$i]);
                $count++;
            }
        }
        return $count > 0 ? ($sum / $count) * 100 : 0;
    }

    private function calculateMedian(array $values): float
    {
        if (empty($values)) return 0;
        $sorted = $values;
        sort($sorted);
        $count  = count($sorted);
        $middle = (int) floor($count / 2);
        return $count % 2 === 0
            ? ($sorted[$middle - 1] + $sorted[$middle]) / 2
            : $sorted[$middle];
    }

    private function calculateOutlierBounds(array $values): array
    {
        if (count($values) < 4) return [-PHP_FLOAT_MAX, PHP_FLOAT_MAX];

        $sorted = $values;
        sort($sorted);
        $q1  = $this->calculatePercentile($sorted, 25);
        $q3  = $this->calculatePercentile($sorted, 75);
        $iqr = $q3 - $q1;

        if ($iqr == 0) return [-PHP_FLOAT_MAX, PHP_FLOAT_MAX];

        return [$q1 - 1.5 * $iqr, $q3 + 1.5 * $iqr];
    }

    private function calculatePercentile(array $sorted, float $percentile): float
    {
        $count    = count($sorted);
        $index    = ($percentile / 100) * ($count - 1);
        $lower    = (int) floor($index);
        $upper    = (int) ceil($index);
        $fraction = $index - $lower;

        return $lower == $upper
            ? $sorted[$lower]
            : $sorted[$lower] + ($fraction * ($sorted[$upper] - $sorted[$lower]));
    }
}
