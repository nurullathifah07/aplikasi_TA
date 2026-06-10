@extends('layouts.admin_layout')

@section('title', 'Detail Stok Darah')

@section('content')

<h1 class="mt-4">Detail Stok Darah</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('stok-darah.index') }}">Stok Darah</a></li>
    <li class="breadcrumb-item active">Detail</li>
</ol>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-info-circle me-1"></i> Informasi Stok
            </div>
            <div class="card-body text-center">
                <h2 class="text-danger fw-bold">{{ $stokDarah->golongan_darah }}</h2>
                <p class="text-muted">{{ $stokDarah->komponenDarah->kode }} - {{ $stokDarah->komponenDarah->nama_lengkap }}</p>
                <h3 class="fw-bold">{{ $stokDarah->jumlah }} kantong</h3>
                @if ($stokDarah->jumlah >= 50)
                    <span class="badge bg-success fs-6">Aman</span>
                @elseif ($stokDarah->jumlah >= 20)
                    <span class="badge bg-warning fs-6">Menipis</span>
                @else
                    <span class="badge bg-danger fs-6">Kritis</span>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-history me-1"></i> Riwayat Perubahan
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="table-dark-red">
                        <tr>
                            <th>Waktu</th>
                            <th>Tipe</th>
                            <th>Jumlah</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
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
                            <td colspan="4" class="text-center text-muted">Belum ada riwayat</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<a href="{{ route('stok-darah.index') }}" class="btn btn-secondary">
    <i class="fas fa-arrow-left me-1"></i> Kembali
</a>

@endsection
