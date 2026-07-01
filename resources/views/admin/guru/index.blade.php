@extends('layouts.admin')

@section('title', 'Data Guru')

@section('content')

<style>
    /* --- LAYOUT UTAMA --- */
    .page-header-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        flex-wrap: wrap;
        gap: 15px;
    }

    /* Grid Container: Kiri (Sidebar) & Kanan (Content) */
    .admin-responsive-wrapper {
        display: grid;
        grid-template-columns: 360px 1fr; /* Sidebar fix 360px, Kanan sisa layar */
        gap: 25px;
        align-items: start;
    }

    /* --- SIDEBAR WRAPPER (KIRI) --- */
    .sidebar-wrapper {
        display: flex;
        flex-direction: column;
        gap: 20px;
        /* Sticky Logic: Agar sidebar ikut turun saat scroll di desktop */
        position: sticky;
        top: 20px;
        /* Antisipasi jika form terlalu panjang di layar pendek: */
        max-height: calc(100vh - 40px);
        overflow-y: auto; 
        scrollbar-width: none; /* Firefox */
    }

    /* Card Style di Sidebar */
    .form-sidebar {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        border: 1px solid #e5e7eb;
    }

    /* --- CONTENT GRID (KANAN) --- */
    .guru-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 20px;
    }

    /* --- RESPONSIVE MOBILE (< 900px) --- */
    @media (max-width: 991px) {
        .admin-responsive-wrapper {
            grid-template-columns: 1fr; /* Jadi 1 kolom lurus */
        }

        .sidebar-wrapper {
            position: relative; /* Matikan sticky di mobile */
            max-height: none; /* Matikan scroll internal */
            overflow-y: visible;
            order: 1; /* Sidebar muncul duluan */
        }
        
        /* Jika ingin List Guru muncul DULUAN sebelum Form di HP, 
           ganti order: 2 pada sidebar-wrapper dan order: 1 pada content-wrapper */
        
        .page-header-flex {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .page-header-flex .btn-back {
            width: 100%;
            justify-content: center;
        }
    }

    /* --- STYLING FORM & UPLOAD --- */
    .upload-area {
        border: 2px dashed #d1d5db;
        border-radius: 12px; /* Kotak rounded dikit */
        padding: 10px;
        width: 100%;
        text-align: center;
        cursor: pointer;
        background: #f9fafb;
        transition: all 0.3s;
    }
    .upload-area:hover { border-color: #3b82f6; background: #eff6ff; }
    
    .preview-circle {
        width: 80px; height: 80px; 
        border-radius: 50%; 
        object-fit: cover; 
        margin: 0 auto 5px;
        border: 3px solid white;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .form-label { font-size: 0.85rem; font-weight: 600; color: #374151; margin-bottom: 4px; }
    .form-control, .form-select { font-size: 0.9rem; padding: 8px 12px; margin-bottom: 12px; }
    
    .form-row-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
</style>

{{-- HEADER --}}
<div class="page-header-flex">
    <div>
        <h1 class="page-title" style="margin: 0; font-size: 1.5rem; font-weight: 800; color: #111827;">Data Guru</h1>
        <p class="page-subtitle" style="margin: 5px 0 0; color: #6b7280; font-size: 0.9rem;">Kelola data pengajar dan staf sekolah.</p>
    </div>
    <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm" style="display: flex; align-items: center; gap: 5px;">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

{{-- WRAPPER UTAMA --}}
@if(Auth::user()->role == 'admin')
<div class="admin-responsive-wrapper">
@endif
    {{-- KOLOM KIRI (SIDEBAR) --}}
    
    <div class="sidebar-wrapper">

        {{-- 1. BOX IMPORT EXCEL (Ditaruh Paling Atas) --}}
        @if(Auth::user()->role == 'admin')

        <div class="form-sidebar" style="border-left: 4px solid #10b981;">
            <h6 style="font-weight: 700; color: #10b981; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-file-excel"></i> Import Data Guru
            </h6>
            <form action="{{ route('guru.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-2">
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button class="btn btn-success btn-sm" type="submit">
                                <i class="fas fa-upload"></i> Upload & Import
                            </button>
                            <a href="https://bawamai.restetion.com/templates/template_data_guru.xlsx" download class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-download"></i> Download Template
                            </a>
                        </div>
                    </form>
                </div>
                @endif

                {{-- 2. BOX FORM TAMBAH GURU --}}
                @if(Auth::user()->role == 'admin')

                <div class="form-sidebar" style="border-left: 4px solid #3b82f6;">
                    <h6 style="font-weight: 700; color: #3b82f6; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-user-plus"></i> Tambah Guru Manual
                    </h6>
                    
                    <form action="{{ route('guru.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            {{-- Foto Upload --}}
            <div class="mb-3">
                <label for="fotoInput" class="upload-area">
                    <img id="previewImg" src="{{ asset('img/no-image.png') }}" class="preview-circle"
                        onerror="this.src='https://placehold.co/150x150?text=Foto';">
                    <div style="font-size: 0.8rem; color: #3b82f6;">Klik untuk Upload Foto</div>
                </label>
                <input type="file" name="foto" id="fotoInput" style="display: none;" accept="image/*" onchange="previewFile(this)">
            </div>

            {{-- Identitas Utama --}}
            <div class="form-group">
                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="nama_lengkap" class="form-control" required placeholder="Nama Lengkap">
            </div>

            <div class="form-row-grid">
                <div>
                    <label class="form-label">Gelar Depan</label>
                    <input type="text" name="gelar_depan" class="form-control" placeholder="Cth: Dr.">
                </div>
                <div>
                    <label class="form-label">Gelar Blkng</label>
                    <input type="text" name="gelar_belakang" class="form-control" placeholder="Cth: S.Pd">
                </div>
            </div>

            <div class="form-row-grid">
                <div>
                    <label class="form-label">NIY (Login) <span class="text-danger">*</span></label>
                    <input type="number" name="niy" class="form-control" required placeholder="Nomor...">
                </div>
                <div>
                    <label class="form-label">NUPTK</label>
                    <input type="number" name="nuptk" class="form-control" placeholder="Opsional">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Gender <span class="text-danger">*</span></label>
                <select name="jenis_kelamin" class="form-select" required>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>

            {{-- Kepegawaian --}}
            <div class="form-row-grid">
                <div>
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status_kepegawaian" class="form-select" required>
                        <option value="GTY">GTY</option>
                        <option value="PTY">PTY</option>
                        <option value="GTTY">GTTY</option>
                        <option value="PTTY">PTTY</option>
                        <option value="HONORER">Honorer</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Tugas Tambahan</label>
                    <input type="text" name="tugas_tambahan" class="form-control" placeholder="Cth: Wali Kelas">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">TMT Sekolah</label>
                <input type="date" name="tmt_sekolah" class="form-control">
            </div>

            <div class="form-row-grid">
                <div>
                    <label class="form-label">Masa Kerja (SD)</label>
                    <input type="text" name="masa_kerja_sd" class="form-control" placeholder="Cth: 2 Thn">
                </div>
                <div>
                    <label class="form-label">Masa Kerja (Total)</label>
                    <input type="text" name="masa_kerja_total" class="form-control" placeholder="Cth: 5 Thn">
                </div>
            </div>

            {{-- Biodata --}}
            <div class="form-row-grid">
                <div>
                    <label class="form-label">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" class="form-control" required>
                </div>
                <div>
                    <label class="form-label">Tgl Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control" required>
                </div>
            </div>

            {{-- Alamat (BARU) --}}
            <div class="form-group">
                <label class="form-label">Alamat Lengkap</label>
                <textarea name="alamat" class="form-control" rows="2" placeholder="Nama Jalan, RT/RW..."></textarea>
            </div>

            {{-- Pendidikan (UPDATED) --}}
            <div class="form-row-grid">
                <div>
                    <label class="form-label">Pendidikan</label>
                    <select name="pendidikan_terakhir" class="form-select" required>
                        <option value="S1">S1</option>
                        <option value="S2">S2</option>
                        <option value="S3">S3</option>
                        <option value="D3">D3</option>
                        <option value="SMA">SMA/Sederajat</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Thn Lulus</label>
                    <input type="number" name="tahun_lulus" class="form-control" placeholder="Cth: 2015">
                </div>
            </div>

            {{-- Kontak (UPDATED) --}}
            <div class="form-row-grid">
                <div>
                    <label class="form-label">No. HP / WA</label>
                    <input type="text" name="no_hp" class="form-control" placeholder="08...">
                </div>
                <div>
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="email@...">
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 fw-bold mt-2">
                <i class="fas fa-save"></i> Simpan Data
            </button>
        </form>
        </div>
        <div class="mt-1 pt-1">
            <form action="{{ route('guru.destroyAll') }}" method="POST" onsubmit="return confirm('PERINGATAN KERAS!\n\nApakah Anda yakin ingin MENGHAPUS SEMUA DATA GURU?\n\nData yang dihapus: Biodata, Foto, dan Akun Login.\nTindakan ini tidak dapat dibatalkan!');">
                @csrf
                @method('DELETE')
                
                {{-- Class 'btn-danger' membuat latar merah, 'w-100' membuatnya lebar penuh --}}
                <button type="submit" class="btn btn-danger w-100 btn-sm fw-bold shadow-sm">
                    <i class="fas fa-trash-alt me-2"></i> Reset / Hapus Semua Data
                </button>
            </form>
        </div>
        @endif
        
    </div>
    
    {{-- KOLOM KANAN (LIST DATA) --}}
    <div class="content-wrapper">
        
        {{-- SEARCH & FILTER BAR --}}
        <div style="background: white; padding: 15px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 20px; border: 1px solid #e5e7eb;">
            <form action="{{ route('guru.index') }}" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control mb-0" placeholder="Cari Nama, NIY, NUPTK..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                @if(request('search'))
                    <a href="{{ route('guru.index') }}" class="btn btn-light border"><i class="fas fa-sync"></i></a>
                @endif
            </form>
        </div>

        
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- GRID GURU --}}
        <div class="guru-grid">
            @forelse($gurus as $guru)
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden; transition: transform 0.2s;">
                
                {{-- Link Pembungkus ke Halaman Detail (Show) --}}
                <a href="{{ route('guru.show', $guru->id) }}" class="text-decoration-none text-dark h-100 d-flex flex-column">
                    
                    <div style="height: 70px; position: block; background-color: #f3f4f6;">
                        <span class="badge bg-white text-primary position-absolute top-0 end-0 m-2 shadow-sm">
                            {{ $guru->status_kepegawaian }}
                        </span>
                    </div>
                    
                    <div class="card-body text-center pt-0 flex-grow-1">
                        {{-- Foto Profil --}}
                        @php
                            $imgSrc = $guru->foto ? asset('storage/' . $guru->foto) : asset('img/no-image.png');
                        @endphp
                        <img src="{{ $imgSrc }}" 
                            alt="{{ $guru->nama_lengkap }}" 
                            style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 4px solid white; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-top: -40px; background: white;"
                            onerror="this.onerror=null; this.src='https://placehold.co/150x150?text=No+Img';">

                        <h6 class="mt-3 mb-1 fw-bold text-dark">
                            {{ $guru->gelar_depan }} {{ $guru->nama_lengkap }} {{ $guru->gelar_belakang }}
                        </h6>
                        
                        <p class="text-muted small mb-2">
                            NIY: <strong>{{ $guru->niy }}</strong>
                        </p>

                        @if($guru->kelas) 
                            <div class="mb-3">
                                <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                    <i class="fas fa-chalkboard-teacher"></i> Wali Kelas {{ $guru->kelas->nama_kelas }}
                                </span>
                            </div>
                        @endif
                        
                        <p class="small text-muted mb-0"><i class="fas fa-info-circle me-1"></i> Klik untuk detail</p>
                    </div>
                </a>

                {{-- Action Buttons (Ditaruh diluar link utama agar tidak bentrok) --}}
                @if(Auth::user()->role == 'admin')

                <div class="card-footer bg-white border-top-0 pb-3 pt-0 d-flex justify-content-center gap-2">
                    <a href="{{ route('guru.edit', $guru->id) }}" class="btn btn-light btn-sm rounded-circle text-primary border" style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;" title="Edit Data">
                        <i class="fas fa-edit"></i>
                    </a>
                    
                    <form action="{{ route('guru.destroy', $guru->id) }}" method="POST" onsubmit="return confirm('Hapus data guru ini? Akun login juga akan terhapus.');">
                        @csrf @method('DELETE')
                        <button class="btn btn-light btn-sm rounded-circle text-danger border" style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;" title="Hapus Data">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
                @endif

            </div>
            @empty
            <div class="col-12 py-5 text-center text-muted">
                <i class="fas fa-search fa-3x mb-3 text-light"></i>
                <p>Tidak ada data guru ditemukan.</p>
            </div>
            @endforelse
        </div>        
        <div class="mt-4">
            {{ $gurus->links() }}
        </div>

    </div>

 @if(Auth::user()->role == 'admin')
</div>
@endif
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

@endsection
