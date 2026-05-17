@extends('layouts.admin_layout')

@section('content')

<div class="container-fluid px-4">

    <!-- Judul -->
    <h1 class="mt-4">Dashboard</h1>

    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Prediksi Stok Darah</li>
    </ol>

    <!-- CARD RINGKASAN -->
    <div class="row">

        <!-- Total Pendonor -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">Total Pendonor</div>
                <div class="card-footer">120 Orang</div>
            </div>
        </div>

        <!-- Total Stok -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">Total Stok Darah</div>
                <div class="card-footer">350 Kantong</div>
            </div>
        </div>

        <!-- Data Historis -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">Data Historis</div>
                <div class="card-footer">24 Bulan</div>
            </div>
        </div>

        <!-- Hasil Prediksi -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">Hasil Prediksi</div>
                <div class="card-footer">Bulan Berikutnya</div>
            </div>
        </div>

    </div>

    <!-- STOK PER GOLONGAN DARAH -->
    <div class="card mb-4">
        <div class="card-header">
            Stok Darah Saat Ini
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Golongan Darah</th>
                        <th>Jumlah Kantong</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>A</td>
                        <td>80</td>
                        <td><span class="badge bg-success">Aman</span></td>
                    </tr>
                    <tr>
                        <td>B</td>
                        <td>40</td>
                        <td><span class="badge bg-warning">Menipis</span></td>
                    </tr>
                    <tr>
                        <td>AB</td>
                        <td>20</td>
                        <td><span class="badge bg-danger">Kritis</span></td>
                    </tr>
                    <tr>
                        <td>O</td>
                        <td>210</td>
                        <td><span class="badge bg-success">Aman</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- GRAFIK PREDIKSI -->
    <div class="card mb-4">
        <div class="card-header">
            Grafik Prediksi Stok Darah (Holt’s Linear)
        </div>
        <div class="card-body">
            <canvas id="myChart" height="100"></canvas>
        </div>
    </div>

</div>

@endsection
