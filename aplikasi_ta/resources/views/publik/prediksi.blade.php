@extends('layouts.publik_layout')

@section('title', 'Prediksi Kebutuhan Darah')

@section('content')

<div class="hero-section text-center">
    <div class="container">
        <h1 class="fw-bold"><i class="fas fa-chart-bar me-2"></i> Prediksi Kebutuhan Darah</h1>
        <p class="lead">Prediksi kebutuhan darah 7 hari ke depan menggunakan metode Holt's Linear</p>
    </div>
</div>

<div class="container mt-4">
    @if ($prediksi->isEmpty())
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle me-1"></i>
            Belum ada data prediksi yang tersedia saat ini.
        </div>
    @else
        <!-- Chart -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-chart-bar me-1"></i> Diagram Prediksi Kebutuhan Darah 7 Hari ke Depan
            </div>
            <div class="card-body">
                <canvas id="prediksiChart" height="100"></canvas>
            </div>
        </div>

        <!-- Tabel -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table me-1"></i> Detail Prediksi
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark-red">
                        <tr>
                            <th>Tanggal</th>
                            <th>Golongan Darah</th>
                            <th>Komponen</th>
                            <th>Prediksi Kebutuhan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prediksi as $p)
                        <tr>
                            <td>{{ $p->tanggal_target->format('d/m/Y') }}</td>
                            <td><span class="badge bg-danger">{{ $p->golongan_darah }}</span></td>
                            <td>{{ $p->komponenDarah->kode }}</td>
                            <td>{{ round($p->nilai_prediksi) }} kantong</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

@endsection

@push('scripts')
@if (!$prediksi->isEmpty())
<script>
    const ctx = document.getElementById('prediksiChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($prediksi->map(fn($p) => $p->tanggal_target->format('d/m') . ' (' . $p->golongan_darah . '-' . $p->komponenDarah->kode . ')')) !!},
            datasets: [{
                label: 'Prediksi Kebutuhan (kantong)',
                data: {!! json_encode($prediksi->pluck('nilai_prediksi')) !!},
                backgroundColor: 'rgba(220, 53, 69, 0.6)',
                borderColor: 'rgba(220, 53, 69, 1)',
                borderWidth: 1
            }]
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
