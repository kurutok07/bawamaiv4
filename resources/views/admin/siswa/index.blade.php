@extends('layouts.admin')

@section('title', 'Manajemen Data Siswa')

@section('content')

{{-- === HEADER PAGE & ACTIONS === --}}
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Data Siswa</h1>
        <p class="text-muted small mb-0 mt-1">
            Tahun Ajaran Aktif: 
            @if($activeTa)
                <span class="badge bg-success shadow-sm px-2 py-1"><i class="fas fa-calendar-check mr-1"></i> {{ $activeTa->tahun }}</span>
            @else
                <span class="badge bg-danger shadow-sm px-2 py-1"><i class="fas fa-exclamation-triangle mr-1"></i> Belum diset</span>
            @endif
        </p>
    </div>
    
    <div class="d-flex gap-2 mt-2">
        @if(Auth::user()->role == 'admin')

        <div class="">
            <form action="{{ route('siswa.destroyAll') }}" method="POST" onsubmit="return confirm('PERINGATAN KERAS!\n\nApakah Anda yakin ingin MENGHAPUS SEMUA DATA SISWA?\n\nData yang dihapus: Biodata, Foto, Nilai, Data Kesehatan, dan Akun Login.\nTindakan ini tidak dapat dibatalkan!');">
                @csrf
                @method('DELETE')
                
                <button type="submit" class="btn btn-danger w-100 btn-sm fw-bold shadow-sm">
                    <i class="fas fa-trash-alt me-2"></i> Reset Data Siswa
                </button>
            </form>
        </div>

        {{-- Tombol Import (Trigger Modal) --}}

        <button class="btn btn-success shadow-sm btn-sm mr-2" data-toggle="modal" data-target="#importModal">
            <i class="fas fa-file-excel fa-sm text-white-50 mr-1"></i> Import Excel
        </button>

        {{-- Tombol Tambah Siswa (Link ke Halaman Create Full) --}}
        <a href="{{ route('siswa.create') }}" class="btn btn-success shadow-sm btn-sm font-weight-bold px-3">
            <i class="fas fa-user-plus fa-sm text-white-50 mr-1"></i> Tambah Siswa Baru
        </a>
                

        @endif
                {{-- Tombol Kembali Dashboard --}}
        <a href="{{ route('dashboard') }}" class="btn btn-secondary shadow-sm btn-sm mr-2">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Dashboard
        </a>

    </div>
</div>

{{-- === FEEDBACK MESSAGES === --}}
@if (session('success'))
    <div class="alert alert-success border-left-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger border-left-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

{{-- TAMBAHAN: Untuk Alert Warning (Kuning) --}}
@if (session('warning'))
    <div class="alert alert-warning border-left-warning alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('warning') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
{{-- === FILTER & PENCARIAN === --}}
<div class="card shadow-sm mb-4 border-0 border-top-success">
    <div class="card-body py-3">
        <form action="{{ route('siswa.index') }}" method="GET">
            <div class="row align-items-end">
                {{-- Search Input --}}
                <div class="col-lg-4 col-md-6 mb-3 mb-lg-0">
                    <label class="small font-weight-bold text-muted mb-1">Cari Data</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light border-0"><i class="fas fa-search text-gray-400"></i></span>
                        </div>
                        <input type="text" name="search" class="form-control bg-light border-0 small" 
                               placeholder="Nama Siswa / NISN / NIK..." value="{{ request('search') }}">
                    </div>
                </div>

                {{-- Filter Kelas --}}
                <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                    <label class="small font-weight-bold text-muted mb-1">Filter Kelas</label>
                    <select name="kelas_id" class="form-control bg-light border-0 small">
                        <option value="">-- Semua Siswa --</option>
                        
                        {{-- OPSI BARU: Tanpa Kelas --}}
                        <option value="tanpa_kelas" {{ request('kelas_id') == 'tanpa_kelas' ? 'selected' : '' }} class="text-danger font-weight-bold">
                            Belum Masuk Kelas
                        </option>
                        
                        <optgroup label="Daftar Kelas">
                            @foreach($daftarKelas as $k)
                                <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kelas }} (Tk. {{ $k->tingkat }})
                                </option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>
                {{-- Filter Gender --}}
                <div class="col-lg-2 col-md-6 mb-3 mb-lg-0">
                    <label class="small font-weight-bold text-muted mb-1">Gender</label>
                    <select name="jk" class="form-control bg-light border-0 small">
                        <option value="">Semua</option>
                        <option value="L" {{ request('jk') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ request('jk') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                {{-- Tombol Submit --}}
                <div class="col-lg-3 col-md-6 d-flex">
                    <button type="submit" class="btn btn-success btn-sm flex-fill mr-2 shadow-sm">
                        <i class="fas fa-filter fa-sm text-white-50 mr-1"></i> Terapkan Filter
                    </button>
                    <a href="{{ route('siswa.index') }}" class="btn btn-light btn-sm border shadow-sm" title="Reset">
                        <i class="fas fa-undo fa-sm"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- === GRID DAFTAR SISWA === --}}
<div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4 justify-content-center">
    @forelse($siswas as $siswa)
        @php
            $kelasAktif = $siswa->kelas->first(); 
        @endphp
    <div class="col mb-4">
        <div class="card h-100 shadow-sm border-0 position-relative overflow-hidden hover-card">
            
            {{-- Banner Gradient Berdasarkan Gender --}}
            <div style="height: 70px; background: {{ $siswa->jenis_kelamin == 'L' ? 'linear-gradient(135deg, #4e73df 10%, #224abe 100%)' : 'linear-gradient(135deg, #e74a3b 10%, #be2617 100%)' }};">
                {{-- Action Dropdown (Pojok Kanan Atas) --}}
                <div class="dropdown no-arrow position-absolute" style="top: 10px; right: 10px;">
                    <a class="dropdown-toggle text-white" href="#" role="button" id="dropdownMenuLink{{ $siswa->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink{{ $siswa->id }}">
                        <div class="dropdown-header">Aksi Siswa:</div>
                        <a class="dropdown-item" href="{{ route('siswa.show', $siswa->id) }}">
                            <i class="fas fa-eye fa-sm fa-fw mr-2 text-gray-400"></i> Detail Profil
                        </a>
                        @if(Auth::user()->role == 'admin')
                        <a class="dropdown-item" href="{{ route('siswa.edit', $siswa->id) }}">
                            <i class="fas fa-pen fa-sm fa-fw mr-2 text-gray-400"></i> Edit Data
                        </a>
                        <div class="dropdown-divider"></div>
                        <form action="{{ route('siswa.destroy', $siswa->id) }}" method="POST" class="d-block">
                            @csrf @method('DELETE')
                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Yakin hapus siswa {{ $siswa->nama_lengkap }}? Semua data terkait (nilai, keluarga, login) akan terhapus permanen.')">
                                <i class="fas fa-trash fa-sm fa-fw mr-2"></i> Hapus Siswa
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="card-body text-center pt-0 position-relative">
                {{-- Foto Profil (Floating) --}}
                <a href="{{ route('siswa.show', $siswa->id) }}">
                    <div class="position-relative d-inline-block" style="margin-top: -35px;">
                        <img src="{{ $siswa->foto ? asset($siswa->foto) : asset('img/no-image.png') }}" 
                        class="bg-white rounded-circle p-1"
                        style="width: 80px; height: 80px; object-fit: cover; box-shadow: 0 4px 8px rgba(0,0,0,0.1);"
                        onerror="this.src='{{ asset('img/no-image.png') }}'">
                    </div>
                </a>
                
                {{-- Nama & NISN --}}
                <h6 class="mt-3 mb-1 font-weight-bold text-dark text-truncate px-2" title="{{ $siswa->nama_lengkap }}">
                    {{ Str::limit($siswa->nama_lengkap, 22) }}
                </h6>
                <div class="mb-2">
                <span class="badge border px-2 py-1" 
                    style="background-color: #f8f9fc; color: #5a5c69; font-family: 'Courier New', monospace; font-size: 0.9em;">
                    NISN: {{ $siswa->nisn }}
                </span>
            </div>

                {{-- Status Kelas --}}
                <div class="mb-3">
                @if($kelasAktif)
                    {{-- Style: Hijau Soft (Active/Success) --}}
                    <span class="badge border px-2 py-1" 
                        style="background-color: #d1e7dd; color: #0f5132; border-color: #badbcc; font-size: 0.85em;">
                        <i class="fas fa-chalkboard-teacher mr-1"></i> {{ $kelasAktif->nama_kelas }}
                    </span>
                @else
                    {{-- Style: Merah Soft (Warning/Attention) --}}
                    <span class="badge border px-2 py-1" 
                        style="background-color: #f8d7da; color: #842029; border-color: #f5c2c7; font-size: 0.85em;">
                        <i class="fas fa-exclamation-circle mr-1"></i> Belum Masuk Kelas
                    </span>
                @endif
                </div>

                {{-- Info Tambahan (Grid Kecil) --}}
                <div class="row text-center border-top pt-2 no-gutters">
                    <div class="col-6 border-right">
                        <small class="text-xs font-weight-bold text-uppercase text-muted d-block">Gender</small>
                        <span class="small font-weight-bold {{ $siswa->jenis_kelamin == 'L' ? 'text-success' : 'text-danger' }}">
                            {{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                        </span>
                    </div>
                    <div class="col-6">
                        <small class="text-xs font-weight-bold text-uppercase text-muted d-block">Ibu Kandung</small>
                        <span class="small text-dark text-truncate d-block px-1">
                            {{ Str::limit($siswa->ibu->nama ?? '-', 10) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Footer (Link to Show) --}}
            <a href="{{ route('siswa.show', $siswa->id) }}" class="card-footer bg-light border-top-0 text-center small text-success font-weight-bold py-2" style="text-decoration: none;">
                Lihat Profil Lengkap <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
@empty
    {{-- Tambahkan style="grid-column: 1 / -1;" agar elemen melebar full ke samping --}}
    <div class="col-12" style="grid-column: 1 / -1;">
        
        {{-- Gunakan Flexbox Utility biar kontennya beneran di tengah --}}
        <div class="d-flex flex-column align-items-center justify-content-center text-center py-5" style="min-height: 300px;">
            
            
            <h5 class="text-gray-600 font-weight-bold">Data tidak ditemukan</h5>
            <p class="text-muted small mb-0">Coba ubah kata kunci pencarian atau filter Anda.</p>
            
            @if(Auth::user()->role == 'admin')
                <a href="{{ route('siswa.create') }}" class="btn btn-success btn-sm mt-3 shadow-sm px-4 rounded-pill">
                    <i class="fas fa-plus mr-1"></i> Tambah Siswa Baru
                </a>
            @endif
            
        </div>
    </div>
    @endforelse
</div>

{{-- === PAGINATION === --}}
<div class="mt-4 d-flex justify-content-center">
    {{ $siswas->links() }} 
</div>


{{-- === MODAL IMPORT EXCEL === --}}
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title font-weight-bold" id="importModalLabel"><i class="fas fa-file-excel mr-2"></i> Import Data Siswa</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('siswa.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <img src="https://illustrations.popsy.co/green/success.svg" width="120" class="mb-2">
                        <p class="small text-muted">Upload file Excel (.xlsx / .xls) berisi data siswa masal.</p>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold small">Pilih File Excel</label>
                        <div class="custom-file">
                            <input type="file" name="file" class="custom-file-input" id="importFile" required>
                            <label class="custom-file-label" for="importFile">Choose file...</label>
                        </div>
                    </div>

                    <div class="alert alert-light border small text-muted mt-3">
                        <i class="fas fa-info-circle text-info mr-1"></i> <strong>Penting:</strong>
                        <ul class="pl-3 mb-0 mt-1">
                            <li>Pastikan format kolom sesuai Template.</li>
                            <li>NISN dan NIK harus unik.</li>
                        </ul>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="#" class="btn btn-sm btn-outline-success border-0 font-weight-bold">
                            <i class="fas fa-download mr-1"></i> Download Template Excel
                        </a>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 font-weight-bold">Upload & Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Styling Tambahan untuk Card */
    .hover-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    .profile-img {
        transition: transform 0.3s ease;
    }
    .hover-card:hover .profile-img {
        transform: scale(1.1);
    }
    .text-xs {
        font-size: 0.75rem;
    }
    /* Agar tampilan input file bootstrap dinamis (optional JS needed for label update, or use simple input) */
</style>

@endsection