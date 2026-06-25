@extends('layouts.admin_layout')

@section('title', 'Preprocessing Data')

@push('styles')
<link href="{{ asset('vendor/datatables/datatables.bootstrap5.min.css') }}" rel="stylesheet" />
<style>
    div.dataTables_processing {
        background: rgba(255, 255, 255, 0.9) !important;
        border: none !important;
        box-shadow: none !important;
        font-size: 14px;
        color: #dc3545;
    }

    div.dataTables_processing>div:last-child {
        display: none !important;
    }

    .stat-card {
        text-align: center;
        padding: 20px 10px;
    }

    .stat-card .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        line-height: 1;
    }

    .stat-card .stat-label {
        font-size: 0.85rem;
        color: #6c757d;
        margin-top: 8px;
    }
</style>
@endpush

@section('content')

<h1 class="mt-4">Preprocessing Data</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Preprocessing</li>
</ol>

<!-- LANGKAH 1 -->
<div class="card mb-4">
    <div class="card-header">
        <strong>LANGKAH 1</strong> — PILIH DATA UNTUK DIPROSES
    </div>
    <div class="card-body">
        <form action="{{ route('preprocessing.proses') }}" method="POST" id="formPreprocessing">
            @csrf
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="golongan_darah" class="form-label">Golongan Darah</label>
                    <select class="form-select" id="golongan_darah" name="golongan_darah" required>
                        <option value="">-- Pilih --</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="AB">AB</option>
                        <option value="O">O</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="komponen_darah_id" class="form-label">Komponen Darah</label>
                    <select class="form-select" id="komponen_darah_id" name="komponen_darah_id" required>
                        <option value="">-- Pilih --</option>
                        @foreach (\App\Models\KomponenDarah::all() as $kd)
                        <option value="{{ $kd->id }}">{{ $kd->kode }} - {{ $kd->nama_lengkap }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                    <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                    <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" required>
                </div>
            </div>
            <div class="row" style="display: none;">
                <div class="col-md-3 mb-3">
                    <label for="metode_imputasi" class="form-label">Metode Imputasi</label>
                    <select class="form-select" id="metode_imputasi" name="metode_imputasi" required>
                        <option value="median" selected>Median</option>
                        <option value="mean">Mean</option>
                        <option value="zero">Isi 0</option>
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- LANGKAH 2 -->
<div id="langkah2" style="display: none;">
    <div class="card mb-4">
        <div class="card-header">
            <strong>LANGKAH 2</strong> — STATISTIK DATA SEBELUM DIPROSES
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="card border">
                        <div class="stat-card">
                            <div class="stat-number text-primary" id="statTotalHari">0</div>
                            <div class="stat-label">Total Hari dalam Rentang</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border">
                        <div class="stat-card">
                            <div class="stat-number text-success" id="statHariData">0</div>
                            <div class="stat-label">Hari Ada Data (Asli)</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border">
                        <div class="stat-card">
                            <div class="stat-number text-danger" id="statHariKosong">0</div>
                            <div class="stat-label">Hari Kosong (Missing)</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Warning -->
            <div id="warningData" style="display: none;"></div>

            <!-- Button Proses -->
            <button type="button" class="btn btn-primary" id="btnProses">
                <i class="fas fa-cogs me-1"></i> Proses Preprocessing
            </button>
        </div>
    </div>
</div>

<!-- Hasil Preprocessing -->
@if ($preprocessed)
<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table me-1"></i> Hasil Preprocessing ({{ count($preprocessed) }} data)
            </div>
            <div class="card-body">
                <div style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-dark-red" style="position: sticky; top: 0;">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Jumlah (kantong)</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($preprocessed as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($item['tanggal'])->format('d/m/Y') }}</td>
                                <td>{{ $item['jumlah'] }}</td>
                                <td>
                                    @if (!empty($item['is_missing']))
                                    <span class="badge bg-secondary">Missing → {{ $item['jumlah'] }}</span>
                                    @else
                                    @php
                                    $isOutlier = collect($outliers)->where('tanggal', $item['tanggal'])->first();
                                    @endphp
                                    @if ($isOutlier)
                                    <span class="badge bg-warning">Outlier ({{ $isOutlier['nilai_asli'] }} → {{ $isOutlier['nilai_pengganti'] }})</span>
                                    @else
                                    <span class="badge bg-success">Normal</span>
                                    @endif
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Ringkasan Hasil Preprocessing -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-clipboard-check me-1"></i> Ringkasan Hasil Preprocessing
            </div>
            <div class="card-body">
                @php
                    $config = session('preprocessing_config');
                    $metodeLabel = match($config['metode_imputasi'] ?? 'median') {
                        'mean' => 'Mean (Rata-rata)',
                        'zero' => 'Isi 0',
                        default => 'Median',
                    };
                    $nilaiImputasi = $config['nilai_imputasi'] ?? 0;
                    $missingCount = collect($preprocessed)->where('is_missing', true)->count();
                @endphp

                <table class="table table-sm mb-3">
                    <tr>
                        <td><strong>Penanganan Missing Values</strong></td>
                    </tr>
                    <tr>
                        <td>
                            Hari tanpa permintaan diisi dengan <strong>{{ $metodeLabel }}</strong> = <strong>{{ $nilaiImputasi }}</strong>
                            <br><small class="text-muted">{{ $missingCount }} hari missing terdeteksi</small>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Penanganan Outlier</strong></td>
                    </tr>
                    <tr>
                        <td>
                            Deteksi menggunakan metode <strong>IQR (Interquartile Range)</strong>, diganti dengan <strong>Median</strong> = <strong>{{ count($outliers) > 0 ? $outliers[0]['nilai_pengganti'] : '-' }}</strong>
                            <br><small class="text-muted">{{ count($outliers) }} outlier terdeteksi</small>
                        </td>
                    </tr>
                </table>

                @if (count($outliers) > 0)
                <hr>
                <small class="fw-bold">Detail Outlier:</small>
                <div style="max-height: 200px; overflow-y: auto;">
                    <table class="table table-sm table-bordered mt-1">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Asli</th>
                                <th>Pengganti</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($outliers as $o)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($o['tanggal'])->format('d/m/Y') }}</td>
                                <td><span class="text-danger fw-bold">{{ $o['nilai_asli'] }}</span></td>
                                <td><span class="text-success fw-bold">{{ $o['nilai_pengganti'] }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<!-- Data Mentah (Server-side) -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-database me-1"></i> Data Permintaan Mentah
    </div>
    <div class="card-body">
        <table id="preprocessingTable" class="table table-bordered table-hover table-sm" style="width:100%">
            <thead class="table-dark-red">
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Rumah Sakit</th>
                    <th>Golongan</th>
                    <th>Komponen</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('vendor/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/datatables.bootstrap5.min.js') }}"></script>
<script>
    $('#preprocessingTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("preprocessing.data") }}',
        columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
            { data: 'tanggal_formatted', name: 'tanggal' },
            { data: 'rumah_sakit_nama', name: 'rumahSakit.nama' },
            {
                data: 'golongan_darah',
                name: 'golongan_darah',
                render: function(data) {
                    return '<span class="badge bg-danger">' + data + '</span>';
                }
            },
            { data: 'komponen_kode', name: 'komponenDarah.kode' },
            { data: 'jumlah', name: 'jumlah' }
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
        order: [[1, 'asc']]
    });

    // AJAX cek jumlah data saat filter berubah → munculkan LANGKAH 2
    function cekDataTersedia() {
        var golongan = $('#golongan_darah').val();
        var komponen = $('#komponen_darah_id').val();
        var mulai = $('#tanggal_mulai').val();
        var selesai = $('#tanggal_selesai').val();

        if (!golongan || !komponen || !mulai || !selesai) {
            $('#langkah2').hide();
            return;
        }

        // Tampilkan langkah 2 dengan loading
        $('#langkah2').show();
        $('#statTotalHari').text('...');
        $('#statHariData').text('...');
        $('#statHariKosong').text('...');
        $('#warningData').hide();

        $.get('{{ route("preprocessing.cek-data") }}', {
            golongan_darah: golongan,
            komponen_darah_id: komponen,
            tanggal_mulai: mulai,
            tanggal_selesai: selesai
        }, function(data) {
            $('#statTotalHari').text(data.total_hari_range);
            $('#statHariData').text(data.hari_ada_data);
            $('#statHariKosong').text(data.hari_kosong);

            // Warning
            var warningHtml = '';
            if (data.hari_ada_data < 4) {
                warningHtml = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-1"></i> Data terlalu sedikit! Minimal 4 hari ada data untuk proses preprocessing.</div>';
            } else if (data.hari_kosong > data.hari_ada_data * 3) {
                warningHtml = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-1"></i> Banyak hari kosong (' + data.hari_kosong + ' hari). Pertimbangkan mempersempit range tanggal agar hasil prediksi lebih akurat.</div>';
            }

            if (warningHtml) {
                $('#warningData').html(warningHtml).show();
            } else {
                $('#warningData').hide();
            }
        });
    }

    $('#golongan_darah, #komponen_darah_id, #tanggal_mulai, #tanggal_selesai').on('change', cekDataTersedia);

    // Button proses submit form
    $('#btnProses').on('click', function() {
        $('#formPreprocessing').submit();
    });
</script>
@endpush
