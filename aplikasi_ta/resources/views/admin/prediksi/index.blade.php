@extends('layouts.admin_layout')

@section('title', 'Hasil Prediksi')

@section('content')

<h1 class="mt-4">Hasil Prediksi</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Hasil Prediksi</li>
</ol>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-brain me-1"></i> Prediksi Kebutuhan Darah 7 Hari ke Depan</span>
        <form action="{{ route('prediksi.generate') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-sync me-1"></i> Generate Ulang
            </button>
        </form>
    </div>
    <div class="card-body">
        @if ($prediksi->isEmpty())
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-1"></i>
                Belum ada hasil prediksi. Silakan jalankan proses Holt's Linear terlebih dahulu.
            </div>
        @else
            <table class="table table-bordered table-hover">
                <thead class="table-dark-red">
                    <tr>
                        <th>Tanggal Target</th>
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

            <!-- Chart -->
            <div class="mt-4">
                <canvas id="prediksiChart" height="100"></canvas>
            </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
@if (!$prediksi->isEmpty())
<script>
    const ctx = document.getElementById('prediksiChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($prediksi->map(fn($p) => $p->tanggal_target->format('d/m/Y') . ' (' . $p->golongan_darah . '-' . $p->komponenDarah->kode . ')')) !!},
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
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endif
@endpush
