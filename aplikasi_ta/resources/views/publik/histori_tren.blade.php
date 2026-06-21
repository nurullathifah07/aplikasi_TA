@extends('layouts.publik_layout')

@section('title', 'Histori Tren Permintaan')

@section('content')

<style>
    div.dataTables_processing {
        background: rgba(255,255,255,0.9) !important;
        border: none !important;
        box-shadow: none !important;
        font-size: 14px;
        color: #dc3545;
    }
    div.dataTables_processing > div:last-child {
        display: none !important;
    }
</style>

<div class="hero-section text-center">
    <div class="container">
        <h1 class="fw-bold"><i class="fas fa-chart-line me-2"></i> Histori Tren Permintaan</h1>
        <p class="lead">Tren permintaan darah historis di Kabupaten Tanah Laut</p>
    </div>
</div>

<div class="container mt-4">
    <!-- Chart dengan filter bulan -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-chart-line me-1"></i> Grafik Tren Permintaan Darah (<span id="labelBulan">{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</span>)</span>
            <input type="month" id="filterBulan" class="form-control" style="width: 200px;" value="{{ date('Y-m') }}">
        </div>
        <div class="card-body">
            <canvas id="trenChart" height="100"></canvas>
            <div id="chartLoader" class="text-center py-3" style="display:none;">
                <div class="spinner-border spinner-border-sm text-danger"></div> Memuat grafik...
            </div>
            <div id="chartEmpty" class="text-center text-muted py-3" style="display:none;">
                <i class="fas fa-info-circle me-1"></i> Tidak ada data pada bulan ini.
            </div>
        </div>
    </div>

    <!-- Tabel Server-side -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i> Data Histori Permintaan
        </div>
        <div class="card-body">
            <table id="historiTable" class="table table-bordered table-hover table-sm" style="width:100%">
                <thead class="table-dark-red">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Golongan Darah</th>
                        <th>Komponen</th>
                        <th>Total Permintaan</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('vendor/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/datatables.bootstrap5.min.js') }}"></script>
<link href="{{ asset('vendor/datatables/datatables.bootstrap5.min.css') }}" rel="stylesheet" />
<script>
$('#historiTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: '{{ route("publik.histori.data") }}',
    columns: [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
        { data: 'tanggal_formatted', name: 'tanggal' },
        { data: 'golongan_darah', name: 'golongan_darah', render: function(data) { return '<span class="badge bg-danger">' + data + '</span>'; } },
        { data: 'komponen_kode', name: 'komponenDarah.kode' },
        { data: 'total', name: 'total', render: function(data) { return data + ' kantong'; } }
    ],
    language: {
        processing: '<div class="spinner-border spinner-border-sm text-danger"></div> Memuat...',
        search: 'Cari:',
        lengthMenu: 'Tampilkan _MENU_ data',
        info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
        infoEmpty: 'Tidak ada data',
        zeroRecords: 'Data tidak ditemukan',
        paginate: { first: 'Awal', last: 'Akhir', next: '&raquo;', previous: '&laquo;' }
    },
    order: [[1, 'desc']]
});

// Chart dinamis per bulan
var trenChart = null;

function loadChart(bulan) {
    $('#chartLoader').show();
    $('#chartEmpty').hide();
    if (trenChart) { trenChart.destroy(); }

    $.get('{{ route("publik.histori.chart") }}', { bulan: bulan }, function(data) {
        $('#chartLoader').hide();

        if (data.dates.length === 0) {
            $('#chartEmpty').show();
            return;
        }

        const golonganColors = {
            'A': { bg: 'rgba(220, 53, 69, 0.6)', border: 'rgba(220, 53, 69, 1)' },
            'B': { bg: 'rgba(13, 110, 253, 0.6)', border: 'rgba(13, 110, 253, 1)' },
            'AB': { bg: 'rgba(25, 135, 84, 0.6)', border: 'rgba(25, 135, 84, 1)' },
            'O': { bg: 'rgba(255, 193, 7, 0.6)', border: 'rgba(255, 193, 7, 1)' }
        };

        const datasets = Object.keys(data.series).map(gol => {
            return {
                label: 'Golongan ' + gol,
                data: data.series[gol],
                backgroundColor: golonganColors[gol]?.bg || 'rgba(0,0,0,0.3)',
                borderColor: golonganColors[gol]?.border || 'rgba(0,0,0,1)',
                borderWidth: 2,
                fill: false
            };
        });

        const ctx = document.getElementById('trenChart').getContext('2d');
        trenChart = new Chart(ctx, {
            type: 'line',
            data: { labels: data.dates, datasets: datasets },
            options: {
                responsive: true,
                scales: { yAxes: [{ ticks: { beginAtZero: true } }] }
            }
        });
    });
}

// Load chart default bulan berjalan
loadChart($('#filterBulan').val());

// Reload chart saat bulan berubah
$('#filterBulan').on('change', function() {
    var val = $(this).val();
    var date = new Date(val + '-01');
    var namaBulan = date.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
    $('#labelBulan').text(namaBulan);
    loadChart(val);
});
</script>
@endpush
