<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prediksi Stok Darah</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Style tambahan -->
    <style>
        .hero {
            background: linear-gradient(to right, #8B0000, #dc3545);
            color: white;
            padding: 100px 0;
        }
        .section {
            padding: 60px 0;
        }
    </style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#">Sistem Prediksi Darah</a>

        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="#tentang">Tentang</a></li>
                <li class="nav-item"><a class="nav-link" href="#fitur">Fitur</a></li>
                <li class="nav-item"><a class="nav-link" href="#kontak">Kontak</a></li>
                <li class="nav-item">
                    <a class="btn btn-danger ms-2" href="{{ route('admin.dashboard') }}">Login</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="hero text-center">
    <div class="container">
        <h1 class="fw-bold">
            Prediksi Kebutuhan Darah
        </h1>
        <p class="lead mt-3">
            Sistem berbasis web untuk memprediksi kebutuhan stok darah di Kabupaten Tanah Laut menggunakan metode Time Series Forecasting.
        </p>
        <a href="/login" class="btn btn-light mt-3">Mulai Sekarang</a>
    </div>
</section>

<!-- TENTANG -->
<section id="tentang" class="section text-center">
    <div class="container">
        <h2>Tentang Sistem</h2>
        <p class="mt-3">
            Sistem ini dikembangkan untuk membantu pihak terkait dalam mengelola dan memprediksi kebutuhan stok darah secara lebih akurat.
            Dengan memanfaatkan metode <b>Time Series Forecasting (Holt’s Linear)</b>, sistem ini mampu memberikan estimasi kebutuhan darah di masa mendatang.
        </p>
    </div>
</section>

<!-- MASALAH -->
<section class="section bg-light text-center">
    <div class="container">
        <h2>Permasalahan</h2>
        <p class="mt-3">
            Ketersediaan stok darah seringkali tidak seimbang dengan kebutuhan.
            Hal ini dapat menyebabkan kekurangan darah pada saat dibutuhkan, terutama dalam kondisi darurat.
        </p>
    </div>
</section>

<!-- SOLUSI -->
<section class="section text-center">
    <div class="container">
        <h2>Solusi</h2>
        <p class="mt-3">
            Sistem ini memberikan solusi dengan melakukan prediksi kebutuhan darah berdasarkan data historis,
            sehingga pihak pengelola dapat mengambil keputusan yang lebih tepat.
        </p>
    </div>
</section>

<!-- FITUR -->
<section id="fitur" class="section bg-light">
    <div class="container">
        <h2 class="text-center mb-5">Fitur Utama</h2>

        <div class="row text-center">

            <div class="col-md-3">
                <h5>Manajemen Data</h5>
                <p>Pengelolaan data pengguna dan stok darah</p>
            </div>

            <div class="col-md-3">
                <h5>Data Historis</h5>
                <p>Penyimpanan data kebutuhan darah sebelumnya</p>
            </div>

            <div class="col-md-3">
                <h5>Perhitungan</h5>
                <p>Prediksi menggunakan metode Holt’s Linear</p>
            </div>

            <div class="col-md-3">
                <h5>Laporan</h5>
                <p>Hasil prediksi dalam bentuk laporan</p>
            </div>

        </div>
    </div>
</section>

<!-- CTA -->
<section class="section text-center">
    <div class="container">
        <h2>Mulai Gunakan Sistem</h2>
        <p class="mt-3">
            Akses sistem untuk melihat data dan hasil prediksi kebutuhan darah.
        </p>
        <a href="/login" class="btn btn-danger">Login Sekarang</a>
    </div>
</section>

<!-- FOOTER -->
<footer class="bg-dark text-white text-center p-3">
    <p>&copy; 2026 Sistem Prediksi Stok Darah - Kabupaten Tanah Laut</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
