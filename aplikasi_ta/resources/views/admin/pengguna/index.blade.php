@extends('layouts.admin_layout')

@section('title', 'Data Pengguna')

@section('content')

<h1 class="mt-4">Data Pengguna</h1>

<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
    <li class="breadcrumb-item active">Data Pengguna</li>
</ol>

<div class="card mb-4 shadow-sm">

    <div class="card-header d-flex justify-content-between align-items-center">

        <div>
            <i class="fas fa-users me-1"></i>
            Data Pengguna
        </div>

        <a href="#" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Tambah Pengguna
        </a>

    </div>

    <div class="card-body">

        <table id="datatablesSimple" class="table table-bordered table-hover text-center align-middle">

            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Foto</th>
                    <th>Nama Pengguna</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>No HP</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

                <tr class="text-center">
                    <td>1</td>

                    <td>
                        <img src="https://via.placeholder.com/50"
                             class="rounded-circle"
                             width="50"
                             height="50">
                    </td>

                    <td>Admin PMI</td>
                    <td>admin</td>

                    <td>
                        <span class="badge bg-primary">
                            Admin
                        </span>
                    </td>

                    <td>
                        084538275642
                    </td>

                    <td>

                        <a href="#" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>

                        <a href="#" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i>
                        </a>

                    </td>
                </tr>

                <tr class="text-center">
                    <td>2</td>

                    <td>
                        <img src="https://via.placeholder.com/50"
                             class="rounded-circle"
                             width="50"
                             height="50">
                    </td>

                    <td>Pimpinan PMI</td>
                    <td>pimpinan</td>

                    <td>
                        <span class="badge bg-dark">
                            Pimpinan
                        </span>
                    </td>

                    <td>
                        085634986712
                    </td>

                    <td>

                        <a href="#" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>

                        <a href="#" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i>
                        </a>

                    </td>
                </tr>

            </tbody>

        </table>

    </div>
</div>

@endsection
