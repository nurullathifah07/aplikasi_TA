@extends('layouts.publik_layout')

@section('title', 'Stok Darah Saat Ini')

@section('content')

<div class="hero-section text-center">
    <div class="container">
        <h1 class="fw-bold"><i class="fas fa-boxes-stacked me-2"></i> Stok Darah Saat Ini</h1>
        <p class="lead">Informasi ketersediaan stok darah di PMI Kabupaten Tanah Laut</p>
    </div>
</div>

<div class="container mt-4">
    @if ($stokDarah->isEmpty())
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle me-1"></i>
            Belum ada data stok darah yang tersedia.
        </div>
    @else
        <div class="row">
            @foreach ($stokDarah as $stok)
            <div class="col-md-3 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h2 class="text-danger fw-bold">{{ $stok->golongan_darah }}</h2>
                        <p class="text-muted mb-1">{{ $stok->komponenDarah->kode }}</p>
                        <h3 class="fw-bold">{{ $stok->jumlah }}</h3>
                        <p class="text-muted">kantong</p>
                        @if ($stok->jumlah >= 50)
                            <span class="badge bg-success">Aman</span>
                        @elseif ($stok->jumlah >= 20)
                            <span class="badge bg-warning">Menipis</span>
                        @else
                            <span class="badge bg-danger">Kritis</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

@endsection
