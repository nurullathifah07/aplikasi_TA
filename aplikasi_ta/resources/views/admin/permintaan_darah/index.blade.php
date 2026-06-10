@extends('layouts.admin_layout')

@section('title', 'Permintaan Darah')

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

<h1 class="mt-4">Permintaan Darah</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Permintaan Darah</li>
</ol>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-hand-holding-medical me-1"></i> Data Permintaan Darah</span>
        <div>
            <button class="btn btn-success btn-sm me-1" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fas fa-file-excel me-1"></i> Import Excel
            </button>
            <a href="{{ route('permintaan-darah.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Tambah
            </a>
        </div>
    </div>
    <div class="card-body">
        <table id="permintaanTable" class="table table-bordered table-hover" style="width:100%">
            <thead class="table-dark-red">
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Rumah Sakit</th>
                    <th>Golongan</th>
                    <th>Komponen</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Modal Import -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">
                    <i class="fas fa-file-excel me-1"></i> Import Data Permintaan Darah
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <small>
                        <strong>Format header:</strong> tanggal, id_rs, golongan_darah, komponen, jumlah, status<br>
                        <strong>Catatan:</strong> Data dimulai dari baris ke-2 (baris 1 = header). Jika header tidak sesuai maka import gagal.<br>
                        <a href="{{ route('permintaan-darah.template') }}" class="fw-bold">
                            <i class="fas fa-download me-1"></i> Klik disini untuk download template
                        </a>
                    </small>
                </div>

                <form id="importForm" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="file" class="form-label">Pilih File Excel (.xlsx)</label>
                        <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.xls" required>
                    </div>
                </form>

                <!-- Preloader -->
                <div id="importLoader" style="display: none;" class="text-center py-3">
                    <div class="spinner-border text-danger" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Sedang mengimport data, mohon tunggu...</p>
                </div>

                <!-- Hasil Import -->
                <div id="importResult" style="display: none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success" id="btnImport">
                    <i class="fas fa-upload me-1"></i> Import
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('vendor/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/datatables.bootstrap5.min.js') }}"></script>
<script>
// Server-side DataTables
var table = $('#permintaanTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: '{{ route("permintaan-darah.data") }}',
    columns: [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
        { data: 'tanggal_formatted', name: 'tanggal' },
        { data: 'rumah_sakit_nama', name: 'rumahSakit.nama' },
        { data: 'golongan_darah', name: 'golongan_darah', render: function(data) { return '<span class="badge bg-danger">' + data + '</span>'; } },
        { data: 'komponen_kode', name: 'komponenDarah.kode' },
        { data: 'jumlah', name: 'jumlah', render: function(data) { return data + ' kantong'; } },
        { data: 'status_badge', name: 'status' },
        { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
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

// Import Excel
document.getElementById('btnImport').addEventListener('click', function() {
    const form = document.getElementById('importForm');
    const fileInput = document.getElementById('file');
    const loader = document.getElementById('importLoader');
    const result = document.getElementById('importResult');
    const btnImport = this;

    if (!fileInput.files.length) {
        Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Pilih file Excel terlebih dahulu.' });
        return;
    }

    loader.style.display = 'block';
    result.style.display = 'none';
    btnImport.disabled = true;

    const formData = new FormData(form);

    fetch('{{ route("permintaan-darah.import") }}', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        loader.style.display = 'none';
        btnImport.disabled = false;

        if (data.success) {
            let html = '<div class="alert alert-success"><i class="fas fa-check-circle me-1"></i> ' + data.message + '</div>';
            if (data.errors && data.errors.length > 0) {
                html += '<div class="alert alert-warning"><strong>Peringatan:</strong><ul class="mb-0 mt-1">';
                data.errors.forEach(function(err) { html += '<li><small>' + err + '</small></li>'; });
                html += '</ul></div>';
            }
            result.innerHTML = html;
            result.style.display = 'block';
            table.ajax.reload();
        } else {
            let html = '<div class="alert alert-danger"><i class="fas fa-times-circle me-1"></i> ' + data.message + '</div>';
            if (data.errors && data.errors.length > 0) {
                html += '<div class="alert alert-warning"><strong>Detail error:</strong><ul class="mb-0 mt-1">';
                data.errors.forEach(function(err) { html += '<li><small>' + err + '</small></li>'; });
                html += '</ul></div>';
            }
            result.innerHTML = html;
            result.style.display = 'block';
        }
    })
    .catch(error => {
        loader.style.display = 'none';
        btnImport.disabled = false;
        result.innerHTML = '<div class="alert alert-danger"><i class="fas fa-times-circle me-1"></i> Terjadi kesalahan. Pastikan file sesuai format dan header benar.</div>';
        result.style.display = 'block';
    });
});

// AJAX Update Status (centang & x) - langsung tanpa alert
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.btn-status');
    if (!btn) return;

    const id = btn.dataset.id;
    const status = btn.dataset.status;
    const row = btn.closest('tr');
    const statusCell = row.querySelector('td:nth-child(7)');
    const actionBtns = row.querySelectorAll('.btn-status');

    actionBtns.forEach(b => { b.disabled = true; });
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    fetch('/admin/permintaan-darah/' + id + '/update-status', {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (status === 'terpenuhi') {
                statusCell.innerHTML = '<span class="badge bg-success">Terpenuhi</span>';
            } else {
                statusCell.innerHTML = '<span class="badge bg-danger">Ditolak</span>';
            }
            actionBtns.forEach(b => b.remove());
        }
    })
    .catch(() => {
        actionBtns.forEach(b => { b.disabled = false; });
        btn.innerHTML = status === 'terpenuhi' ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times"></i>';
    });
});
</script>
@endpush
