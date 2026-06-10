<?php

namespace App\Http\Controllers;

use App\Models\Prediksi;
use Illuminate\Http\Request;

class PrediksiController extends Controller
{
    public function index()
    {
        $prediksi = Prediksi::with('komponenDarah')
            ->orderBy('tanggal_prediksi', 'desc')
            ->orderBy('tanggal_target', 'asc')
            ->get();

        return view('admin.prediksi.index', compact('prediksi'));
    }

    // Generate prediksi 7 hari ke depan — redirect ke Holt's Linear karena proses dimulai dari sana
    public function generate(Request $request)
    {
        return redirect()->route('holts.index')
            ->with('error', 'Untuk generate prediksi baru, silakan jalankan proses Holt\'s Linear terlebih dahulu.');
    }
}
