@extends('layouts.admin')

@section('title', 'Arsip Raport')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="font-weight-bold mb-0 text-gray-800">Arsip Raport Saya</h3>
            <p class="text-muted small mb-0">Pilih kelas untuk melihat dokumen raport.</p>
        </div>
        <a href="{{ route('landing') }}" class="btn btn-secondary btn-sm shadow-sm rounded-pill px-3">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    @if($riwayatKelas->isEmpty())
        <div class="text-center py-5">
            <img src="{{ asset('img/empty_data.svg') }}" style="width: 200px; opacity: 0.6;" class="mb-4">
            <h5 class="text-gray-600">Belum Ada Riwayat Kelas</h5>
            <p class="text-muted">Anda belum terdaftar dalam kelas manapun pada sistem ini.</p>
        </div>
    @else

        <div class="row">
            @foreach($riwayatKelas as $kelas)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-sm border-0 h-100 folder-card">
                        {{-- Header Card (Warna-warni sesuai ID agar tidak monoton) --}}
                        @php
                            $colors = ['bg-primary', 'bg-success', 'bg-info', 'bg-warning', 'bg-danger'];
                            $bgClass = $colors[$loop->index % 5];
                        @endphp
                        
                        <div class="card-header {{ $bgClass }} text-white py-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0 font-weight-bold">{{ $kelas->nama_kelas }}</h5>
                                <small class="opacity-75">{{ $kelas->tahunAjaran->tahun ?? 'Tahun Ajaran -' }}</small>
                            </div>
                            <i class="fas fa-folder-open fa-2x opacity-50"></i>
                        </div>
                        
                        {{-- Body Card --}}
                        <div class="card-body d-flex flex-column">
                            <div class="mb-3">
                                <small class="text-uppercase text-muted font-weight-bold" style="font-size: 0.7rem;">Wali Kelas</small>
                                <div class="d-flex align-items-center mt-1">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mr-2" style="width: 35px; height: 35px;">
                                        <i class="fas fa-user-tie text-secondary"></i>
                                    </div>
                                    <span class="font-weight-bold text-dark small">
                                        {{ $kelas->waliKelas->nama_lengkap ?? 'Belum ditentukan' }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="mt-auto">
                                <a href="{{ route('siswa.raport.show', ['kelas_id' => $kelas->id]) }}" 
                                   class="btn btn-outline-dark btn-block rounded-pill btn-sm font-weight-bold">
                                    <i class="fas fa-eye mr-1"></i> Buka Arsip
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<style>
    .folder-card {
        transition: transform 0.2s;
    }
    .folder-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important;
    }
</style>
@endsection