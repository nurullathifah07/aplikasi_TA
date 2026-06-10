<?php

namespace App\Http\Controllers;

use App\Exports\TemplatePermintaanDarahExport;
use App\Imports\PermintaanDarahImport;
use App\Models\KomponenDarah;
use App\Models\PermintaanDarah;
use App\Models\RumahSakit;
use App\Models\StokDarah;
use App\Models\StokDarahLog;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class PermintaanDarahController extends Controller
{
    public function index()
    {
        return view('admin.permintaan_darah.index');
    }

    // Server-side DataTables endpoint
    public function data()
    {
        $query = PermintaanDarah::with(['rumahSakit', 'komponenDarah'])
            ->orderBy('tanggal', 'desc');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('tanggal_formatted', function ($row) {
                return $row->tanggal->format('d/m/Y');
            })
            ->addColumn('rumah_sakit_nama', function ($row) {
                return $row->rumahSakit->nama;
            })
            ->addColumn('komponen_kode', function ($row) {
                return $row->komponenDarah->kode;
            })
            ->addColumn('status_badge', function ($row) {
                if ($row->status == 'terpenuhi') {
                    return '<span class="badge bg-success">Terpenuhi</span>';
                } elseif ($row->status == 'pending') {
                    return '<span class="badge bg-warning">Pending</span>';
                }
                return '<span class="badge bg-danger">Ditolak</span>';
            })
            ->addColumn('aksi', function ($row) {
                $html = '';
                if ($row->status == 'pending') {
                    $html .= '<button class="btn btn-success btn-sm btn-status" data-id="' . $row->id . '" data-status="terpenuhi" title="Setujui"><i class="fas fa-check"></i></button> ';
                    $html .= '<button class="btn btn-secondary btn-sm btn-status" data-id="' . $row->id . '" data-status="ditolak" title="Tolak"><i class="fas fa-times"></i></button> ';
                }
                $html .= '<a href="' . route('permintaan-darah.edit', $row->id) . '" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a> ';
                $html .= '<form action="' . route('permintaan-darah.destroy', $row->id) . '" method="POST" class="d-inline">';
                $html .= csrf_field() . method_field('DELETE');
                $html .= '<button type="submit" class="btn btn-danger btn-sm btn-delete" title="Hapus"><i class="fas fa-trash"></i></button>';
                $html .= '</form>';
                return $html;
            })
            ->rawColumns(['status_badge', 'aksi'])
            ->make(true);
    }

    public function create()
    {
        $rumahSakit = RumahSakit::all();
        $komponenDarah = KomponenDarah::all();

        return view('admin.permintaan_darah.create', compact('rumahSakit', 'komponenDarah'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'rumah_sakit_id' => 'required|exists:rumah_sakit,id',
            'tanggal' => 'required|date',
            'golongan_darah' => 'required|in:A,B,AB,O',
            'komponen_darah_id' => 'required|exists:komponen_darah,id',
            'jumlah' => 'required|integer|min:1',
        ]);

        PermintaanDarah::create($request->only(
            'rumah_sakit_id', 'tanggal', 'golongan_darah', 'komponen_darah_id', 'jumlah'
        ));

        return redirect()->route('permintaan-darah.index')
            ->with('success', 'Permintaan darah berhasil ditambahkan');
    }

    public function edit(PermintaanDarah $permintaanDarah)
    {
        $rumahSakit = RumahSakit::all();
        $komponenDarah = KomponenDarah::all();

        return view('admin.permintaan_darah.edit', compact('permintaanDarah', 'rumahSakit', 'komponenDarah'));
    }

    // Saat update, cek perubahan status untuk sinkronisasi stok darah secara otomatis
    public function update(Request $request, PermintaanDarah $permintaanDarah)
    {
        $request->validate([
            'rumah_sakit_id' => 'required|exists:rumah_sakit,id',
            'tanggal' => 'required|date',
            'golongan_darah' => 'required|in:A,B,AB,O',
            'komponen_darah_id' => 'required|exists:komponen_darah,id',
            'jumlah' => 'required|integer|min:1',
            'status' => 'required|in:pending,terpenuhi,ditolak',
        ]);

        $oldStatus = $permintaanDarah->status;
        $newStatus = $request->status;

        $permintaanDarah->update($request->only(
            'rumah_sakit_id', 'tanggal', 'golongan_darah', 'komponen_darah_id', 'jumlah', 'status'
        ));

        // Jika status berubah menjadi "terpenuhi", kurangi stok otomatis
        if ($oldStatus !== 'terpenuhi' && $newStatus === 'terpenuhi') {
            $this->kurangiStok($permintaanDarah);
        }

        // Jika status berubah dari "terpenuhi" ke lainnya, kembalikan stok
        if ($oldStatus === 'terpenuhi' && $newStatus !== 'terpenuhi') {
            $this->kembalikanStok($permintaanDarah);
        }

        return redirect()->route('permintaan-darah.index')
            ->with('success', 'Permintaan darah berhasil diupdate');
    }

    public function destroy(PermintaanDarah $permintaanDarah)
    {
        // Jika permintaan yang dihapus berstatus terpenuhi, kembalikan stok dulu
        if ($permintaanDarah->status === 'terpenuhi') {
            $this->kembalikanStok($permintaanDarah);
        }

        $permintaanDarah->delete();

        return redirect()->route('permintaan-darah.index')
            ->with('success', 'Permintaan darah berhasil dihapus');
    }

    // Update status permintaan via AJAX (centang = terpenuhi, x = ditolak)
    public function updateStatus(Request $request, PermintaanDarah $permintaanDarah)
    {
        $request->validate([
            'status' => 'required|in:terpenuhi,ditolak',
        ]);

        $oldStatus = $permintaanDarah->status;
        $newStatus = $request->status;

        $permintaanDarah->update(['status' => $newStatus]);

        // Jika status berubah menjadi "terpenuhi", kurangi stok otomatis
        if ($oldStatus !== 'terpenuhi' && $newStatus === 'terpenuhi') {
            $this->kurangiStok($permintaanDarah);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diubah menjadi ' . $newStatus,
            'status' => $newStatus,
        ]);
    }

    // Mengurangi stok darah dan mencatat log keluar saat permintaan dipenuhi
    private function kurangiStok(PermintaanDarah $permintaan)
    {
        $stok = StokDarah::where('golongan_darah', $permintaan->golongan_darah)
            ->where('komponen_darah_id', $permintaan->komponen_darah_id)
            ->first();

        if ($stok) {
            // Pastikan stok tidak menjadi negatif
            $jumlahKurang = min($permintaan->jumlah, $stok->jumlah);
            $stok->decrement('jumlah', $jumlahKurang);

            StokDarahLog::create([
                'stok_darah_id' => $stok->id,
                'tipe' => 'keluar',
                'jumlah' => $jumlahKurang,
                'keterangan' => 'Permintaan dari ' . $permintaan->rumahSakit->nama,
                'permintaan_darah_id' => $permintaan->id,
            ]);
        }
    }

    // Mengembalikan stok darah dan mencatat log masuk saat status dibatalkan dari terpenuhi
    private function kembalikanStok(PermintaanDarah $permintaan)
    {
        $stok = StokDarah::where('golongan_darah', $permintaan->golongan_darah)
            ->where('komponen_darah_id', $permintaan->komponen_darah_id)
            ->first();

        if ($stok) {
            $stok->increment('jumlah', $permintaan->jumlah);

            StokDarahLog::create([
                'stok_darah_id' => $stok->id,
                'tipe' => 'masuk',
                'jumlah' => $permintaan->jumlah,
                'keterangan' => 'Pembatalan permintaan dari ' . $permintaan->rumahSakit->nama,
                'permintaan_darah_id' => $permintaan->id,
            ]);
        }
    }

    // Import data permintaan darah dari file Excel (.xlsx)
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120',
        ]);

        $import = new PermintaanDarahImport();
        Excel::import($import, $request->file('file'));

        $errors = $import->getErrors();
        $imported = $import->getImportedCount();

        if (count($errors) > 0 && $imported == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Import gagal. Tidak ada data yang berhasil diimport.',
                'errors' => $errors,
            ]);
        }

        $message = "Berhasil import {$imported} data.";
        if (count($errors) > 0) {
            $message .= " " . count($errors) . " baris gagal.";
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'errors' => $errors,
            'imported' => $imported,
        ]);
    }

    // Download template Excel untuk import
    public function downloadTemplate()
    {
        return Excel::download(new TemplatePermintaanDarahExport(), 'template_permintaan_darah.xlsx');
    }
}
