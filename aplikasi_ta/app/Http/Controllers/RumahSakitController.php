<?php

namespace App\Http\Controllers;

use App\Models\RumahSakit;
use Illuminate\Http\Request;

class RumahSakitController extends Controller
{
    public function index()
    {
        $rumahSakit = RumahSakit::all();
        return view('admin.rumah_sakit.index', compact('rumahSakit'));
    }

    public function create()
    {
        return view('admin.rumah_sakit.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
        ]);

        RumahSakit::create($request->only('nama', 'alamat'));

        return redirect()->route('rumah-sakit.index')
            ->with('success', 'Data rumah sakit berhasil ditambahkan');
    }

    public function edit(RumahSakit $rumahSakit)
    {
        return view('admin.rumah_sakit.edit', compact('rumahSakit'));
    }

    public function update(Request $request, RumahSakit $rumahSakit)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
        ]);

        $rumahSakit->update($request->only('nama', 'alamat'));

        return redirect()->route('rumah-sakit.index')
            ->with('success', 'Data rumah sakit berhasil diupdate');
    }

    public function destroy(RumahSakit $rumahSakit)
    {
        $rumahSakit->delete();

        return redirect()->route('rumah-sakit.index')
            ->with('success', 'Data rumah sakit berhasil dihapus');
    }
}
