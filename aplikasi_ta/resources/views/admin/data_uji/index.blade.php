@extends('layouts.admin_layout')

@section('title', 'Data Uji')

@section('content')

<h1 class="mt-4 fw-bold">Data Uji</h1>

<div class="card shadow-sm border-0">

    <div class="card-header bg-white">

        <div class="fw-semibold">
            <i class="fas fa-chart-line text-success me-2"></i>
            Hasil Data Uji
        </div>

    </div>

    <div class="card-body">

        <div class="table-responsive">

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
                        <td>Minggu 10 Juni 2023</td>
                        <td>A</td>
                        <td>WB</td>
                        <td>9</td>
                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection
