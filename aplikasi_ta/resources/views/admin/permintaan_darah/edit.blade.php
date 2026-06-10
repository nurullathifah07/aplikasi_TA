@extends('layouts.admin_layout')

@section('title', 'Edit Permintaan Darah')

@section('content')

<h1 class="mt-4">Edit Permintaan Darah</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('permintaan-darah.index') }}">Permintaan Darah</a></li>
    <li class="breadcrumb-item active">Edit</li>
</ol>

<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-edit me-1"></i> Form Edit Permintaan Darah
    </div>
    <div class="card-body">
        <form action="{{ route('permintaan-darah.update', $permintaanDarah) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="rumah_sakit_id" class="form-label">Rumah Sakit</label>
                    <select class="form-select @error('rumah_sakit_id') is-invalid @enderror" id="rumah_sakit_id" name="rumah_sakit_id" required>
                        <option value="">-- Pilih Rumah Sakit --</option>
                        @foreach ($rumahSakit as $rs)
                            <option value="{{ $rs->id }}" {{ old('rumah_sakit_id', $permintaanDarah->rumah_sakit_id) == $rs->id ? 'selected' : '' }}>
                                {{ $rs->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('rumah_sakit_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="tanggal" class="form-label">Tanggal Permintaan</label>
                    <input type="date" class="form-control @error('tanggal') is-invalid @enderror" id="tanggal" name="tanggal" value="{{ old('tanggal', $permintaanDarah->tanggal->format('Y-m-d')) }}" required>
                    @error('tanggal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="golongan_darah" class="form-label">Golongan Darah</label>
                    <select class="form-select @error('golongan_darah') is-invalid @enderror" id="golongan_darah" name="golongan_darah" required>
                        <option value="A" {{ old('golongan_darah', $permintaanDarah->golongan_darah) == 'A' ? 'selected' : '' }}>A</option>
                        <option value="B" {{ old('golongan_darah', $permintaanDarah->golongan_darah) == 'B' ? 'selected' : '' }}>B</option>
                        <option value="AB" {{ old('golongan_darah', $permintaanDarah->golongan_darah) == 'AB' ? 'selected' : '' }}>AB</option>
                        <option value="O" {{ old('golongan_darah', $permintaanDarah->golongan_darah) == 'O' ? 'selected' : '' }}>O</option>
                    </select>
                    @error('golongan_darah')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label for="komponen_darah_id" class="form-label">Komponen Darah</label>
                    <select class="form-select @error('komponen_darah_id') is-invalid @enderror" id="komponen_darah_id" name="komponen_darah_id" required>
                        @foreach ($komponenDarah as $kd)
                            <option value="{{ $kd->id }}" {{ old('komponen_darah_id', $permintaanDarah->komponen_darah_id) == $kd->id ? 'selected' : '' }}>
                                {{ $kd->kode }} - {{ $kd->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                    @error('komponen_darah_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label for="jumlah" class="form-label">Jumlah (kantong)</label>
                    <input type="number" class="form-control @error('jumlah') is-invalid @enderror" id="jumlah" name="jumlah" value="{{ old('jumlah', $permintaanDarah->jumlah) }}" min="1" required>
                    @error('jumlah')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="pending" {{ old('status', $permintaanDarah->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="terpenuhi" {{ old('status', $permintaanDarah->status) == 'terpenuhi' ? 'selected' : '' }}>Terpenuhi</option>
                        <option value="ditolak" {{ old('status', $permintaanDarah->status) == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Update
                </button>
                <a href="{{ route('permintaan-darah.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
