<?php

namespace App\Http\Controllers;

use App\Models\KomponenDarah;
use App\Models\Prediksi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HoltsLinearController extends Controller
{
    public function index()
    {
        $komponenDarah = KomponenDarah::all();
        $hasil = session('holts_hasil');

        return view('admin.holts_linear.index', compact('komponenDarah', 'hasil'));
    }

    // Menjalankan proses Holt's Linear Exponential Smoothing
    // Melakukan split data latih/uji, menghitung level & trend, lalu menghasilkan forecasting
    public function proses(Request $request)
    {
        $request->validate([
            'golongan_darah' => 'required|in:A,B,AB,O',
            'komponen_darah_id' => 'required|exists:komponen_darah,id',
            'rasio_split' => 'required|in:70:30,80:20,90:10',
            'tanggal_prediksi_mulai' => 'required|date',
            'tanggal_prediksi_selesai' => 'required|date|after_or_equal:tanggal_prediksi_mulai',
        ]);

        // Ambil data preprocessing dari session
        $preprocessed = session('preprocessed_data');
        $config = session('preprocessing_config');

        if (!$preprocessed) {
            return redirect()->route('holts.index')
                ->with('error', 'Silakan lakukan preprocessing terlebih dahulu.');
        }

        // Validasi bahwa data preprocessing sesuai dengan yang dipilih
        if ($config['golongan_darah'] !== $request->golongan_darah ||
            $config['komponen_darah_id'] != $request->komponen_darah_id) {
            return redirect()->route('holts.index')
                ->with('error', 'Data preprocessing tidak sesuai. Lakukan preprocessing ulang untuk golongan dan komponen yang dipilih.');
        }

        $data = array_column($preprocessed, 'jumlah');
        $tanggal = array_column($preprocessed, 'tanggal');
        $totalData = count($data);

        if ($totalData < 4) {
            return redirect()->route('holts.index')
                ->with('error', 'Data terlalu sedikit. Minimal 4 data untuk proses Holt\'s Linear.');
        }

        // Split data latih dan uji berdasarkan rasio
        list($trainRatio) = explode(':', $request->rasio_split);
        $trainSize = (int) round($totalData * ($trainRatio / 100));
        $testSize = $totalData - $trainSize;

        if ($testSize < 2) {
            return redirect()->route('holts.index')
                ->with('error', 'Data uji terlalu sedikit. Gunakan rasio split yang lebih kecil atau tambah data permintaan.');
        }

        $trainData = array_slice($data, 0, $trainSize);
        $testData = array_slice($data, $trainSize);
        $trainTanggal = array_slice($tanggal, 0, $trainSize);
        $testTanggal = array_slice($tanggal, $trainSize);

        // Tentukan alpha dan beta
        $optimasiOtomatis = $request->has('optimasi_otomatis');

        if ($optimasiOtomatis) {
            list($bestAlpha, $bestBeta) = $this->optimasiParameter($trainData, $testData);
        } else {
            $request->validate([
                'alpha' => 'required|numeric|min:0.01|max:0.99',
                'beta' => 'required|numeric|min:0.01|max:0.99',
            ]);
            $bestAlpha = (float) $request->alpha;
            $bestBeta = (float) $request->beta;
        }

        // Hitung jumlah hari prediksi berdasarkan range tanggal yang dipilih user
        $lastDate = Carbon::parse(end($tanggal));
        $prediksiMulai = Carbon::parse($request->tanggal_prediksi_mulai);
        $prediksiSelesai = Carbon::parse($request->tanggal_prediksi_selesai);

        // Validasi tanggal prediksi harus setelah tanggal terakhir data
        if ($prediksiMulai->lte($lastDate)) {
            return redirect()->route('holts.index')
                ->with('error', 'Tanggal prediksi mulai harus setelah tanggal terakhir data (' . $lastDate->format('d/m/Y') . ').');
        }

        // Hitung offset dan jumlah hari prediksi
        $offsetMulai = $lastDate->diffInDays($prediksiMulai);
        $offsetSelesai = $lastDate->diffInDays($prediksiSelesai);
        $jumlahHariPrediksi = $prediksiMulai->diffInDays($prediksiSelesai) + 1;

        // Jalankan Holt's Linear dengan detail perhitungan
        $hasilHolts = $this->holtsLinearDetail($trainData, $bestAlpha, $bestBeta, $testSize + $offsetSelesai);
        $detailPerhitungan = $hasilHolts['detail'];
        $forecast = $hasilHolts['forecast'];

        // Ambil forecast untuk data uji (evaluasi)
        $forecastTest = array_slice($forecast, 0, $testSize);

        // Hitung evaluasi
        $rmse = $this->hitungRMSE($testData, $forecastTest);
        $mape = $this->hitungMAPE($testData, $forecastTest);
        $mae = $this->hitungMAE($testData, $forecastTest);

        // Ambil forecast untuk range tanggal yang dipilih user
        $forecastRange = array_slice($forecast, $testSize + $offsetMulai - 1, $jumlahHariPrediksi);
        $prediksiHari = [];
        for ($i = 0; $i < $jumlahHariPrediksi; $i++) {
            $prediksiHari[] = [
                'tanggal' => $prediksiMulai->copy()->addDays($i)->format('Y-m-d'),
                'nilai' => max(0, round($forecastRange[$i] ?? 0, 2)),
            ];
        }

        // Simpan hasil ke database
        Prediksi::where('golongan_darah', $request->golongan_darah)
            ->where('komponen_darah_id', $request->komponen_darah_id)
            ->delete();

        foreach ($prediksiHari as $p) {
            Prediksi::create([
                'tanggal_prediksi' => Carbon::today(),
                'golongan_darah' => $request->golongan_darah,
                'komponen_darah_id' => $request->komponen_darah_id,
                'tanggal_target' => $p['tanggal'],
                'nilai_prediksi' => $p['nilai'],
                'alpha' => $bestAlpha,
                'beta' => $bestBeta,
                'rmse' => $rmse,
                'mape' => $mape,
                'mae' => $mae,
                'rasio_split' => $request->rasio_split,
            ]);
        }

        // Susun tabel rekapitulasi lengkap (data latih + data uji + prediksi)
        $rekapitulasi = [];

        // Bagian data latih
        foreach ($detailPerhitungan as $d) {
            $rekapitulasi[] = [
                'hari' => $d['i'],
                'tanggal' => $trainTanggal[$d['i'] - 1] ?? '-',
                'permintaan' => $d['yt'],
                'level' => round($d['lt'], 4),
                'trend' => round($d['tt'], 4),
                'forecast' => $d['ft'] !== null ? round($d['ft'], 4) : '-',
                'tipe' => 'latih',
            ];
        }

        // Bagian data uji
        for ($i = 0; $i < $testSize; $i++) {
            $rekapitulasi[] = [
                'hari' => $trainSize + $i + 1,
                'tanggal' => $testTanggal[$i],
                'permintaan' => $testData[$i],
                'level' => '-',
                'trend' => '-',
                'forecast' => round($forecastTest[$i], 4),
                'tipe' => 'uji',
            ];
        }

        // Bagian prediksi (range user)
        for ($i = 0; $i < $jumlahHariPrediksi; $i++) {
            $rekapitulasi[] = [
                'hari' => $totalData + $offsetMulai + $i,
                'tanggal' => $prediksiHari[$i]['tanggal'],
                'permintaan' => '?',
                'level' => '-',
                'trend' => '-',
                'forecast' => $prediksiHari[$i]['nilai'],
                'tipe' => 'prediksi',
            ];
        }

        // Simpan hasil ke session untuk ditampilkan
        $hasil = [
            'alpha' => $bestAlpha,
            'beta' => $bestBeta,
            'rmse' => round($rmse, 4),
            'mape' => round($mape, 4),
            'mae' => round($mae, 4),
            'rasio_split' => $request->rasio_split,
            'total_data' => $totalData,
            'train_size' => $trainSize,
            'test_size' => $testSize,
            'data_aktual' => $testData,
            'data_forecast' => $forecastTest,
            'test_tanggal' => $testTanggal,
            'prediksi_hari' => $prediksiHari,
            'golongan_darah' => $request->golongan_darah,
            'komponen_darah_id' => $request->komponen_darah_id,
            'rekapitulasi' => $rekapitulasi,
        ];

        session(['holts_hasil' => $hasil]);

        return redirect()->route('holts.index')
            ->with('success', 'Proses Holt\'s Linear berhasil. MAPE: ' . round($mape, 2) . '% | Prediksi: ' . $jumlahHariPrediksi . ' hari');
    }

    // Implementasi Holt's Linear dengan detail perhitungan setiap iterasi
    private function holtsLinearDetail(array $data, float $alpha, float $beta, int $forecastPeriods): array
    {
        $n = count($data);

        // Inisialisasi level dan trend
        $level = $data[0];
        $trend = $data[1] - $data[0];

        $detail = [];
        $forecasts = [];

        // Baris pertama (i=1): inisialisasi, belum ada forecast
        $detail[] = [
            'i' => 1,
            'yt' => $data[0],
            'lt' => $level,
            'tt' => $trend,
            'ft' => null,
        ];

        // Proses smoothing pada data training
        for ($t = 1; $t < $n; $t++) {
            $prevLevel = $level;
            $prevTrend = $trend;

            // Forecast untuk periode ini: Ft = Lt-1 + Tt-1
            $ft = $prevLevel + $prevTrend;

            // Update level: Lt = alpha * Yt + (1 - alpha) * (Lt-1 + Tt-1)
            $level = $alpha * $data[$t] + (1 - $alpha) * ($prevLevel + $prevTrend);

            // Update trend: Tt = beta * (Lt - Lt-1) + (1 - beta) * Tt-1
            $trend = $beta * ($level - $prevLevel) + (1 - $beta) * $prevTrend;

            $detail[] = [
                'i' => $t + 1,
                'yt' => $data[$t],
                'lt' => $level,
                'tt' => $trend,
                'ft' => $ft,
            ];
        }

        // Generate forecast: Ft+m = Lt + m * Tt
        for ($m = 1; $m <= $forecastPeriods; $m++) {
            $forecasts[] = $level + ($m * $trend);
        }

        return [
            'detail' => $detail,
            'forecast' => $forecasts,
        ];
    }

    // Optimasi parameter alpha dan beta menggunakan grid search (cari error terkecil)
    private function optimasiParameter(array $trainData, array $testData): array
    {
        $bestAlpha = 0.1;
        $bestBeta = 0.1;
        $bestError = PHP_FLOAT_MAX;
        $testSize = count($testData);

        for ($alpha = 0.1; $alpha <= 0.9; $alpha += 0.1) {
            for ($beta = 0.1; $beta <= 0.9; $beta += 0.1) {
                $hasilHolts = $this->holtsLinearDetail($trainData, $alpha, $beta, $testSize);
                $forecast = $hasilHolts['forecast'];
                $mse = $this->hitungMSE($testData, $forecast);

                if ($mse < $bestError) {
                    $bestError = $mse;
                    $bestAlpha = round($alpha, 1);
                    $bestBeta = round($beta, 1);
                }
            }
        }

        return [$bestAlpha, $bestBeta];
    }

    // Hitung Mean Squared Error
    private function hitungMSE(array $actual, array $forecast): float
    {
        $n = min(count($actual), count($forecast));
        if ($n == 0) return 0;

        $sum = 0;
        for ($i = 0; $i < $n; $i++) {
            $sum += pow($actual[$i] - $forecast[$i], 2);
        }

        return $sum / $n;
    }

    // Hitung Root Mean Squared Error
    private function hitungRMSE(array $actual, array $forecast): float
    {
        return sqrt($this->hitungMSE($actual, $forecast));
    }

    // Hitung Mean Absolute Percentage Error
    private function hitungMAPE(array $actual, array $forecast): float
    {
        $n = min(count($actual), count($forecast));
        if ($n == 0) return 0;

        $sum = 0;
        $count = 0;

        for ($i = 0; $i < $n; $i++) {
            if ($actual[$i] != 0) {
                $sum += abs(($actual[$i] - $forecast[$i]) / $actual[$i]);
                $count++;
            }
        }

        if ($count == 0) return 0;

        return ($sum / $count) * 100;
    }

    // Hitung Mean Absolute Error
    private function hitungMAE(array $actual, array $forecast): float
    {
        $n = min(count($actual), count($forecast));
        if ($n == 0) return 0;

        $sum = 0;
        for ($i = 0; $i < $n; $i++) {
            $sum += abs($actual[$i] - $forecast[$i]);
        }

        return $sum / $n;
    }
    
}
