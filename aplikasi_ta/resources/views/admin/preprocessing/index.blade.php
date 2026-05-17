@extends('layouts.admin_layout')

@section('title', 'Preprocessing')

@section('content')

<!-- PAGE TITLE -->
<h1 class="mt-4 fw-bold">Preprocessing Data</h1>

<!-- BREADCRUMB -->
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}">
            Dashboard
        </a>
    </li>

    <li class="breadcrumb-item active">
        Preprocessing
    </li>
</ol>

<!-- CARD -->
<div class="card shadow-sm border-0">

    <!-- HEADER -->
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">

        <div class="fw-semibold">
            <i class="fas fa-cogs text-primary me-2"></i>
            Tahap Preprocessing Dataset
        </div>

        <!-- BUTTON PROCESS -->
        <form action="#" method="POST">
            @csrf

            <button type="submit"
                    class="btn btn-primary btn-sm shadow-sm">

                <i class="fas fa-play me-1"></i>
                Proses Preprocessing

            </button>
        </form>

    </div>

    <!-- BODY -->
    <div class="card-body">

        <!-- ALERT INFO -->
        <div class="alert alert-info border-0 shadow-sm">

            <div class="d-flex">

                <div class="me-3 mt-1">
                    <i class="fas fa-info-circle fa-lg"></i>
                </div>

                <div>

                    <h6 class="fw-bold mb-2">
                        Informasi Tahap Preprocessing
                    </h6>

                    <ul class="mb-0">
                        <li>Membersihkan data yang tidak valid</li>
                        <li>Mengubah nilai kosong menjadi 0</li>
                        <li>Mengelompokkan data berdasarkan minggu</li>
                        <li>Membentuk dataset forecasting</li>
                    </ul>

                </div>

            </div>

        </div>

        <!-- HASIL PREPROCESSING -->
        <div class="card border-0 shadow-sm mt-4">

            <!-- HEADER -->
            <div class="card-header bg-light d-flex justify-content-between align-items-center">

                <div class="fw-semibold">
                    <i class="fas fa-check-circle text-success me-2"></i>
                    Hasil Preprocessing
                </div>

                <span class="badge bg-success">
                    Data Diproses
                </span>

            </div>

            <!-- BODY -->
            <div class="card-body">

                <!-- JIKA BELUM ADA HASIL -->
                {{--
                <div class="text-center py-5">

                    <img src="{{ asset('img/no-data.svg') }}"
                         width="180"
                         class="mb-3">

                    <h5 class="fw-bold text-secondary">
                        Belum Ada Hasil Preprocessing
                    </h5>

                    <p class="text-muted mb-0">
                        Klik tombol proses preprocessing untuk memulai pengolahan data.
                    </p>

                </div>
                --}}

                <!-- TABEL -->
                <div class="table-responsive">

                    <table id="datatablesSimple"
                           class="table table-bordered table-hover table-striped align-middle">

                        <thead class="table-success">

                            <tr>
                                <th>No</th>
                                <th>Minggu</th>
                                <th>Golongan Darah</th>
                                <th>Komponen</th>
                                <th>Permintaan</th>
                                <th>Status</th>
                            </tr>

                        </thead>

                        <tbody>

                            <tr>
                                <td>1</td>
                                <td>Minggu 1 Juni 2023</td>
                                <td>A</td>
                                <td>WB</td>
                                <td>12</td>

                                <td>
                                    <span class="badge bg-success">
                                        Valid
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <td>2</td>
                                <td>Minggu 1 Juni 2023</td>
                                <td>B</td>
                                <td>PRC</td>
                                <td>0</td>

                                <td>
                                    <span class="badge bg-warning text-dark">
                                        Transformasi 0
                                    </span>
                                </td>
                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

        <!-- BUTTON NEXT -->
        <div class="text-end mt-4">

            <a href="#"
               class="btn btn-success shadow-sm">

                <i class="fas fa-forward me-1"></i>
                Lanjut Data Latih & Uji

            </a>

        </div>

    </div>

</div>

@endsection
