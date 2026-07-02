@extends('layouts.admin_layout')

@section('title', "Prediksi Kebutuhan Darah")

@push('styles')
<style>
    .step-header {
        background: #dc3545;
        color: white;
        padding: 10px 16px;
        border-radius: 6px 6px 0 0;
        font-weight: 600;
    }
    .step-body { padding: 16px; border: 1px solid #dee2e6; border-top: none; border-radius: 0 0 6px 6px; }
    .stat-number { font-size: 2rem; font-weight: 700; line-height: 1; }
    .stat-label { font-size: 0.8rem; color: #6c757d; margin-top: 6px; }
</style>
@endpush

@section('content')

<h1 class="mt-4">Prediksi Kebutuhan Darah</h1>

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    {{ session('error') }}
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
        @endforeach
    </ul>
</div>
@endif
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Prediksi</li>
</ol>

<form action="{{ route('peramalan.proses') }}" method="POST">
@csrf

{{-- ===== LANGKAH 1: PILIH DATA ===== --}}
<div class="mb-4">
    <div class="step-header">LANGKAH 1 — PILIH DATA DAN PERIODE PREDIKSI</div>
    <div class="step-body">
        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label">Golongan Darah</label>
                <select class="form-select" name="golongan_darah" required>
                    <option value="">-- Pilih --</option>
                    @foreach(['A','B','AB','O'] as $g)
                    <option value="{{ $g }}" {{ old('golongan_darah', $hasil['golongan_darah'] ?? '') == $g ? 'selected' : '' }}>{{ $g }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Komponen Darah</label>
                <select class="form-select" name="komponen_darah_id" required>
                    <option value="">-- Pilih --</option>
                    @foreach($komponenDarah as $kd)
                    <option value="{{ $kd->id }}" {{ old('komponen_darah_id', $hasil['komponen_darah_id'] ?? '') == $kd->id ? 'selected' : '' }}>
                        {{ $kd->kode }} - {{ $kd->nama_lengkap }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Tanggal Mulai Data</label>
                <input type="date" class="form-control" name="tanggal_mulai" value="{{ old('tanggal_mulai', $hasil['tanggal_mulai'] ?? '') }}" required>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Tanggal Selesai Data</label>
                <input type="date" class="form-control" name="tanggal_selesai" value="{{ old('tanggal_selesai', $hasil['tanggal_selesai'] ?? '') }}" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label">Periode Prediksi</label>

                <select class="form-select" name="periode_prediksi" id="periode_prediksi">
                    <option value="">-- Pilih Periode --</option>
                    <option value="1"
                        {{ old('periode_prediksi') == 1 ? 'selected' : '' }}>
                        1 Hari ke Depan
                    </option>

                    <option value="7"
                        {{ old('periode_prediksi') == 7 ? 'selected' : '' }}>
                        7 Hari ke Depan
                    </option>

                    <option value="custom"
                        {{ old('periode_prediksi') == 'custom' ? 'selected' : '' }}>
                        Pilih Tanggal
                    </option>
                </select>

                {{-- Field tanggal muncul jika memilih custom --}}
                <div id="customDate" class="mt-2" style="display:none;">
                    <label class="form-label">Tanggal Prediksi</label>

                    <input
                        type="date"
                        class="form-control"
                        name="tanggal_prediksi"
                        value="{{ old('tanggal_prediksi') }}">

                    <small class="text-danger">
                        Maksimal 7 hari setelah tanggal selesai data.
                    </small>
                </div>
            </div>

            <!--<div class="col-md-3 mb-3">
                <label class="form-label">
                    Tanggal Prediksi Terakhir
                </label>

                <input type="date"
                    class="form-control"
                    id="tanggal_target"
                    readonly>
            </div>-->
            <div class="col-md-3 mb-3">
                <label class="form-label">Rasio Split (Latih:Uji)</label>
                <select class="form-select" name="rasio_split">
                    @foreach(['70:30','80:20','90:10'] as $r)
                    <option value="{{ $r }}" {{ old('rasio_split', $hasil['rasio_split'] ?? '80:20') == $r ? 'selected' : '' }}>{{ $r }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

{{-- ===== LANGKAH 2: PARAMETER ===== --}}
<div class="mb-4">
    <div class="step-header">LANGKAH 2 — PARAMETER HOLT'S LINEAR</div>
    <div class="step-body">
        <div class="form-check form-switch mb-3">
            <input
                class="form-check-input"
                type="checkbox"
                id="optimasi_otomatis"
                name="optimasi_otomatis"
                {{ old('optimasi_otomatis', $hasil['optimasi_otomatis'] ?? true) ? 'checked' : '' }}
            >
            <label class="form-check-label" for="optimasi_otomatis">
                <strong>Optimasi Parameter Otomatis</strong>
                <small class="text-muted ms-1">(Grid Search — sistem mencari alpha & beta terbaik)</small>
            </label>
        </div>
        <div id="manual_params" class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label">Alpha (α)</label>
                <input type="number" class="form-control" name="alpha" step="0.01" min="0.01" max="0.99"
                    placeholder="0.01 – 0.99" value="{{ old('alpha', $hasil['alpha'] ?? '') }}">
                <small class="text-muted">Pemulusan level</small>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Beta (β)</label>
                <input type="number" class="form-control" name="beta" step="0.01" min="0.01" max="0.99"
                    placeholder="0.01 – 0.99" value="{{ old('beta', $hasil['beta'] ?? '') }}">
                <small class="text-muted">Pemulusan tren</small>
            </div>
        </div>
        <div id="info_optimasi" style="display:none;" class="alert alert-info mb-0">
            <i class="fas fa-info-circle me-1"></i>
            Sistem akan mencari kombinasi alpha dan beta terbaik (0.01 – 0.99) menggunakan Grid Search.
        </div>

        <button type="submit" class="btn btn-danger mt-3">
            <i class="fas fa-play me-1"></i> Jalankan Preprocessing & Prediksi
        </button>
    </div>
</div>

</form>

{{-- ===== HASIL ===== --}}
@if($hasil)

{{-- Ringkasan Info --}}
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center p-3 border">
            <div class="stat-number text-primary">{{ $hasil['total_data'] }}</div>
            <div class="stat-label">Total Data</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center p-3 border">
            <div class="stat-number text-success">{{ $hasil['train_size'] }}</div>
            <div class="stat-label">Data Latih</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center p-3 border">
            <div class="stat-number text-warning">{{ $hasil['test_size'] }}</div>
            <div class="stat-label">Data Uji</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center p-3 border">
            <div class="stat-number text-danger">{{ $hasil['missing_count'] }}</div>
            <div class="stat-label">Missing Value (Imputasi)</div>
        </div>
    </div>
</div>

<div class="row mb-4">
    {{-- Preprocessing Summary --}}
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-cogs me-1"></i> Hasil Preprocessing & Parameter</div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr><td>Golongan Darah</td><td><strong>{{ $hasil['golongan_darah'] }}</strong></td></tr>
                    <tr><td>Missing Value</td><td><strong>{{ $hasil['missing_count'] }} hari → Median = {{ $hasil['nilai_imputasi'] }}</strong></td></tr>
                    <tr><td>Outlier</td><td><strong>{{ $hasil['outlier_count'] }} data → diganti Median</strong></td></tr>
                    <tr>
                    <td>Alpha (α)</td>
                        <td>
                            <strong>{{ $hasil['alpha'] }}</strong>

                            @if($hasil['optimasi_otomatis'])
                                <span class="badge bg-info">Otomatis</span>
                            @endif
                        </td>
                    </tr>
                    <tr><td>Beta (β)</td><td><strong>{{ $hasil['beta'] }}</strong></td></tr>
                    <tr><td>Rasio Split</td><td><strong>{{ $hasil['rasio_split'] }}</strong></td></tr>
                    <tr><td>RMSE</td><td><span class="badge bg-primary fs-6">{{ $hasil['rmse'] }}</span></td></tr>
                    <tr><td>MAPE</td><td><span class="badge bg-{{ $hasil['mape'] < 10 ? 'success' : ($hasil['mape'] < 20 ? 'info' : ($hasil['mape'] < 50 ? 'warning' : 'danger')) }} fs-6">{{ $hasil['mape'] }}%</span></td></tr>
                    <tr><td>MAE</td><td><span class="badge bg-secondary fs-6">{{ $hasil['mae'] }}</span></td></tr>
                </table>
            </div>
        </div>
    </div>

    {{-- Grafik --}}
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-chart-line me-1"></i> Perbandingan Aktual vs Forecast (Data Uji)</div>
            <div class="card-body">
                <canvas id="comparisonChart" height="120"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Outlier Detail --}}
@if(count($hasil['outliers']) > 0)
<div class="card mb-4">
    <div class="card-header"><i class="fas fa-exclamation-triangle me-1 text-warning"></i> Detail Outlier yang Ditemukan</div>
    <div class="card-body">
        <table class="table table-sm table-bordered">
            <thead class="table-dark-red">
                <tr><th>Tanggal</th><th>Nilai Asli</th><th>Nilai Pengganti (Median)</th></tr>
            </thead>
            <tbody>
                @foreach($hasil['outliers'] as $o)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($o['tanggal'])->format('d/m/Y') }}</td>
                    <td><span class="text-danger fw-bold">{{ $o['nilai_asli'] }}</span></td>
                    <td><span class="text-success fw-bold">{{ $o['nilai_pengganti'] }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- ================= Hasil Preprocessing ================= --}}
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-filter me-1"></i>
        Data Setelah Preprocessing
    </div>

    <div class="card-body">

        <div style="max-height:450px;overflow-y:auto;">
            <table class="table table-bordered table-hover table-sm">
                <thead class="table-dark-red">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Jumlah Setelah Preprocessing</th>
                    </tr>
                </thead>

                <tbody>

                @foreach($hasil['preprocessed_data'] as $i => $row)

                    @php
                        $status = 'Normal';

                        if($row['is_missing']){
                            $status = 'Missing Value';
                        }

                        foreach($hasil['outliers'] as $o){
                            if($o['tanggal'] == $row['tanggal']){
                                $status = 'Outlier';
                                break;
                            }
                        }
                    @endphp

                    <tr>

                        <td>{{ $i+1 }}</td>

                        <td>{{ \Carbon\Carbon::parse($row['tanggal'])->format('d/m/Y') }}</td>

                        <td>

                            @if($status=='Normal')
                                <span class="badge bg-success">
                                    Normal
                                </span>

                            @elseif($status=='Missing Value')
                                <span class="badge bg-warning text-dark">
                                    Missing Value
                                </span>

                            @else
                                <span class="badge bg-danger">
                                    Outlier
                                </span>

                            @endif

                        </td>

                        <td>
                            <strong>{{ $row['jumlah'] }}</strong>
                        </td>

                    </tr>

                @endforeach

                </tbody>
            </table>
        </div>

    </div>
</div>

{{-- Rekapitulasi --}}
@if(isset($hasil['rekapitulasi']))
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-table me-1"></i> Rekapitulasi Perhitungan Holt's Linear
        <small class="text-muted ms-2">(Lt = α×Yt + (1−α)×(Lt-1+Tt-1) | Tt = β×(Lt−Lt-1) + (1−β)×Tt-1 | Ft = Lt-1+Tt-1)</small>
    </div>
    <div class="card-body">
        <div style="max-height: 450px; overflow-y: auto;">
            <table class="table table-bordered table-hover table-sm text-center">
                <thead class="table-dark-red" style="position:sticky;top:0;z-index:1;">
                    <tr>
                        <th>Hari (i)</th><th>Tanggal</th><th>Permintaan (Yt)</th>
                        <th>Level (Lt)</th><th>Trend (Tt)</th><th>Forecast (Ft)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($hasil['rekapitulasi'] as $row)
                    <tr class="@if($row['tipe']=='uji') table-warning @elseif($row['tipe']=='prediksi') table-info @endif">
                        <td>{{ $row['hari'] }}</td>
                        <td>{{ $row['tanggal'] != '-' ? \Carbon\Carbon::parse($row['tanggal'])->format('d/m/Y') : '-' }}</td>
                        <td><strong>{{ $row['permintaan'] === '?' ? '?' : $row['permintaan'] }}</strong></td>
                        <td>{{ $row['level'] }}</td>
                        <td>{{ $row['trend'] }}</td>
                        <td><strong>{{ $row['forecast'] === '-' ? '-' : $row['forecast'] }}</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-2">
            <span class="badge bg-white border text-dark me-2">Data Latih</span>
            <span class="badge bg-warning text-dark me-2">Data Uji</span>
            <span class="badge bg-info text-dark">Prediksi</span>
        </div>
    </div>
</div>
@endif

{{-- Hasil Prediksi --}}
@if(isset($hasil['prediksi_hari']))
<div class="card mb-4">
    <div class="card-header"><i class="fas fa-brain me-1"></i> Hasil Prediksi ({{ count($hasil['prediksi_hari']) }} hari)</div>
    <div class="card-body">
        <table class="table table-bordered table-hover">
            <thead class="table-dark-red">
                <tr><th>No</th><th>Tanggal</th><th>Prediksi Kebutuhan</th></tr>
            </thead>
            <tbody>
                @foreach($hasil['prediksi_hari'] as $i => $p)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($p['tanggal'])->format('d/m/Y') }}</td>
                    <td><strong>{{ $p['nilai'] }}</strong> kantong</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endif

@endsection

@push('scripts')
<script>
    // Toggle manual/otomatis
    const toggle = document.getElementById('optimasi_otomatis');
    const manualDiv = document.getElementById('manual_params');
    const infoDiv = document.getElementById('info_optimasi');

    function updateToggle() {
        if (toggle.checked) {
            manualDiv.style.display = 'none';
            infoDiv.style.display = 'block';
        } else {
            manualDiv.style.display = 'flex';
            infoDiv.style.display = 'none';
        }
    }
    toggle.addEventListener('change', updateToggle);
    updateToggle();

    // ===========================
    // Toggle tanggal prediksi
    // ===========================

    const periode = document.getElementById('periode_prediksi');
    const customDate = document.getElementById('customDate');

    function toggleTanggal() {

        if (periode.value === 'custom') {
            customDate.style.display = 'block';
        } else {
            customDate.style.display = 'none';
        }

    }

    periode.addEventListener('change', toggleTanggal);
    toggleTanggal();
</script>

<script>

document.addEventListener("DOMContentLoaded", function(){

    const periode = document.getElementById("periode_prediksi");

    const tanggalSelesai =
        document.querySelector("input[name='tanggal_selesai']");

    const tanggalTarget =
        document.getElementById("tanggal_target");

    function updateTanggal(){

        if(tanggalSelesai.value==""){
            tanggalTarget.value="";
            return;
        }

        let tgl = new Date(tanggalSelesai.value);

        tgl.setDate(
            tgl.getDate()+parseInt(periode.value)
        );

        tanggalTarget.value =
            tgl.toISOString().split('T')[0];

    }

    periode.addEventListener("change",updateTanggal);

    tanggalSelesai.addEventListener("change",updateTanggal);

    updateTanggal();

});

</script>

@if($hasil ?? false)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function(){
    const ctx = document.getElementById('comparisonChart');
    if (!ctx) return;
    new Chart(ctx.getContext('2d'), {
        type: 'line',
        data: {
            labels: {!! json_encode(array_map(fn($t) => \Carbon\Carbon::parse($t)->format('d/m'), $hasil['test_tanggal'])) !!},
            datasets: [
                {
                    label: 'Data Aktual',
                    data: {!! json_encode($hasil['data_aktual']) !!},
                    borderColor: 'rgba(220,53,69,1)',
                    backgroundColor: 'rgba(220,53,69,0.1)',
                    borderWidth: 2, tension: 0.3, fill: false
                },
                {
                    label: 'Forecast',
                    data: {!! json_encode(array_map(fn($v) => round($v,2), $hasil['data_forecast'])) !!},
                    borderColor: 'rgba(13,110,253,1)',
                    backgroundColor: 'rgba(13,110,253,0.1)',
                    borderWidth: 2, borderDash: [5,5], tension: 0.3, fill: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: { y: { beginAtZero: true } }
        }
    });
})();
</script>
@endif
@endpush
