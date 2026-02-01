@extends('layouts.admin')
@section('title', 'Manajemen Kepala Sekolah')

@section('content')


<div class="row d-flex justify-content-between mb-4">
    <div class="col-md-6 ">
    <h1 class="h3 text-gray-800">Akun Kepala Sekolah/Yayasan</h1>
    </div>
    <div class=" text-right d-flex justify-content-between">
        <a href="{{ route('admin.kepala-sekolah.create') }}" class="btn btn-success shadow-sm">
        <i class="fas fa-plus mr-1"></i> Tambah Akun
        </a>

        <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success border-left-success" role="alert">
    {{ session('success') }}
</div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-white">
        <h6 class="m-0 font-weight-bold text-success">Daftar Akun Yayasan</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="bg-light">
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="10%" class="text-center">Foto</th>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kepseks as $index => $k)
                    <tr>
                        <td class="text-center align-middle">{{ $index + 1 }}</td>
                        <td class="text-center align-middle">
                            @if($k->foto_profil)
                                <img src="{{ asset($k->foto_profil) }}" class="rounded-circle border" width="40" height="40" style="object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto border" style="width: 40px; height: 40px;">
                                    <i class="fas fa-user text-secondary"></i>
                                </div>
                            @endif
                        </td>
                        <td class="font-weight-bold align-middle">{{ $k->name }}</td>
                        <td class="align-middle">{{ $k->username }}</td>
                        <td class="align-middle">{{ $k->email }}</td>
                        <td class="text-center align-middle">
                            <a href="{{ route('admin.kepala-sekolah.edit', $k->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.kepala-sekolah.destroy', $k->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus akun ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <img src="https://img.icons8.com/ios/50/cbd5e0/empty-box.png" class="mb-2"><br>
                            Belum ada akun Kepala Sekolah.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection