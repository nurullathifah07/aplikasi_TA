<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AkunController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $akun = Akun::all();

        return view('admin.akun.index', compact('akun'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.akun.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'foto' => 'nullable|image|mimes:jpg,jpeg,png',
            'username' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'role' => 'required'
        ]);

        $foto = null;

        if ($request->hasFile('foto')) {
            $foto = $request->file('foto')->store('foto_akun', 'public');
        }

        Akun::create([
            'foto' => $foto,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        return redirect()->route('akun.index')
            ->with('success', 'Data akun berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Akun $akun)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Akun $akun)
    {
        return view('admin.akun.edit', compact('akun'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Akun $akun)
    {
        $foto = $akun->foto;

        if ($request->hasFile('foto')) {
            $foto = $request->file('foto')->store('foto_akun', 'public');
        }

        $akun->update([
            'foto' => $foto,
            'username' => $request->username,
            'email' => $request->email,
            'role' => $request->role
        ]);

        return redirect()->route('akun.index')
            ->with('success', 'Data akun berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Akun $akun)
    {
        $akun->delete();

        return redirect()->route('akun.index')
            ->with('success', 'Data akun berhasil dihapus');
    }
}
