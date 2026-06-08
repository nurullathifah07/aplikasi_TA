@extends('layouts.publik_layout')

@section('title', 'Sistem Prediksi Kebutuhan Darah')

@section('content')

<!-- HERO -->
<section class="hero-section text-center">
    <div class="container">
        <h1 class="fw-bold display-4">
            <i class="fas fa-tint me-2"></i> Prediksi Kebutuhan Darah
        </h1>
        <p class="lead mt-3">
            Sistem berbasis web untuk memprediksi kebutuhan stok darah di Kabupaten Tanah Laut
            menggunakan metode Time Series Forecasting (Holt's Linear Exponential Smoothing).
        </p>
        <div class="mt-4">
            <a href="{{ route('publik.prediksi') }}" class="btn btn-light btn-lg me-2">
                <i class="fas fa-chart-bar me-1"></i> Lihat Prediksi
            </a>
            <a href="{{ route('publik.stok') }}" class="btn btn-outline-light btn-lg">
                <i class="fas fa-boxes-stacked me-1"></i> Cek Stok Darah
            </a>
        </div>
    </div>
</section>

<!-- TENTANG -->
<section class="py-5 text-center">
    <div class="container">
        <h2 class="fw-bold">Tentang Sistem</h2>
        <p class="mt-3 col-lg-8 mx-auto">
            Sistem ini dikembangkan untuk membantu PMI Kabupaten Tanah Laut dalam mengelola dan memprediksi
            kebutuhan stok darah secara lebih akurat. Dengan memanfaatkan metode
            <strong>Holt's Linear Exponential Smoothing</strong>, sistem ini mampu memberikan estimasi
            kebutuhan darah 7 hari ke depan.
        </p>
    </div>
</section>

<!-- FITUR -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center fw-bold mb-5">Fitur Utama</h2>

        <div class="row text-center g-4">
            <div class="col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-hospital fa-3x text-danger mb-3"></i>
                        <h5>Manajemen Data</h5>
                        <p class="text-muted">Pengelolaan data rumah sakit, permintaan, dan stok darah</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-database fa-3x text-primary mb-3"></i>
                        <h5>Data Historis</h5>
                        <p class="text-muted">Penyimpanan data permintaan darah harian dari rumah sakit</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-calculator fa-3x text-success mb-3"></i>
                        <h5>Prediksi</h5>
                        <p class="text-muted">Prediksi kebutuhan darah menggunakan Holt's Linear</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-chart-line fa-3x text-warning mb-3"></i>
                        <h5>Evaluasi</h5>
                        <p class="text-muted">Evaluasi akurasi dengan RMSE, MAPE, dan MAE</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-5 text-center">
    <div class="container">
        <h2 class="fw-bold">Lihat Informasi Kebutuhan Darah</h2>
        <p class="mt-3 text-muted">
            Akses informasi prediksi kebutuhan darah, stok saat ini, dan histori tren permintaan.
        </p>
        <div class="mt-3">
            <a href="{{ route('publik.prediksi') }}" class="btn btn-danger btn-lg me-2">
                <i class="fas fa-chart-bar me-1"></i> Prediksi
            </a>
            <a href="{{ route('publik.histori') }}" class="btn btn-outline-danger btn-lg">
                <i class="fas fa-chart-line me-1"></i> Histori Tren
            </a>
        </div>
    </div>
</section>

@endsection
