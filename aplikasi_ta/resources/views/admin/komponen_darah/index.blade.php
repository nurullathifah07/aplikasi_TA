@extends('layouts.admin_layout')

@section('title', 'Data Komponen Darah')

@section('content')

<h1 class="mt-4">Data Komponen Darah</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Komponen Darah</li>
</ol>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-vials me-1"></i> Data Komponen Darah</span>
        <a href="{{ route('komponen-darah.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> Tambah
        </a>
    </div>
    <div class="card-body">
        <table id="datatablesSimple" class="table table-bordered table-hover">
            <thead class="table-dark-red">
                <tr>
                    <th width="5%">No</th>
                    <th width="20%">Kode</th>
                    <th>Nama Lengkap</th>
                    <th width="15%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($komponenDarah as $kd)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td><span class="badge bg-info">{{ $kd->kode }}</span></td>
                    <td>{{ $kd->nama_lengkap }}</td>
                    <td>
                        <a href="{{ route('komponen-darah.edit', $kd) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('komponen-darah.destroy', $kd) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm btn-delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
