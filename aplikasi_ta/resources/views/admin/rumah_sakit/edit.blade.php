@extends('layouts.admin_layout')

@section('title', 'Edit Rumah Sakit')

@section('content')

<h1 class="mt-4">Edit Rumah Sakit</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('rumah-sakit.index') }}">Rumah Sakit</a></li>
    <li class="breadcrumb-item active">Edit</li>
</ol>

<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-edit me-1"></i> Form Edit Rumah Sakit
    </div>
    <div class="card-body">
        <form action="{{ route('rumah-sakit.update', $rumahSakit) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="nama" class="form-label">Nama Rumah Sakit</label>
                <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $rumahSakit->nama) }}" required>
                @error('nama')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3" required>{{ old('alamat', $rumahSakit->alamat) }}</textarea>
                @error('alamat')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Update
                </button>
                <a href="{{ route('rumah-sakit.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
