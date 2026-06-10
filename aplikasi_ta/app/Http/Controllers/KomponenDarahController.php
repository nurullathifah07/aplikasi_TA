<?php

namespace App\Http\Controllers;

use App\Models\KomponenDarah;
use Illuminate\Http\Request;

class KomponenDarahController extends Controller
{
    public function index()
    {
        $komponenDarah = KomponenDarah::all();
        return view('admin.komponen_darah.index', compact('komponenDarah'));
    }

    public function create()
    {
        return view('admin.komponen_darah.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:10|unique:komponen_darah,kode',
            'nama_lengkap' => 'required|string|max:255',
        ]);

        KomponenDarah::create($request->only('kode', 'nama_lengkap'));

        return redirect()->route('komponen-darah.index')
            ->with('success', 'Data komponen darah berhasil ditambahkan');
    }

    public function edit(KomponenDarah $komponenDarah)
    {
        return view('admin.komponen_darah.edit', compact('komponenDarah'));
    }

    public function update(Request $request, KomponenDarah $komponenDarah)
    {
        $request->validate([
            'kode' => 'required|string|max:10|unique:komponen_darah,kode,' . $komponenDarah->id,
            'nama_lengkap' => 'required|string|max:255',
        ]);

        $komponenDarah->update($request->only('kode', 'nama_lengkap'));

        return redirect()->route('komponen-darah.index')
            ->with('success', 'Data komponen darah berhasil diupdate');
    }

    public function destroy(KomponenDarah $komponenDarah)
    {
        $komponenDarah->delete();

        return redirect()->route('komponen-darah.index')
            ->with('success', 'Data komponen darah berhasil dihapus');
    }
}
