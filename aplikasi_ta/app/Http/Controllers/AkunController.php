<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class AkunController extends Controller
{
    public function index()
    {
        $akun = Akun::all();
        return view('admin.akun.index', compact('akun'));
    }

    public function create()
    {
        return view('admin.akun.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'nama' => 'required|string|max:255',
            'no_telpon' => 'nullable|string|max:20',
            'username' => 'required|unique:akun,username',
            'email' => 'required|email|unique:akun,email',
            'password' => 'required',
            'role' => 'required'
        ]);

        $foto = null;

        if ($request->hasFile('foto')) {
            $foto = $this->uploadFoto($request->file('foto'));
        }

        Akun::create([
            'foto' => $foto,
            'nama' => $request->nama,
            'no_telpon' => $request->no_telpon,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        return redirect()->route('akun.index')
            ->with('success', 'Data akun berhasil ditambahkan');
    }

    public function show(Akun $akun)
    {
        //
    }

    public function edit(Akun $akun)
    {
        return view('admin.akun.edit', compact('akun'));
    }

    public function update(Request $request, Akun $akun)
    {
        $request->validate([
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'nama' => 'required|string|max:255',
            'no_telpon' => 'nullable|string|max:20',
            'username' => 'required|unique:akun,username,' . $akun->id,
            'email' => 'required|email|unique:akun,email,' . $akun->id,
            'password' => 'nullable|min:6',
        ]);

        $foto = $akun->foto;

        // Hapus foto lama jika upload foto baru
        if ($request->hasFile('foto')) {
            $this->hapusFoto($akun->foto);
            $foto = $this->uploadFoto($request->file('foto'));
        }

        $data = [
            'foto' => $foto,
            'nama' => $request->nama,
            'no_telpon' => $request->no_telpon,
            'username' => $request->username,
            'email' => $request->email,
            'role' => $request->role
        ];

        // Update password hanya jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $akun->update($data);

        return redirect()->route('akun.index')
            ->with('success', 'Data akun berhasil diupdate');
    }

    public function destroy(Akun $akun)
    {
        $this->hapusFoto($akun->foto);
        $akun->delete();

        return redirect()->route('akun.index')
            ->with('success', 'Data akun berhasil dihapus');
    }

    // Upload foto ke folder public/uploads/foto_akun menggunakan $_SERVER['DOCUMENT_ROOT'] sebagai fallback
    private function uploadFoto($file): string
    {
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $relativePath = 'uploads/foto_akun';
        $uploadPath = $this->getPublicPath($relativePath);

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $file->move($uploadPath, $filename);

        return $relativePath . '/' . $filename;
    }

    // Hapus foto dari folder public
    private function hapusFoto(?string $foto): void
    {
        if ($foto) {
            $fullPath = $this->getPublicPath($foto);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
    }

    // Dapatkan path public yang benar (support hosting dan local)
    private function getPublicPath(string $path = ''): string
    {
        // Di hosting, DOCUMENT_ROOT mengarah ke public_html (folder yang diakses browser)
        if (isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT'] !== '') {
            $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '/');
            // Jika DOCUMENT_ROOT berbeda dari public_path, gunakan DOCUMENT_ROOT (hosting)
            if ($docRoot !== rtrim(public_path(), '/')) {
                return $docRoot . '/' . $path;
            }
        }

        return public_path($path);
    }
}
