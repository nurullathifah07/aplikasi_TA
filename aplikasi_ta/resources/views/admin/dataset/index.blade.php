@extends('layouts.admin_layout')

@section('title', 'Dataset')

@section('content')

<h1 class="mt-4">Dataset</h1>

<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item active">Dataset</li>
</ol>

<div class="card shadow-sm mb-4">

    <!-- HEADER -->
    <div class="card-header d-flex justify-content-between align-items-center">

        <div>
            <i class="fas fa-database me-1"></i>
            Data Dataset
        </div>

        <div class="d-flex gap-2">

            <!-- BUTTON IMPORT -->
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fas fa-file-import"></i>
                Import Data
            </button>

            <!-- BUTTON TAMBAH -->
            <a href="#" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i>
                Tambah Data
            </a>

        </div>

    </div>

    <!-- BODY -->
    <div class="card-body">

        <table id="datatablesSimple"
               class="table table-bordered table-hover table-striped">

            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Golongan Darah</th>
                    <th>Komponen</th>
                    <th>Permintaan</th>
                    <th>Kembali</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

                <tr>
                    <td>1</td>
                    <td>01-06-2023</td>
                    <td>A</td>
                    <td>WB</td>
                    <td>12</td>
                    <td>2</td>

                    <td>

                        <a href="#"
                           class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>

                        <a href="#"
                           class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i>
                        </a>

                    </td>
                </tr>

                <tr>
                    <td>2</td>
                    <td>01-06-2023</td>
                    <td>B</td>
                    <td>PRC</td>
                    <td>8</td>
                    <td>1</td>

                    <td>

                        <a href="#"
                           class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>

                        <a href="#"
                           class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i>
                        </a>

                    </td>
                </tr>

            </tbody>

        </table>

    </div>

</div>


<!-- MODAL IMPORT -->
<div class="modal fade" id="importModal" tabindex="-1">

    <div class="modal-dialog">

        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header">

                <h5 class="modal-title">
                    Import Dataset
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>

            </div>

            <!-- BODY -->
            <div class="modal-body">

                <form action="#" method="POST" enctype="multipart/form-data">

                    @csrf

                    <div class="mb-3">

                        <label class="form-label">
                            Upload File Excel / CSV
                        </label>

                        <input type="file"
                               class="form-control"
                               accept=".xlsx,.xls,.csv">

                    </div>

                    <div class="alert alert-info">

                        <i class="fas fa-info-circle"></i>

                        Format file yang didukung:
                        <strong>.xlsx, .xls, .csv</strong>

                    </div>

                </form>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer">

                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                    Tutup

                </button>

                <button type="submit"
                        class="btn btn-success">

                    <i class="fas fa-file-import"></i>
                    Import Data

                </button>

            </div>

        </div>

    </div>

</div>

@endsection
