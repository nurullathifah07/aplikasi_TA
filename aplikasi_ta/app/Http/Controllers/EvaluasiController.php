<?php

namespace App\Http\Controllers;

use App\Models\Prediksi;

class EvaluasiController extends Controller
{
    public function index()
    {
        // Ambil evaluasi unik per golongan darah & komponen (berdasarkan tanggal prediksi terbaru)
        $evaluasi = Prediksi::with('komponenDarah')
            ->selectRaw('golongan_darah, komponen_darah_id, alpha, beta, rmse, mape, mae, rasio_split, tanggal_prediksi')
            ->groupBy('golongan_darah', 'komponen_darah_id', 'alpha', 'beta', 'rmse', 'mape', 'mae', 'rasio_split', 'tanggal_prediksi')
            ->orderBy('tanggal_prediksi', 'desc')
            ->get();

        return view('admin.evaluasi.index', compact('evaluasi'));
    }
}
