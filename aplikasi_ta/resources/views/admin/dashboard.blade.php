@extends('layouts.admin_layout')

@section('title', 'Dashboard')

@section('content')

<h1 class="mt-4">Dashboard</h1>

<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item active">Prediksi Kebutuhan Darah - PMI Kabupaten Tanah Laut</li>
</ol>

<!-- CARD RINGKASAN -->
<div class="row">

    <!-- Total Rumah Sakit -->
    <div class="col-xl-3 col-md-6">
        <div class="card bg-primary text-white mb-4">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="small">Total Rumah Sakit</div>
                    <div class="fs-4 fw-bold">{{ $totalRS }}</div>
                </div>
                <i class="fas fa-hospital fa-2x opacity-50"></i>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link text-decoration-none" href="{{ route('rumah-sakit.index') }}">Lihat Detail</a>
                <i class="fas fa-angle-right text-white"></i>
            </div>
        </div>
    </div>

    <!-- Total Stok -->
    <div class="col-xl-3 col-md-6">
        <div class="card bg-success text-white mb-4">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="small">Total Stok Darah</div>
                    <div class="fs-4 fw-bold">{{ $totalStok }} Kantong</div>
                </div>
                <i class="fas fa-boxes-stacked fa-2x opacity-50"></i>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link text-decoration-none" href="{{ route('stok-darah.index') }}">Lihat Detail</a>
                <i class="fas fa-angle-right text-white"></i>
            </div>
        </div>
    </div>

    <!-- Permintaan Hari Ini -->
    <div class="col-xl-3 col-md-6">
        <div class="card bg-warning text-white mb-4">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="small">Permintaan Hari Ini</div>
                    <div class="fs-4 fw-bold">{{ $permintaanHariIni }}</div>
                </div>
                <i class="fas fa-hand-holding-medical fa-2x opacity-50"></i>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link text-decoration-none" href="{{ route('permintaan-darah.index') }}">Lihat Detail</a>
                <i class="fas fa-angle-right text-white"></i>
            </div>
        </div>
    </div>

    <!-- Total Data Historis -->
    <div class="col-xl-3 col-md-6">
        <div class="card bg-danger text-white mb-4">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="small">Data Historis</div>
                    <div class="fs-4 fw-bold">{{ $totalPermintaan }} Record</div>
                </div>
                <i class="fas fa-database fa-2x opacity-50"></i>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link text-decoration-none" href="{{ route('permintaan-darah.index') }}">Lihat Detail</a>
                <i class="fas fa-angle-right text-white"></i>
            </div>
        </div>
    </div>

</div>

<!-- STOK PER GOLONGAN DARAH -->
<div class="row">
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-tint me-1"></i> Stok Darah Saat Ini
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark-red">
                        <tr>
                            <th>Golongan</th>
                            <th>Komponen</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stokDarah as $stok)
                        <tr>
                            <td><span class="badge bg-danger">{{ $stok->golongan_darah }}</span></td>
                            <td>{{ $stok->komponenDarah->kode }}</td>
                            <td>{{ $stok->jumlah }} kantong</td>
                            <td>
                                @if ($stok->jumlah >= 50)
                                    <span class="badge bg-success">Aman</span>
                                @elseif ($stok->jumlah >= 20)
                                    <span class="badge bg-warning">Menipis</span>
                                @else
                                    <span class="badge bg-danger">Kritis</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">Belum ada data stok</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- PERMINTAAN TERBARU -->
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-clock me-1"></i> Permintaan Terbaru
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark-red">
                        <tr>
                            <th>Tanggal</th>
                            <th>RS</th>
                            <th>Gol</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($permintaanTerbaru as $p)
                        <tr>
                            <td>{{ $p->tanggal->format('d/m/Y') }}</td>
                            <td>{{ $p->rumahSakit->nama }}</td>
                            <td>{{ $p->golongan_darah }}</td>
                            <td>{{ $p->jumlah }}</td>
                            <td>
                                @if ($p->status == 'terpenuhi')
                                    <span class="badge bg-success">Terpenuhi</span>
                                @elseif ($p->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @else
                                    <span class="badge bg-danger">Ditolak</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Belum ada permintaan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
