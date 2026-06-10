<?php

namespace App\Http\Controllers;

use App\Models\KomponenDarah;
use App\Models\StokDarah;
use App\Models\StokDarahLog;
use Illuminate\Http\Request;

class StokDarahController extends Controller
{
    public function index()
    {
        $stokDarah = StokDarah::with('komponenDarah')->get();
        $logs = StokDarahLog::with(['stokDarah.komponenDarah', 'permintaanDarah'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('admin.stok_darah.index', compact('stokDarah', 'logs'));
    }

    public function create()
    {
        $komponenDarah = KomponenDarah::all();
        return view('admin.stok_darah.create', compact('komponenDarah'));
    }

    // Menambah stok darah masuk (dari donor), menggunakan firstOrCreate agar stok per golongan+komponen unik
    public function store(Request $request)
    {
        $request->validate([
            'golongan_darah' => 'required|in:A,B,AB,O',
            'komponen_darah_id' => 'required|exists:komponen_darah,id',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255',
        ]);

        // Cari stok yang sudah ada atau buat baru dengan jumlah awal 0
        $stok = StokDarah::firstOrCreate(
            [
                'golongan_darah' => $request->golongan_darah,
                'komponen_darah_id' => $request->komponen_darah_id,
            ],
            ['jumlah' => 0]
        );

        // Tambah jumlah stok
        $stok->increment('jumlah', $request->jumlah);

        // Catat log stok masuk untuk riwayat
        StokDarahLog::create([
            'stok_darah_id' => $stok->id,
            'tipe' => 'masuk',
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan ?? 'Penambahan stok darah',
        ]);

        return redirect()->route('stok-darah.index')
            ->with('success', 'Stok darah berhasil ditambahkan');
    }

    public function show(StokDarah $stokDarah)
    {
        $logs = StokDarahLog::where('stok_darah_id', $stokDarah->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.stok_darah.show', compact('stokDarah', 'logs'));
    }

    public function edit(StokDarah $stokDarah)
    {
        return view('admin.stok_darah.edit', compact('stokDarah'));
    }

    public function update(Request $request, StokDarah $stokDarah)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $jumlahLama = $stokDarah->jumlah;
        $jumlahBaru = (int) $request->jumlah;
        $selisih = $jumlahBaru - $jumlahLama;

        $stokDarah->update([
            'jumlah' => $jumlahBaru,
        ]);

        // Catat koreksi stok manual agar perubahan stok tetap terlacak
        if ($selisih !== 0) {
            StokDarahLog::create([
                'stok_darah_id' => $stokDarah->id,
                'tipe' => $selisih > 0 ? 'masuk' : 'keluar',
                'jumlah' => abs($selisih),
                'keterangan' => $request->keterangan ?? 'Koreksi manual stok darah',
            ]);
        }

        return redirect()->route('stok-darah.index')
            ->with('success', 'Stok darah berhasil diupdate');
    }

    public function destroy(StokDarah $stokDarah)
    {
        $stokDarah->delete();

        return redirect()->route('stok-darah.index')
            ->with('success', 'Stok darah berhasil dihapus');
    }
}
