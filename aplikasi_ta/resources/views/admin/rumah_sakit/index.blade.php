@extends('layouts.admin_layout')

@section('title', 'Data Rumah Sakit')

@section('content')

<h1 class="mt-4">Data Rumah Sakit</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Rumah Sakit</li>
</ol>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-hospital me-1"></i> Data Rumah Sakit</span>
        <a href="{{ route('rumah-sakit.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> Tambah
        </a>
    </div>
    <div class="card-body">
        <table id="datatablesSimple" class="table table-bordered table-hover">
            <thead class="table-dark-red">
                <tr>
                    <th width="5%">No</th>
                    <th width="5%">ID</th>
                    <th>Nama Rumah Sakit</th>
                    <th>Alamat</th>
                    <th width="15%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rumahSakit as $rs)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $rs->id }}</td>
                    <td>{{ $rs->nama }}</td>
                    <td>{{ $rs->alamat }}</td>
                    <td>
                        <a href="{{ route('rumah-sakit.edit', $rs) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('rumah-sakit.destroy', $rs) }}" method="POST" class="d-inline">
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
