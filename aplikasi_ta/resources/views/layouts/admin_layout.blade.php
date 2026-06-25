<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>@yield('title', 'Dashboard') - PMI Tanah Laut</title>

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />
    <link href="{{ asset('vendor/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />

    <style>
        /* Navbar - Putih */
        .sb-topnav {
            background-color: #ffffff !important;
            border-bottom: 1px solid #e0e0e0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .sb-topnav .navbar-brand {
            color: #dc3545 !important;
            font-weight: bold;
        }
        .sb-topnav .nav-link {
            color: #333333 !important;
        }
        .sb-topnav .btn-link {
            color: #dc3545 !important;
        }

        /* Sidebar - Merah PMI */
        .sb-sidenav-dark {
            background-color: #dc3545 !important;
        }
        .sb-sidenav-dark .sb-sidenav-menu .nav-link {
            color: rgba(255, 255, 255, 0.85);
        }
        .sb-sidenav-dark .sb-sidenav-menu .nav-link:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.15);
        }
        .sb-sidenav-dark .sb-sidenav-menu .nav-link.active {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.2);
            font-weight: 600;
        }
        .sb-sidenav-dark .sb-sidenav-menu .nav-link .sb-nav-link-icon {
            color: rgba(255, 255, 255, 0.85);
        }
        .sb-sidenav-dark .sb-sidenav-menu-heading {
            color: rgba(255, 255, 255, 0.75) !important;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .sb-sidenav-dark .sb-sidenav-footer {
            background-color: rgba(0, 0, 0, 0.1);
            color: #ffffff;
        }

        /* Table header - Merah PMI */
        .table-dark-red {
            background-color: #dc3545 !important;
            color: #ffffff !important;
        }
        .table-dark-red th {
            background: transparent !important;
            color: #ffffff !important;
            border-color: rgba(255, 255, 255, 0.3) !important;
        }
    </style>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}" />

    <!-- FontAwesome -->
    <link href="{{ asset('vendor/fontawesome/css/all.min.css') }}" rel="stylesheet" />

    @stack('styles')
</head>

<body class="sb-nav-fixed">

<!-- NAVBAR -->
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">

    <!-- Brand -->
    <a class="navbar-brand ps-3" href="{{ route('admin.dashboard') }}">
        <img src="{{ asset('logo.png') }}" alt="Logo" height="30" class="me-2"> PMI Tanah Laut
    </a>

    <!-- Toggle Sidebar -->
    <button class="btn btn-link btn-sm me-4" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Navbar Right -->
    <ul class="navbar-nav ms-auto me-3 me-lg-4">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user fa-fw"></i> {{ Auth::user()->username }}
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="{{ route('akun.index') }}">Kelola Akun</a></li>
                <li><hr class="dropdown-divider" /></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="fas fa-sign-out-alt me-1"></i> Logout
                        </button>
                    </form>
                </li>
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
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                        Dashboard
                    </a>

                    <!-- DATA MASTER -->
                    <div class="sb-sidenav-menu-heading">Data Master</div>

                    <a class="nav-link {{ request()->routeIs('rumah-sakit.*') ? 'active' : '' }}" href="{{ route('rumah-sakit.index') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-hospital"></i></div>
                        Rumah Sakit
                    </a>

                    <a class="nav-link {{ request()->routeIs('komponen-darah.*') ? 'active' : '' }}" href="{{ route('komponen-darah.index') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-vials"></i></div>
                        Komponen Darah
                    </a>

                    <a class="nav-link {{ request()->routeIs('akun.*') ? 'active' : '' }}" href="{{ route('akun.index') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                        Data Akun
                    </a>

                    <!-- TRANSAKSI -->
                    <div class="sb-sidenav-menu-heading">Transaksi</div>

                    <a class="nav-link {{ request()->routeIs('permintaan-darah.*') ? 'active' : '' }}" href="{{ route('permintaan-darah.index') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-hand-holding-medical"></i></div>
                        Permintaan Darah
                    </a>

                    <a class="nav-link {{ request()->routeIs('stok-darah.*') ? 'active' : '' }}" href="{{ route('stok-darah.index') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-boxes-stacked"></i></div>
                        Stok Darah
                    </a>

                    <!-- PERHITUNGAN -->
                    <div class="sb-sidenav-menu-heading">Perhitungan</div>

                    <a class="nav-link {{ request()->routeIs('preprocessing.*') ? 'active' : '' }}" href="{{ route('preprocessing.index') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-filter"></i></div>
                        Preprocessing
                    </a>

                    <a class="nav-link {{ request()->routeIs('holts.*') ? 'active' : '' }}" href="{{ route('holts.index') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-calculator"></i></div>
                        Holt's Linear
                    </a>

                    <a class="nav-link {{ request()->routeIs('evaluasi.*') ? 'active' : '' }}" href="{{ route('evaluasi.index') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-chart-line"></i></div>
                        Evaluasi
                    </a>

                    <a class="nav-link {{ request()->routeIs('prediksi.*') ? 'active' : '' }}" href="{{ route('prediksi.index') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-brain"></i></div>
                        Hasil Prediksi
                    </a>

                </div>
            </div>

            <div class="sb-sidenav-footer">
                <div class="small">Login sebagai:</div>
                {{ Auth::user()->username ?? 'Admin' }}
            </div>
        </nav>
    </div>

    <!-- CONTENT -->
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">

                {{-- Flash Messages ditangani oleh SweetAlert2 --}}

                @yield('content')

            </div>
        </main>

        <!-- FOOTER -->
        <footer class="py-4 bg-light mt-auto">
            <div class="text-muted text-center">
                Copyright &copy; PMI Kabupaten Tanah Laut {{ date('Y') }}
            </div>
        </footer>
    </div>

</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/scripts.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>
<script src="{{ asset('js/datatables-simple-demo.js') }}"></script>
<script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>


<!-- SweetAlert2 Flash Messages -->
@if (session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: '{!! session('success') !!}',
        showConfirmButton: false,
        timer: 2000
    });
</script>
@endif

@if (session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: '{!! session('error') !!}',
        showConfirmButton: true
    });
</script>
@endif

@if ($errors->any())
<script>
    Swal.fire({
        icon: 'error',
        title: 'Validasi Gagal',
        html: '{!! implode("<br>", $errors->all()) !!}',
        showConfirmButton: true
    });
</script>
@endif

<!-- SweetAlert2 Confirm Delete -->
<script>
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-delete');
        if (btn) {
            e.preventDefault();
            const form = btn.closest('form');
            Swal.fire({
                title: 'Yakin hapus data ini?',
                text: 'Data yang dihapus tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@stack('scripts')

</body>
</html>
