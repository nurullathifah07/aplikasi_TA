@extends('layouts.admin_layout')

@section('title', 'Tambah Akun')

@section('content')

<h1 class="mt-4">Tambah Akun</h1>

<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item">
        <a href="{{ route('akun.index') }}">
            Data Akun
        </a>
    </li>

    <li class="breadcrumb-item active">
        Tambah Akun
    </li>
</ol>

<div class="card shadow-sm">

    <div class="card-header">
        <i class="fas fa-plus me-1"></i>
        Form Tambah Akun
    </div>

    <div class="card-body">

        <form action="{{ route('akun.store') }}"
              method="POST"
              enctype="multipart/form-data">

            @csrf

            <div class="mb-3">

                <label class="form-label">
                    Foto
                </label>

                <input type="file"
                       name="foto"
                       class="form-control">

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Nama
                </label>

                <input type="text"
                       name="nama"
                       class="form-control"
                       placeholder="Masukkan nama lengkap"
                       required>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    No. Telpon
                </label>

                <input type="text"
                       name="no_telpon"
                       class="form-control"
                       placeholder="Masukkan no. telpon">

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Username
                </label>

                <input type="text"
                       name="username"
                       class="form-control"
                       placeholder="Masukkan username"
                       required>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Email
                </label>

                <input type="email"
                       name="email"
                       class="form-control"
                       placeholder="Masukkan email"
                       required>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Password
                </label>

                <input type="password"
                       name="password"
                       class="form-control"
                       placeholder="Masukkan password"
                       required>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Role
                </label>

                <input type="text"
                        name="role"
                        class="form-control"
                        value="admin"
                        disabled>
                <input type="hidden" name="role" value="admin">

            </div>

            <div class="d-flex gap-2">

                <button type="submit"
                        class="btn btn-primary">

                    <i class="fas fa-save"></i>
                    Simpan

                </button>

                <a href="{{ route('akun.index') }}"
                   class="btn btn-secondary">

                    Kembali

                </a>

            </div>

        </form>

    </div>

</div>

@endsection
