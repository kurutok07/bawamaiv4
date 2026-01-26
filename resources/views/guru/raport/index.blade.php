@extends('layouts.admin')

@section('content')
{{-- Custom CSS untuk efek hover --}}
<style>
    .card-hover {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s;
    }
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    .bg-icon-primary {
        background-color: rgba(78, 115, 223, 0.1);
        padding: 15px;
        border-radius: 50%;
    }
</style>

<div class="container-fluid">

    {{-- Header & Tombol Kembali --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-book-open text-primary mr-2"></i> E-Raport
            </h1>
            <p class="mb-0 text-gray-600 small mt-1">Pilih kelas untuk mengelola nilai raport siswa.</p>
        </div>
        <a href="{{ url('/dashboard') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Kembali ke Dashboard
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger border-left-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        @forelse($classes as $kelas)
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 card-hover">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            {{-- Badge Tahun Ajaran --}}
                            <div class="mb-2">
                                <span class="badge badge-primary px-2 py-1">
                                    <i class="fas fa-calendar-alt mr-1"></i> TA: {{ $activeYear->tahun }}
                                </span>
                            </div>
                            
                            {{-- Nama Kelas --}}
                            <div class="h5 mb-1 font-weight-bold text-gray-800">
                                Kelas {{ $kelas->nama_kelas }}
                            </div>
                            
                            {{-- Jumlah Siswa --}}
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                <i class="fas fa-users mr-1"></i> {{ $kelas->siswas_count }} Siswa Terdaftar
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="bg-icon-primary">
                                <i class="fas fa-chalkboard-teacher fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="sidebar-divider my-3">
                    
                    {{-- Tombol Aksi --}}
                    <a href="{{ route('guru.raport.show', $kelas->id) }}" class="btn btn-primary btn-block shadow-sm">
                        <i class="fas fa-folder-open mr-2"></i> Buka Kelas
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-body text-center py-5">
                    <!--<img class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="width: 15rem;" src="{{ asset('img/undraw_empty.svg') }}" alt="No Data"> -->
                    <h5 class="text-gray-800 font-weight-bold">Belum Ada Kelas</h5>
                    <p class="text-gray-600 mb-0">
                        Anda belum terdaftar sebagai Wali Kelas di Tahun Ajaran Aktif ({{ $activeYear->tahun ?? '-' }}).
                    </p>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection