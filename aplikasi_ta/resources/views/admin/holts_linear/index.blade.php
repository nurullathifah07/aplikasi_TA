@extends('layouts.admin_layout')

@section('title', "Holt's Linear Exponential Smoothing")

@section('content')

<h1 class="mt-4">Holt's Linear Exponential Smoothing</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Holt's Linear</li>
</ol>

<!-- Form Konfigurasi -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-calculator me-1"></i> Konfigurasi & Proses
    </div>
    <div class="card-body">
        <form action="{{ route('holts.proses') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="golongan_darah" class="form-label">Golongan Darah</label>
                    <select class="form-select" id="golongan_darah" name="golongan_darah" required>
                        <option value="">-- Pilih --</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="AB">AB</option>
                        <option value="O">O</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="komponen_darah_id" class="form-label">Komponen Darah</label>
                    <select class="form-select" id="komponen_darah_id" name="komponen_darah_id" required>
                        <option value="">-- Pilih --</option>
                        @foreach ($komponenDarah as $kd)
                            <option value="{{ $kd->id }}">{{ $kd->kode }} - {{ $kd->nama_lengkap }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="rasio_split" class="form-label">Rasio Split (Latih:Uji)</label>
                    <select class="form-select" id="rasio_split" name="rasio_split" required>
                        <option value="70:30">70:30</option>
                        <option value="80:20" selected>80:20</option>
                        <option value="90:10">90:10</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="optimasi_otomatis" name="optimasi_otomatis" checked>
                        <label class="form-check-label" for="optimasi_otomatis">Optimasi Parameter Otomatis</label>
                    </div>
                </div>
            </div>

            <div class="row" id="manual_params" style="display: none;">
                <div class="col-md-4 mb-3">
                    <label for="alpha" class="form-label">Alpha (0-1)</label>
                    <input type="number" class="form-control" id="alpha" name="alpha" step="0.01" min="0.01" max="0.99" value="0.5">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="beta" class="form-label">Beta (0-1)</label>
                    <input type="number" class="form-control" id="beta" name="beta" step="0.01" min="0.01" max="0.99" value="0.5">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-play me-1"></i> Jalankan Proses
            </button>
        </form>
    </div>
</div>

<!-- Hasil -->
@if ($hasil)
<div class="row">
    <!-- Parameter & Evaluasi -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-sliders-h me-1"></i> Parameter
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr><td>Golongan Darah</td><td><strong>{{ $hasil['golongan_darah'] }}</strong></td></tr>
                    <tr><td>Alpha (&alpha;)</td><td><strong>{{ $hasil['alpha'] }}</strong></td></tr>
                    <tr><td>Beta (&beta;)</td><td><strong>{{ $hasil['beta'] }}</strong></td></tr>
                    <tr><td>Rasio Split</td><td><strong>{{ $hasil['rasio_split'] }}</strong></td></tr>
                    <tr><td>Total Data</td><td><strong>{{ $hasil['total_data'] }}</strong></td></tr>
                    <tr><td>Data Latih</td><td><strong>{{ $hasil['train_size'] }}</strong></td></tr>
                    <tr><td>Data Uji</td><td><strong>{{ $hasil['test_size'] }}</strong></td></tr>
                </table>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-chart-line me-1"></i> Evaluasi
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td>RMSE</td>
                        <td><span class="badge bg-primary fs-6">{{ $hasil['rmse'] }}</span></td>
                    </tr>
                    <tr>
                        <td>MAPE</td>
                        <td><span class="badge bg-success fs-6">{{ $hasil['mape'] }}%</span></td>
                    </tr>
                    <tr>
                        <td>MAE</td>
                        <td><span class="badge bg-info fs-6">{{ $hasil['mae'] }}</span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Grafik Perbandingan -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-chart-bar me-1"></i> Perbandingan Data Aktual vs Forecast (Data Uji)
            </div>
            <div class="card-body">
                <canvas id="comparisonChart" height="120"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Rekapitulasi Perhitungan -->
@if (isset($hasil['rekapitulasi']))
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-table me-1"></i> Rekapitulasi Perhitungan Holt's Linear
        <small class="text-muted ms-2">(Lt = &alpha; &times; Yt + (1-&alpha;) &times; (Lt-1 + Tt-1) | Tt = &beta; &times; (Lt - Lt-1) + (1-&beta;) &times; Tt-1 | Ft = Lt-1 + Tt-1)</small>
    </div>
    <div class="card-body">
        <div style="max-height: 500px; overflow-y: auto;">
            <table class="table table-bordered table-hover table-sm text-center">
                <thead class="table-dark-red" style="position: sticky; top: 0; z-index: 1;">
                    <tr>
                        <th>Hari (i)</th>
                        <th>Tanggal</th>
                        <th>Permintaan (Yt)</th>
                        <th>Level (Lt)</th>
                        <th>Trend (Tt)</th>
                        <th>Forecast (Ft)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($hasil['rekapitulasi'] as $row)
                    <tr class="@if($row['tipe'] == 'uji') table-warning @elseif($row['tipe'] == 'prediksi') table-info @endif">
                        <td>{{ $row['hari'] }}</td>
                        <td>{{ $row['tanggal'] != '-' ? \Carbon\Carbon::parse($row['tanggal'])->format('d/m/Y') : '-' }}</td>
                        <td>
                            @if ($row['permintaan'] === '?')
                                <span class="text-muted">?</span>
                            @else
                                <strong>{{ $row['permintaan'] }}</strong>
                            @endif
                        </td>
                        <td>{{ $row['level'] }}</td>
                        <td>{{ $row['trend'] }}</td>
                        <td>
                            @if ($row['forecast'] === '-')
                                <span class="text-muted">-</span>
                            @else
                                <strong>{{ $row['forecast'] }}</strong>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-2">
            <span class="badge bg-white border text-dark me-2">Data Latih</span>
            <span class="badge bg-warning text-dark me-2">Data Uji</span>
            <span class="badge bg-info text-dark">Prediksi 7 Hari</span>
        </div>
    </div>
</div>
@endif

<!-- Prediksi 7 Hari -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-brain me-1"></i> Prediksi 7 Hari ke Depan
    </div>
    <div class="card-body">
        <table class="table table-bordered table-hover">
            <thead class="table-dark-red">
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Prediksi Kebutuhan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($hasil['prediksi_7_hari'] as $index => $p)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($p['tanggal'])->format('d/m/Y') }}</td>
                    <td><strong>{{ $p['nilai'] }}</strong> kantong</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
    document.getElementById('optimasi_otomatis').addEventListener('change', function() {
        document.getElementById('manual_params').style.display = this.checked ? 'none' : 'flex';
    });
</script>

@if ($hasil)
<script>
    const ctx = document.getElementById('comparisonChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_map(function($t) { return \Carbon\Carbon::parse($t)->format('d/m'); }, $hasil['test_tanggal'])) !!},
            datasets: [
                {
                    label: 'Data Aktual',
                    data: {!! json_encode($hasil['data_aktual']) !!},
                    borderColor: 'rgba(220, 53, 69, 1)',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    borderWidth: 2,
                    fill: false
                },
                {
                    label: 'Forecast',
                    data: {!! json_encode(array_map(fn($v) => round($v, 2), $hasil['data_forecast'])) !!},
                    borderColor: 'rgba(13, 110, 253, 1)',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                yAxes: [{ ticks: { beginAtZero: true } }]
            }
        }
    });
</script>
@endif
@endpush
