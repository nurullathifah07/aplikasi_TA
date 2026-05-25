@extends('layouts.admin_layout')

@section('title', 'Data Akun')

@section('content')

<h1 class="mt-4">Data Akun</h1>

<ol class="breadcrumb mb-4">

    <li class="breadcrumb-item">
        <a href="#">Dashboard</a>
    </li>

    <li class="breadcrumb-item active">
        Data Akun
    </li>

</ol>

<div class="card mb-4 shadow-sm">

    <div class="card-header d-flex justify-content-between align-items-center">

        <div>
            <i class="fas fa-users me-1"></i>
            Data Akun
        </div>

        <a href="{{ route('akun.create') }}"
           class="btn btn-primary btn-sm">

            <i class="fas fa-plus"></i>
            Tambah Akun

        </a>

    </div>

    <div class="card-body">

        @if(session('success'))

            <div class="alert alert-success">
                {{ session('success') }}
            </div>

        @endif

        <table id="datatablesSimple"
               class="table table-bordered table-hover text-center align-middle">

            <thead class="table-light">

                <tr>

                    <th>No</th>
                    <th>Foto</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Role</th>
                    <th>Aksi</th>

                </tr>

            </thead>

            <tbody>

                @forelse ($akun as $item)

                    <tr>

                        <td>
                            {{ $loop->iteration }}
                        </td>

                        <td>

                            @if ($item->foto)

                                <img src="{{ asset('storage/' . $item->foto) }}"
                                     class="rounded-circle border"
                                     width="50"
                                     height="50"
                                     style="object-fit: cover;">

                            @else

                                <img src="https://via.placeholder.com/50"
                                     class="rounded-circle border"
                                     width="50"
                                     height="50">

                            @endif

                        </td>

                        <td>
                            {{ $item->username }}
                        </td>

                        <td>
                            {{ $item->email }}
                        </td>

                        <td>
                            ********
                        </td>

                        <td>

                            <span class="badge bg-primary px-3 py-2">

                                {{ ucfirst($item->role) }}

                            </span>

                        </td>

                        <td>

                            <div class="d-flex justify-content-center gap-1">

                                <a href="{{ route('akun.edit', $item->id) }}"
                                   class="btn btn-warning btn-sm">

                                    <i class="fas fa-edit"></i>

                                </a>

                                <form action="{{ route('akun.destroy', $item->id) }}"
                                      method="POST">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Yakin ingin menghapus data ini?')">

                                        <i class="fas fa-trash"></i>

                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="7">
                            Data akun belum tersedia
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection
