@extends('layouts.admin')

@section('title', 'Manajemen Data Siswa')

@section('content')

{{-- Header Page --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 text-gray-800 mb-0">Data Siswa</h1>
        <p class="text-muted small mb-0">
            Tahun Ajaran Aktif: 
            @if($activeTa)
                <span class="badge bg-success">{{ $activeTa->tahun_ajaran }} - {{ $activeTa->semester }}</span>
            @else
                <span class="badge bg-danger">Belum diset</span>
            @endif
        </p>
    </div>
    <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
    </a>
</div>

{{-- Feedback Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    
    {{-- === KOLOM KIRI: FORM TAMBAH & IMPORT === --}}
    <div class="col-12 col-lg-4 mb-4">
                {{-- Card Import Excel --}}
        <div class="card shadow border-left-success">
            <div class="card-body">
                <h6 class="font-weight-bold text-success mb-3"><i class="fas fa-file-excel"></i> Import Excel</h6>
                <form action="{{ route('siswa.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="input-group mb-2">
                        <input type="file" name="file" class="form-control form-control-sm" required>
                        <button class="btn btn-success btn-sm" type="submit">Upload</button>
                    </div>
                    <small class="text-muted d-block">Pastikan format kolom: NISN, NIK, Nama, Rombel, dll.</small>
                    <a href="{{asset('templates/template_data_siswa.xlsx')}}" download class="small text-muted text-decoration-underline">Download Template</a>
                </form>
            </div>
        </div>

        {{-- Card Tambah Siswa --}}
        <div class="card shadow mt-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold"><i class="fas fa-user-plus me-1"></i> Tambah Siswa Baru</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('siswa.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    {{-- Preview Foto --}}
                    <div class="text-center mb-4">
                        <label for="fotoInput" style="cursor: pointer;" class="position-relative">
                            <img id="previewImg" src="{{ asset('img/no-image.png') }}" 
                                 class="img-thumbnail rounded-circle shadow-sm"
                                 style="width: 120px; height: 120px; object-fit: cover;"
                                 onerror="this.src='https://placehold.co/150x150?text=Upload'">
                            <div class="badge bg-primary position-absolute bottom-0 start-50 translate-middle-x">
                                <i class="fas fa-camera"></i> Upload
                            </div>
                        </label>
                        <input type="file" name="foto" id="fotoInput" class="d-none" accept="image/*" onchange="previewFile(this)">
                    </div>

                    {{-- Data Identitas --}}
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">NISN <span class="text-danger">*</span></label>
                            <input type="text" name="nisn" class="form-control" placeholder="Nomor NISN" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">NIK</label>
                            <input type="text" name="nik" class="form-control" placeholder="Nomor NIK">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama_lengkap" class="form-control" placeholder="Nama Siswa" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tempat & Tgl Lahir</label>
                        <div class="input-group">
                            <input type="text" name="tempat_lahir" class="form-control" placeholder="Kota">
                            <input type="date" name="tanggal_lahir" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select name="jenis_kelamin" class="form-select" required>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>

            

                    {{-- Pilihan Kelas --}}
                    <div class="form-group bg-light p-2 rounded border mb-3">
                        <label class="font-weight-bold text-primary small">Masuk Kelas (Tahun Ini)</label>
                        <select name="kelas_id" class="form-select">
                            <option value="">-- Belum Masuk Kelas --</option>
                            @foreach($daftarKelas as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }} (Tingkat {{ $k->tingkat }})</option>
                            @endforeach
                        </select>
                    </div>

                    <hr class="my-4 border-secondary-subtle">
                    
                    {{-- Data Wali (FORM BIASA) --}}
                    <p class="text-uppercase text-muted small fw-bold mb-3"><i class="fas fa-user-friends me-1"></i> Data Wali (Opsional)</p>
                    
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Nama Wali</label>
                        <input type="text" name="nama_wali" class="form-control" placeholder="Ayah / Ibu / Wali">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">No. HP Wali</label>
                        <input type="text" name="no_hp_wali" class="form-control" placeholder="0812...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control" rows="2" placeholder="Jl. ..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-2">
                        <i class="fas fa-save me-1"></i> Simpan Siswa
                    </button>
                </form>
            </div>
        </div>

    </div>
    
    {{-- === KOLOM KANAN: DAFTAR SISWA (FILTER & LIST) === --}}
    <div class="col-12 col-lg-8">

        {{-- BAR PENCARIAN & FILTER --}}
        <div class="card shadow mb-4 border-0">
            <div class="card-body p-3">
                <form action="{{ route('siswa.index') }}" method="GET">
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-md-4">
                            <label class="small text-muted fw-bold">Cari Siswa</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-search text-gray-400"></i></span>
                                <input type="text" name="search" class="form-control border-start-0 ps-0" 
                                       placeholder="Nama / NISN / NIK..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="small text-muted fw-bold">Filter Kelas</label>
                            <select name="kelas_id" class="form-select">
                                <option value="">Semua Kelas</option>
                                @foreach($daftarKelas as $k)
                                    <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="small text-muted fw-bold">Filter Gender</label>
                            <select name="jk" class="form-select">
                                <option value="">Semua</option>
                                <option value="L" {{ request('jk') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ request('jk') == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-2 d-grid">
                            <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-filter"></i> Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- GRID DAFTAR SISWA --}}
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3">
            @forelse($siswas as $siswa)
                @php
                    // Ambil kelas aktif siswa (dari eager load)
                    $kelasAktif = $siswa->kelas->first(); 
                @endphp
            <div class="col">
                <div class="card h-100 shadow-sm border-0 position-relative overflow-hidden hover-card">
                    {{-- Banner Warna Berdasarkan Gender --}}
                    <div style="height: 60px; background: {{ $siswa->jenis_kelamin == 'L' ? 'linear-gradient(135deg, #3b82f6, #93c5fd)' : 'linear-gradient(135deg, #ec4899, #f9a8d4)' }};"></div>
                    
                    <div class="card-body text-center pt-0 position-relative">
                        {{-- Foto Profil --}}
                        <div class="position-relative d-inline-block" style="margin-top: -30px;">
                            <img src="{{ $siswa->foto ? asset($siswa->foto) : asset('img/no-image.png') }}" 
                            class="profile-img bg-white"
                            style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                            onerror="this.src='{{ asset('img/no-image.png') }}'">
                        </div>
                        
                        <h6 class="mt-2 mb-0 fw-bold text-dark text-truncate" title="{{ $siswa->nama_lengkap }}">
                            {{ Str::limit($siswa->nama_lengkap, 20) }}
                        </h6>
                        <small class="text-muted d-block mb-2">{{ $siswa->nisn }}</small>

                        {{-- Badge Kelas --}}
                        @if($kelasAktif)
                            <span class="badge bg-primary mb-2">
                                <i class="fas fa-chalkboard-teacher me-1"></i> {{ $kelasAktif->nama_kelas }}
                            </span>
                        @else
                            <span class="badge bg-secondary mb-2">No Class</span>
                        @endif

                        <div class="d-flex justify-content-center gap-1 text-xs text-muted mt-1">
                             <span title="Jenis Kelamin"><i class="fas {{ $siswa->jenis_kelamin == 'L' ? 'fa-mars text-primary' : 'fa-venus text-danger' }}"></i></span>
                             <span>&bull;</span>
                             <span title="Ibu Kandung">{{ Str::limit($siswa->nama_ibu_kandung, 10) }}</span>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="card-footer bg-light border-top-0 d-flex justify-content-between py-2">
                        <a href="{{ route('siswa.edit', $siswa->id) }}" class="btn btn-light btn-sm text-primary" title="Edit">
                            <i class="fas fa-pen"></i> Edit
                        </a>
                        <form action="{{ route('siswa.destroy', $siswa->id) }}" method="POST" onsubmit="return confirm('Hapus siswa {{ $siswa->nama_lengkap }}? Akun login juga akan terhapus.');">
                            @csrf @method('DELETE')
                            <button class="btn btn-light btn-sm text-danger" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="text-center py-5 text-muted">
                    <img src="{{ asset('img/empty.svg') }}" width="100" class="mb-3 opacity-50" onerror="this.style.display='none'">
                    <p>Tidak ada data siswa ditemukan.</p>
                </div>
            </div>
            @endforelse
        </div>
        
        {{-- Pagination --}}
        <div class="mt-4 d-flex justify-content-center">
            {{ $siswas->links() }} 
        </div>
    </div>

</div>

<script>
    function previewFile(input){
        var file = input.files[0];
        if(file){
            var reader = new FileReader();
            reader.onload = function(){
                document.getElementById('previewImg').src = reader.result;
            }
            reader.readAsDataURL(file);
        }
    }
</script>

<style>
    .hover-card:hover {
        transform: translateY(-5px);
        transition: transform 0.2s;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
</style>

@endsection