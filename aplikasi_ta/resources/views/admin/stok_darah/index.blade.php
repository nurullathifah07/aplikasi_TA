@extends('layouts.admin_layout')

@section('title', 'Stok Darah')

@section('content')

<h1 class="mt-4">Stok Darah</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Stok Darah</li>
</ol>

<!-- Stok Saat Ini -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-boxes-stacked me-1"></i> Stok Darah Saat Ini</span>
        <a href="{{ route('stok-darah.create') }}" class="btn btn-success btn-sm">
            <i class="fas fa-plus me-1"></i> Tambah Stok Masuk
        </a>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-hover">
            <thead class="table-dark-red">
                <tr>
                    <th>No</th>
                    <th>Golongan Darah</th>
                    <th>Komponen</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($stokDarah as $stok)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td><span class="badge bg-danger fs-6">{{ $stok->golongan_darah }}</span></td>
                    <td>{{ $stok->komponenDarah->kode }} - {{ $stok->komponenDarah->nama_lengkap }}</td>
                    <td>{{ $stok->jumlah }} kantong</td>
                    <td>
                        @if ($stok->jumlah >= 50)
                            <span class="badge bg-success">Aman</span>
                        @elseif ($stok->jumlah >= 20)
                            <span class="badge bg-warning">Menipis</span>
                        @else
                            <span class="badge bg-danger">Kritis</span>
                        @endif
                    </td>
                    <td class="text-nowrap">
                        <a href="{{ route('stok-darah.edit', $stok) }}" class="btn btn-warning btn-sm" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('stok-darah.destroy', $stok) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm btn-delete" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">Belum ada data stok</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Log Riwayat -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-history me-1"></i> Riwayat Perubahan Stok (20 Terakhir)
    </div>
    <div class="card-body">
        <table class="table table-bordered table-hover table-sm">
            <thead class="table-secondary">
                <tr>
                    <th>Waktu</th>
                    <th>Golongan</th>
                    <th>Komponen</th>
                    <th>Tipe</th>
                    <th>Jumlah</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                <tr>
                    <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $log->stokDarah->golongan_darah }}</td>
                    <td>{{ $log->stokDarah->komponenDarah->kode }}</td>
                    <td>
                        @if ($log->tipe == 'masuk')
                            <span class="badge bg-success">Masuk</span>
                        @else
                            <span class="badge bg-danger">Keluar</span>
                        @endif
                    </td>
                    <td>{{ $log->jumlah }} kantong</td>
                    <td>{{ $log->keterangan }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">Belum ada riwayat</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
