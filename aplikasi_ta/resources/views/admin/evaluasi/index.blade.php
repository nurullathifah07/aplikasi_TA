@extends('layouts.admin_layout')

@section('title', 'Evaluasi Model')

@section('content')

<h1 class="mt-4">Evaluasi Model</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Evaluasi</li>
</ol>

<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-chart-line me-1"></i> Hasil Evaluasi (RMSE, MAPE, MAE)
    </div>
    <div class="card-body">
        @if ($evaluasi->isEmpty())
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-1"></i>
                Belum ada hasil evaluasi. Silakan jalankan proses Holt's Linear terlebih dahulu.
            </div>
        @else
            <table class="table table-bordered table-hover">
                <thead class="table-dark-red">
                    <tr>
                        <th>No</th>
                        <th>Tanggal Proses</th>
                        <th>Golongan</th>
                        <th>Komponen</th>
                        <th>Alpha</th>
                        <th>Beta</th>
                        <th>Rasio</th>
                        <th>RMSE</th>
                        <th>MAPE</th>
                        <th>MAE</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($evaluasi as $index => $e)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($e->tanggal_prediksi)->format('d/m/Y') }}</td>
                        <td><span class="badge bg-danger">{{ $e->golongan_darah }}</span></td>
                        <td>{{ $e->komponenDarah->kode }}</td>
                        <td>{{ $e->alpha }}</td>
                        <td>{{ $e->beta }}</td>
                        <td>{{ $e->rasio_split }}</td>
                        <td><strong>{{ $e->rmse }}</strong></td>
                        <td>
                            @if ($e->mape <= 10)
                                <span class="badge bg-success">{{ $e->mape }}%</span>
                            @elseif ($e->mape <= 20)
                                <span class="badge bg-warning">{{ $e->mape }}%</span>
                            @else
                                <span class="badge bg-danger">{{ $e->mape }}%</span>
                            @endif
                        </td>
                        <td><strong>{{ $e->mae }}</strong></td>
                        <td>
                            @if ($e->mape <= 10)
                                <span class="text-success">Sangat Baik</span>
                            @elseif ($e->mape <= 20)
                                <span class="text-warning">Baik</span>
                            @elseif ($e->mape <= 50)
                                <span class="text-info">Cukup</span>
                            @else
                                <span class="text-danger">Kurang Baik</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="alert alert-light mt-3">
                <strong>Interpretasi MAPE:</strong>
                <ul class="mb-0 mt-1">
                    <li><span class="badge bg-success">≤ 10%</span> Sangat Baik</li>
                    <li><span class="badge bg-warning">10% - 20%</span> Baik</li>
                    <li><span class="badge bg-info">20% - 50%</span> Cukup</li>
                    <li><span class="badge bg-danger">> 50%</span> Kurang Baik</li>
                </ul>
            </div>
        @endif
    </div>
</div>

@endsection
