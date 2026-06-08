<?php

namespace App\Http\Controllers;

use App\Models\PermintaanDarah;
use App\Models\RumahSakit;
use App\Models\StokDarah;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalRS = RumahSakit::count();
        $totalStok = StokDarah::sum('jumlah');
        $permintaanHariIni = PermintaanDarah::whereDate('tanggal', Carbon::today())->count();
        $totalPermintaan = PermintaanDarah::count();
        $stokDarah = StokDarah::with('komponenDarah')->get();
        $permintaanTerbaru = PermintaanDarah::with(['rumahSakit', 'komponenDarah'])
            ->orderBy('tanggal', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalRS',
            'totalStok',
            'permintaanHariIni',
            'totalPermintaan',
            'stokDarah',
            'permintaanTerbaru'
        ));
    }
}
