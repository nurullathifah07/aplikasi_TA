<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Dashboard - SB Admin</title>

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />

    <!-- FontAwesome -->
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed">

<!-- NAVBAR -->
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">

    <!-- Brand -->
    <a class="navbar-brand ps-3" href="#">Start Bootstrap</a>

    <!-- Toggle Sidebar -->
    <button class="btn btn-link btn-sm me-4" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- 🔥 Ini yang bikin ke kanan -->
    <ul class="navbar-nav ms-auto me-3 me-lg-4">

        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user fa-fw"></i>
            </a>

            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="#">Settings</a></li>
                <li><a class="dropdown-item" href="#">Activity Log</a></li>
                <li><hr class="dropdown-divider" /></li>
                <li><a class="dropdown-item" href="#">Logout</a></li>
            </ul>
        </li>

    </ul>
</nav>

<div id="layoutSidenav">

    <!-- SIDEBAR -->
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">

                    <!-- Dashboard -->
                    <a class="nav-link" href="{{ route('admin.dashboard') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                        Dashboard
                    </a>

                    <!-- DATA MASTER -->
                    <div class="sb-sidenav-menu-heading">Data Master</div>

                    <a class="nav-link" href="{{ route('pengguna.index') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                        Data Pengguna
                    </a>

                    <!-- MENU DATA -->
                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseData">
                        <div class="sb-nav-link-icon"><i class="fas fa-database"></i></div>
                        Data
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>

                    <div class="collapse" id="collapseData" data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">

                            <a class="nav-link" href="{{ route('dataset.index') }}">
                                Dataset
                            </a>

                            <a class="nav-link" href="{{ route('preprocessing.index') }}">
                                Preprocessing
                            </a>

                            <a class="nav-link" href="{{ route('data_latih.index') }}">
                                Data Latih
                            </a>

                            <a class="nav-link" href="{{ route('data_uji.index') }}">
                                Data Uji
                            </a>

                        </nav>
                    </div>

                    <!-- PERHITUNGAN -->
                    <div class="sb-sidenav-menu-heading">Perhitungan</div>

                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePerhitungan">
                        <div class="sb-nav-link-icon"><i class="fas fa-calculator"></i></div>
                        Holt’s Linear
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>

                    <div class="collapse" id="collapsePerhitungan" data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">

                            <a class="nav-link" href="#">
                                Pembentukan Series
                            </a>

                            <a class="nav-link" href="#">
                                Inisialisasi Parameter
                            </a>

                            <a class="nav-link" href="#">
                                Proses Holt’s Linear
                            </a>

                            <a class="nav-link" href="#">
                                Hasil Forecasting
                            </a>

                            <a class="nav-link" href="#">
                                Evaluasi Hasil Peramalan
                            </a>

                            <a class="nav-link" href="#">
                                Grafik Prediksi
                            </a>

                        </nav>
                    </div>

                    <!-- LAPORAN -->
                    <div class="sb-sidenav-menu-heading">Laporan</div>

                    <a class="nav-link" href="#">
                        <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>
                        Laporan Prediksi
                    </a>

                </div>
            </div>

            <div class="sb-sidenav-footer">
                <div class="small">Login sebagai:</div>
                Admin
            </div>
        </nav>
    </div>

    <!-- CONTENT -->
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">

                @yield('content')

            </div>
        </main>

        <!-- FOOTER -->
        <footer class="py-4 bg-light mt-auto">
            <div class="text-muted text-center">Copyright &copy; Your Website 2026</div>
        </footer>
    </div>

</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="{{ asset('js/scripts.js') }}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>

<script src="{{ asset('assets/demo/chart-area-demo.js') }}"></script>
<script src="{{ asset('assets/demo/chart-bar-demo.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>
<script src="{{ asset('js/datatables-simple-demo.js') }}"></script>

</body>
</html>
