<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Login - Prediksi Kebutuhan Darah</title>
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />
    <link href="{{ asset('vendor/fontawesome/css/all.min.css') }}" rel="stylesheet" />
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}" />
    <style>
        body {
            background-color: #dc3545;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            border-top: 4px solid #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="card shadow-lg border-0 rounded-lg">
                                <div class="card-header text-center">
                                    <img src="{{ asset('logo.png') }}" alt="Logo" height="60" class="mb-2">
                                    <h3 class="fw-bold my-2">
                                        PMI Tanah Laut
                                    </h3>
                                    <p class="text-muted mb-0">Sistem Prediksi Kebutuhan Darah</p>
                                </div>
                                <div class="card-body p-4">
                                    @if ($errors->any())
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            @foreach ($errors->all() as $error)
                                                {{ $error }}
                                            @endforeach
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('login.submit') }}">
                                        @csrf
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputUsername" name="username" type="text"
                                                placeholder="Username" value="{{ old('username') }}" required autofocus />
                                            <label for="inputUsername">Username</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputPassword" name="password" type="password"
                                                placeholder="Password" required />
                                            <label for="inputPassword">Password</label>
                                        </div>
                                        <div class="d-grid">
                                            <button class="btn btn-danger btn-lg" type="submit">
                                                <i class="fas fa-sign-in-alt me-1"></i> Masuk
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <small class="text-muted">
                                        &copy; {{ date('Y') }} PMI Kabupaten Tanah Laut
                                    </small>
                                </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
