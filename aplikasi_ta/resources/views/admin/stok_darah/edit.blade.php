@extends('layouts.admin_layout')

@section('title', 'Edit Stok Darah')

@section('content')

<h1 class="mt-4">Edit Stok Darah</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('stok-darah.index') }}">Stok Darah</a></li>
    <li class="breadcrumb-item active">Edit</li>
</ol>

<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-edit me-1"></i> Form Edit Stok Darah
    </div>
    <div class="card-body">
        <form action="{{ route('stok-darah.update', $stokDarah) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Golongan Darah</label>
                    <input type="text" class="form-control" value="{{ $stokDarah->golongan_darah }}" disabled>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Komponen Darah</label>
                    <input type="text" class="form-control" value="{{ $stokDarah->komponenDarah->kode }} - {{ $stokDarah->komponenDarah->nama_lengkap }}" disabled>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="jumlah" class="form-label">Jumlah Stok (kantong)</label>
                    <input type="number" class="form-control @error('jumlah') is-invalid @enderror" id="jumlah" name="jumlah" value="{{ old('jumlah', $stokDarah->jumlah) }}" min="0" required>
                    @error('jumlah')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan Koreksi (opsional)</label>
                <input type="text" class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" value="{{ old('keterangan') }}" placeholder="Misal: Koreksi stok opname">
                @error('keterangan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Update
                </button>
                <a href="{{ route('stok-darah.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
