@extends('layouts.admin_layout')

@section('title', 'Data Latih')

@section('content')

<h1 class="mt-4 fw-bold">Data Latih</h1>

<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}">
            Dashboard
        </a>
    </li>

    <li class="breadcrumb-item active">
        Data Latih
    </li>
</ol>

<div class="card shadow-sm border-0">

    <!-- HEADER -->
    <div class="card-header bg-white">

        <div class="fw-semibold">
            <i class="fas fa-database text-primary me-2"></i>
            Pembagian Data Latih
        </div>

    </div>

    <!-- BODY -->
    <div class="card-body">

        <!-- FORM -->
        <form action="#"
              method="POST">

            @csrf

            <div class="row">

                <div class="col-md-6">

                    <label class="form-label fw-semibold">
                        Persentase Data
                    </label>

                    <select name="persentase"
                            class="form-select">

                        <option value="">
                            -- Pilih Persentase --
                        </option>

                        <option value="70">
                            70% Data Latih - 30% Data Uji
                        </option>

                        <option value="80">
                            80% Data Latih - 20% Data Uji
                        </option>

                        <option value="90">
                            90% Data Latih - 10% Data Uji
                        </option>

                    </select>

                </div>

                <div class="col-md-6 d-flex align-items-end">

                    <button type="submit"
                            class="btn btn-primary">

                        <i class="fas fa-play me-1"></i>
                        Proses Data Latih

                    </button>

                </div>

            </div>

        </form>

        <!-- TABEL -->
        <div class="table-responsive mt-4">

            <table id="datatablesSimple"
                   class="table table-bordered table-striped table-hover">

                <thead class="table-dark">

                    <tr>
                        <th>No</th>
                        <th>Minggu</th>
                        <th>Golongan Darah</th>
                        <th>Komponen</th>
                        <th>Permintaan</th>
                    </tr>

                </thead>

                <tbody>

                    <tr>
                        <td>1</td>
                        <td>Minggu 1 Juni 2023</td>
                        <td>A</td>
                        <td>WB</td>
                        <td>12</td>
                    </tr>

                    <tr>
                        <td>2</td>
                        <td>Minggu 2 Juni 2023</td>
                        <td>A</td>
                        <td>WB</td>
                        <td>10</td>
                    </tr>

                </tbody>

            </table>

        </div>

        <!-- BUTTON NEXT -->
        <div class="text-end mt-4">

            <a href="#"
               class="btn btn-success">

                <i class="fas fa-forward me-1"></i>
                Lihat Data Uji

            </a>

        </div>

    </div>

</div>

@endsection
