@extends('layouts.admin_layout')

@section('title', 'Tambah Stok Darah')

@section('content')

<h1 class="mt-4">Tambah Stok Darah Masuk</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('stok-darah.index') }}">Stok Darah</a></li>
    <li class="breadcrumb-item active">Tambah Stok</li>
</ol>

<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-plus me-1"></i> Form Tambah Stok Darah Masuk (Donor)
    </div>
    <div class="card-body">
        <form action="{{ route('stok-darah.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="golongan_darah" class="form-label">Golongan Darah</label>
                    <select class="form-select @error('golongan_darah') is-invalid @enderror" id="golongan_darah" name="golongan_darah" required>
                        <option value="">-- Pilih --</option>
                        <option value="A" {{ old('golongan_darah') == 'A' ? 'selected' : '' }}>A</option>
                        <option value="B" {{ old('golongan_darah') == 'B' ? 'selected' : '' }}>B</option>
                        <option value="AB" {{ old('golongan_darah') == 'AB' ? 'selected' : '' }}>AB</option>
                        <option value="O" {{ old('golongan_darah') == 'O' ? 'selected' : '' }}>O</option>
                    </select>
                    @error('golongan_darah')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="komponen_darah_id" class="form-label">Komponen Darah</label>
                    <select class="form-select @error('komponen_darah_id') is-invalid @enderror" id="komponen_darah_id" name="komponen_darah_id" required>
                        <option value="">-- Pilih --</option>
                        @foreach ($komponenDarah as $kd)
                            <option value="{{ $kd->id }}" {{ old('komponen_darah_id') == $kd->id ? 'selected' : '' }}>
                                {{ $kd->kode }} - {{ $kd->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                    @error('komponen_darah_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="jumlah" class="form-label">Jumlah (kantong)</label>
                    <input type="number" class="form-control @error('jumlah') is-invalid @enderror" id="jumlah" name="jumlah" value="{{ old('jumlah') }}" min="1" required>
                    @error('jumlah')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="keterangan" class="form-label">Keterangan (opsional)</label>
                    <input type="text" class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" value="{{ old('keterangan') }}" placeholder="Misal: Donor dari event X">
                    @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-1"></i> Simpan
                </button>
                <a href="{{ route('stok-darah.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
