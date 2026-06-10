@extends('layouts.admin_layout')

@section('title', 'Tambah Komponen Darah')

@section('content')

<h1 class="mt-4">Tambah Komponen Darah</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('komponen-darah.index') }}">Komponen Darah</a></li>
    <li class="breadcrumb-item active">Tambah</li>
</ol>

<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-plus me-1"></i> Form Tambah Komponen Darah
    </div>
    <div class="card-body">
        <form action="{{ route('komponen-darah.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="kode" class="form-label">Kode Komponen</label>
                <input type="text" class="form-control @error('kode') is-invalid @enderror" id="kode" name="kode" value="{{ old('kode') }}" placeholder="Contoh: WB, PRC" required>
                @error('kode')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" placeholder="Contoh: Whole Blood" required>
                @error('nama_lengkap')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Simpan
                </button>
                <a href="{{ route('komponen-darah.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
