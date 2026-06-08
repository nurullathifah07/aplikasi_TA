<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>@yield('title', 'Prediksi Kebutuhan Darah') - PMI Tanah Laut</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="{{ asset('vendor/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('vendor/fontawesome/css/all.min.css') }}" rel="stylesheet" />
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}" />
    <style>
        .navbar-brand-custom {
            font-weight: bold;
            color: #dc3545 !important;
        }
        .hero-section {
            background-color: #dc3545;
            color: white;
            padding: 60px 0;
        }
        .table-dark-red {
            background-color: #dc3545 !important;
            color: #ffffff !important;
        }
        .table-dark-red th {
            background: transparent !important;
            color: #ffffff !important;
            border-color: rgba(255, 255, 255, 0.3) !important;
        }
        .footer-pmi {
            background-color: #dc3545;
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand navbar-brand-custom" href="{{ route('home') }}">
            <img src="{{ asset('logo.png') }}" alt="Logo" height="30" class="me-2"> PMI Tanah Laut
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('publik.prediksi') ? 'active' : '' }}" href="{{ route('publik.prediksi') }}">Prediksi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('publik.stok') ? 'active' : '' }}" href="{{ route('publik.stok') }}">Stok Darah</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('publik.histori') ? 'active' : '' }}" href="{{ route('publik.histori') }}">Histori Tren</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-outline-danger btn-sm ms-2 px-3" href="{{ route('login') }}">
                        <i class="fas fa-sign-in-alt me-1"></i> Login Admin
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Content -->
@yield('content')

<!-- Footer -->
<footer class="footer-pmi text-white py-4 mt-5">
    <div class="container text-center">
        <p class="mb-0">&copy; {{ date('Y') }} PMI Kabupaten Tanah Laut - Sistem Prediksi Kebutuhan Darah</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>
<script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
@stack('scripts')
</body>
</html>
