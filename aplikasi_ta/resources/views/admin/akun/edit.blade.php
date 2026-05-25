@extends('layouts.admin_layout')

@section('title', 'Edit Akun')

@section('content')

<h1 class="mt-4">Edit Akun</h1>

<ol class="breadcrumb mb-4">

    <li class="breadcrumb-item">
        <a href="{{ route('akun.index') }}">
            Data Akun
        </a>
    </li>

    <li class="breadcrumb-item active">
        Edit Akun
    </li>

</ol>

<div class="card shadow-sm">

    <div class="card-header">

        <i class="fas fa-edit me-1"></i>
        Form Edit Akun

    </div>

    <div class="card-body">

        <form action="{{ route('akun.update', $akun->id) }}"
              method="POST"
              enctype="multipart/form-data">

            @csrf
            @method('PUT')

            <div class="mb-3">

                <label class="form-label">
                    Foto
                </label>

                <br>

                @if ($akun->foto)

                    <img src="{{ asset('storage/' . $akun->foto) }}"
                         width="80"
                         height="80"
                         class="rounded-circle mb-3 border"
                         style="object-fit: cover;">

                @else

                    <img src="https://via.placeholder.com/80"
                         width="80"
                         height="80"
                         class="rounded-circle mb-3 border">

                @endif

                <input type="file"
                       name="foto"
                       class="form-control">

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Username
                </label>

                <input type="text"
                       name="username"
                       class="form-control"
                       value="{{ $akun->username }}"
                       required>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Email
                </label>

                <input type="email"
                       name="email"
                       class="form-control"
                       value="{{ $akun->email }}"
                       required>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Password Baru
                </label>

                <input type="password"
                       name="password"
                       class="form-control"
                       placeholder="Kosongkan jika tidak ingin mengganti password">

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Role
                </label>

                <input type="text"
                       name="role"
                       class="form-control"
                       value="{{ $akun->role }}">

            </div>

            <div class="d-flex gap-2">

                <button type="submit"
                        class="btn btn-primary">

                    <i class="fas fa-save"></i>
                    Update

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
