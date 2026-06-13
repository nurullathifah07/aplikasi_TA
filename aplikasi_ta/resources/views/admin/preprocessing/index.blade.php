@extends('layouts.admin_layout')

@section('title', 'Preprocessing Data')

@push('styles')
<link href="{{ asset('vendor/datatables/datatables.bootstrap5.min.css') }}" rel="stylesheet" />
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
@endpush

@section('content')

<h1 class="mt-4">Preprocessing Data</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Preprocessing</li>
</ol>

<div class="alert alert-info">
    <i class="fas fa-info-circle me-1"></i>
    <strong>Preprocessing</strong> meliputi:
    <ul class="mb-0 mt-1">
        <li>Aggregasi data permintaan per hari</li>
        <li>Handling missing values (hari tanpa permintaan diisi 0, mean, atau median)</li>
        <li>Deteksi dan penanganan outlier (metode IQR, diganti median)</li>
    </ul>
</div>

<!-- Form Filter -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-filter me-1"></i> Pilih Data untuk Preprocessing
    </div>
    <div class="card-body">
        <form action="{{ route('preprocessing.proses') }}" method="POST">
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
                <div class="col-md-3 mb-3">
                    <label for="metode_imputasi" class="form-label">Metode Imputasi</label>
                    <select class="form-select" id="metode_imputasi" name="metode_imputasi" required>
                        <option value="median" selected>Median</option>
                        <option value="mean">Mean</option>
                        <option value="zero">Isi 0</option>
                    </select>
                </div>
            </div>

            <!-- Info Data -->
            <div id="infoData" class="alert alert-light border mb-3" style="display: none;">
                <i class="fas fa-info-circle me-1 text-primary"></i>
                <span id="infoText">Memuat...</span>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-cogs me-1"></i> Proses Preprocessing
            </button>
        </form>
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
        <!-- Info Outlier -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-exclamation-triangle me-1"></i> Outlier Terdeteksi ({{ count($outliers) }})
            </div>
            <div class="card-body">
                @if (count($outliers) > 0)
                    <table class="table table-sm table-bordered">
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
                @else
                    <p class="text-muted mb-0">Tidak ada outlier terdeteksi.</p>
                @endif
            </div>
        </div>

        <!-- Ringkasan -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-chart-pie me-1"></i> Ringkasan
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li><strong>Total data:</strong> {{ count($preprocessed) }} hari</li>
                    <li><strong>Missing values:</strong> {{ collect($preprocessed)->where('is_missing', true)->count() }} hari</li>
                    <li><strong>Outlier:</strong> {{ count($outliers) }} data</li>
                    <li><strong>Rata-rata:</strong> {{ round(collect($preprocessed)->avg('jumlah'), 2) }} kantong/hari</li>
                </ul>
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
    columns: [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
        { data: 'tanggal_formatted', name: 'tanggal' },
        { data: 'rumah_sakit_nama', name: 'rumahSakit.nama' },
        { data: 'golongan_darah', name: 'golongan_darah', render: function(data) { return '<span class="badge bg-danger">' + data + '</span>'; } },
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

// AJAX cek jumlah data saat filter berubah
function cekDataTersedia() {
    var golongan = $('#golongan_darah').val();
    var komponen = $('#komponen_darah_id').val();
    var mulai = $('#tanggal_mulai').val();
    var selesai = $('#tanggal_selesai').val();
    var metode = $('#metode_imputasi').val();

    if (!golongan || !komponen || !mulai || !selesai) {
        $('#infoData').hide();
        return;
    }

    $('#infoData').show();
    $('#infoText').html('<i class="fas fa-spinner fa-spin"></i> Mengecek data...');

    $.get('{{ route("preprocessing.cek-data") }}', {
        golongan_darah: golongan,
        komponen_darah_id: komponen,
        tanggal_mulai: mulai,
        tanggal_selesai: selesai
    }, function(data) {
        var html = '<strong>' + data.total_record + ' record</strong> ditemukan | ';
        html += '<strong>' + data.hari_ada_data + ' hari</strong> ada data dari total <strong>' + data.total_hari_range + ' hari</strong> dalam range | ';
        var nilaiImputasi = metode === 'mean' ? data.mean : (metode === 'median' ? data.median : 0);
        var labelMetode = metode === 'mean' ? 'mean' : (metode === 'median' ? 'median' : '0');
        html += '<strong>' + data.hari_kosong + ' hari</strong> akan diisi dengan <strong>' + labelMetode + ' (' + nilaiImputasi + ')</strong>';

        if (data.hari_ada_data < 4) {
            html += '<br><span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Data terlalu sedikit! Minimal 4 hari ada data.</span>';
        } else if (data.hari_kosong > data.hari_ada_data * 3) {
            html += '<br><span class="text-warning"><i class="fas fa-exclamation-triangle"></i> Banyak hari kosong. Pertimbangkan mempersempit range tanggal.</span>';
        }

        $('#infoText').html(html);
    });
}

$('#golongan_darah, #komponen_darah_id, #tanggal_mulai, #tanggal_selesai, #metode_imputasi').on('change', cekDataTersedia);
</script>
@endpush
