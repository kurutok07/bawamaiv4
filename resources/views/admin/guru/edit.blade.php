@extends('layouts.admin')

@section('title', 'Edit Data Guru')

@section('content')

{{-- CSS YANG SAMA DENGAN INDEX --}}
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
        grid-template-columns: 320px 1fr; /* Sidebar kiri sedikit lebih kecil untuk edit */
        gap: 25px;
        align-items: start;
    }

    /* --- SIDEBAR WRAPPER (KIRI) --- */
    .sidebar-wrapper {
        display: flex;
        flex-direction: column;
        gap: 20px;
        position: sticky;
        top: 20px;
    }

    /* Card Style */
    .form-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        border: 1px solid #e5e7eb;
    }

    /* --- RESPONSIVE MOBILE --- */
    @media (max-width: 991px) {
        .admin-responsive-wrapper {
            grid-template-columns: 1fr;
        }
        .sidebar-wrapper {
            position: static;
            order: 1; /* Foto muncul duluan di HP */
        }
        .content-wrapper {
            order: 2;
        }
    }

    /* --- STYLING FORM & UPLOAD --- */
    .upload-area {
        border: 2px dashed #d1d5db;
        border-radius: 12px;
        padding: 20px 10px;
        width: 100%;
        text-align: center;
        cursor: pointer;
        background: #f9fafb;
        transition: all 0.3s;
        margin-bottom: 15px;
    }
    .upload-area:hover { border-color: #3b82f6; background: #eff6ff; }

    .preview-circle {
        width: 120px; height: 120px; /* Lebih besar untuk halaman edit */
        border-radius: 50%;
        object-fit: cover;
        margin: 0 auto 10px;
        border: 4px solid white;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .form-label { font-size: 0.85rem; font-weight: 600; color: #374151; margin-bottom: 6px; }
    .form-control, .form-select { font-size: 0.95rem; padding: 10px 12px; margin-bottom: 15px; border-radius: 8px; }
    textarea.form-control { border-radius: 12px; }

    .form-row-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }
    
    .section-title {
        font-size: 1rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        gap: 10px;
    }
</style>

{{-- HEADER --}}
<div class="page-header-flex">
    <div>
        <h1 class="page-title" style="margin: 0; font-size: 1.5rem; font-weight: 800; color: #111827;">Edit Guru</h1>
        <p class="page-subtitle" style="margin: 5px 0 0; color: #6b7280; font-size: 0.9rem;">
            Perbarui data untuk: <span class="text-primary fw-bold">{{ $guru->nama_lengkap }}</span>
        </p>
    </div>
    <a href="{{ route('guru.index') }}" class="btn btn-secondary btn-sm" style="display: flex; align-items: center; gap: 5px;">
        <i class="fas fa-arrow-left"></i> Batal & Kembali
    </a>
</div>

{{-- FORM UTAMA --}}
<form action="{{ route('guru.update', $guru->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    {{-- Hidden Input untuk validasi unik user_id --}}
    <input type="hidden" name="user_id_check" value="{{ $guru->user_id }}">

    <div class="admin-responsive-wrapper">

        {{-- KOLOM KIRI (FOTO & AKUN) --}}
        <div class="sidebar-wrapper">
            <div class="form-card" style="border-top: 4px solid #3b82f6;">
                <h6 class="section-title text-primary"><i class="fas fa-camera"></i> Foto Profil</h6>
                
                {{-- Foto Upload --}}
                <label for="fotoInput" class="upload-area">
                    @php
                        $imgSrc = $guru->foto ? asset('storage/' . $guru->foto) : asset('img/no-image.png');
                    @endphp
                    <img id="previewImg" src="{{ $imgSrc }}" class="preview-circle"
                         onerror="this.src='https://placehold.co/150x150?text=Foto';">
                    <div style="font-size: 0.9rem; font-weight: 600; color: #3b82f6;">
                        <i class="fas fa-pen"></i> Ubah Foto
                    </div>
                    <small class="text-muted">Klik kotak ini untuk upload</small>
                </label>
                <input type="file" name="foto" id="fotoInput" style="display: none;" accept="image/*" onchange="previewFile(this)">

                <div class="alert alert-light border small text-center text-muted mb-0">
                    Format: JPG, PNG. Maks: 2MB.
                </div>
            </div>

            <div class="form-card">
                <h6 class="section-title"><i class="fas fa-key"></i> Info Akun</h6>
                
                <div class="mb-2">
                    <label class="form-label">NIY (Username Login) <span class="text-danger">*</span></label>
                    <input type="number" name="niy" class="form-control bg-light" value="{{ old('niy', $guru->niy) }}" required>
                    <small class="text-muted" style="font-size: 0.75rem;">Digunakan untuk login ke sistem.</small>
                </div>
                
                <div class="mb-2">
                    <label class="form-label">NUPTK (Opsional)</label>
                    <input type="number" name="nuptk" class="form-control" value="{{ old('nuptk', $guru->nuptk) }}">
                </div>

                <div class="mb-0">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $guru->email) }}">
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm">
                <i class="fas fa-save me-2"></i> Simpan Perubahan
            </button>
        </div>

        {{-- KOLOM KANAN (BIODATA LENGKAP) --}}
        <div class="content-wrapper">
            
            {{-- Error Validation Alert --}}
            @if ($errors->any())
                <div class="alert alert-danger mb-4 border-0 shadow-sm" style="border-left: 4px solid #dc3545;">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-card mb-4">
                <h6 class="section-title"><i class="fas fa-user-edit"></i> Identitas & Biodata</h6>

                <div class="mb-3">
                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap', $guru->nama_lengkap) }}" required style="font-size: 1.1rem; font-weight: 600;">
                </div>

                <div class="form-row-grid">
                    <div>
                        <label class="form-label">Gelar Depan</label>
                        <input type="text" name="gelar_depan" class="form-control" value="{{ old('gelar_depan', $guru->gelar_depan) }}" placeholder="Dr.">
                    </div>
                    <div>
                        <label class="form-label">Gelar Belakang</label>
                        <input type="text" name="gelar_belakang" class="form-control" value="{{ old('gelar_belakang', $guru->gelar_belakang) }}" placeholder="S.Pd">
                    </div>
                </div>

                <div class="form-row-grid">
                    <div>
                        <label class="form-label">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $guru->tempat_lahir) }}" required>
                    </div>
                    <div>
                        <label class="form-label">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', $guru->tanggal_lahir) }}" required>
                    </div>
                </div>

                <div class="form-row-grid">
                    <div>
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select" required>
                            <option value="L" {{ old('jenis_kelamin', $guru->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin', $guru->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">No. HP / WhatsApp</label>
                        <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $guru->no_hp) }}">
                    </div>
                </div>

                <div class="mb-0">
                    <label class="form-label">Alamat Lengkap</label>
                    <textarea name="alamat" class="form-control" rows="3">{{ old('alamat', $guru->alamat) }}</textarea>
                </div>
            </div>

            <div class="form-card">
                <h6 class="section-title"><i class="fas fa-briefcase"></i> Kepegawaian & Pendidikan</h6>

                <div class="form-row-grid">
                    <div>
                        <label class="form-label">Status Kepegawaian</label>
                        <select name="status_kepegawaian" class="form-select" required>
                            <option value="GTY" {{ old('status_kepegawaian', $guru->status_kepegawaian) == 'GTY' ? 'selected' : '' }}>GTY</option>
                            <option value="PTY" {{ old('status_kepegawaian', $guru->status_kepegawaian) == 'PTY' ? 'selected' : '' }}>PTY</option>
                            <option value="GTTY" {{ old('status_kepegawaian', $guru->status_kepegawaian) == 'GTTY' ? 'selected' : '' }}>GTTY</option>
                            <option value="PTTY" {{ old('status_kepegawaian', $guru->status_kepegawaian) == 'PTTY' ? 'selected' : '' }}>PTTY</option>
                            <option value="HONORER" {{ old('status_kepegawaian', $guru->status_kepegawaian) == 'HONORER' ? 'selected' : '' }}>Honorer</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Tugas Tambahan</label>
                        <input type="text" name="tugas_tambahan" class="form-control" value="{{ old('tugas_tambahan', $guru->tugas_tambahan) }}" placeholder="Wali Kelas">
                    </div>
                </div>

                <div class="form-row-grid">
                    <div>
                        <label class="form-label">Pendidikan Terakhir</label>
                        <select name="pendidikan_terakhir" class="form-select" required>
                            <option value="S1" {{ old('pendidikan_terakhir', $guru->pendidikan_terakhir) == 'S1' ? 'selected' : '' }}>S1</option>
                            <option value="S2" {{ old('pendidikan_terakhir', $guru->pendidikan_terakhir) == 'S2' ? 'selected' : '' }}>S2</option>
                            <option value="S3" {{ old('pendidikan_terakhir', $guru->pendidikan_terakhir) == 'S3' ? 'selected' : '' }}>S3</option>
                            <option value="D3" {{ old('pendidikan_terakhir', $guru->pendidikan_terakhir) == 'D3' ? 'selected' : '' }}>D3</option>
                            <option value="SMA" {{ old('pendidikan_terakhir', $guru->pendidikan_terakhir) == 'SMA' ? 'selected' : '' }}>SMA/Sederajat</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Tahun Lulus</label>
                        <input type="number" name="tahun_lulus" class="form-control" value="{{ old('tahun_lulus', $guru->tahun_lulus) }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">TMT Sekolah</label>
                    <input type="date" name="tmt_sekolah" class="form-control" value="{{ old('tmt_sekolah', $guru->tmt_sekolah) }}">
                </div>

                <div class="form-row-grid">
                    <div>
                        <label class="form-label">Masa Kerja (SD)</label>
                        <input type="text" name="masa_kerja_sd" class="form-control" value="{{ old('masa_kerja_sd', $guru->masa_kerja_sd) }}">
                    </div>
                    <div>
                        <label class="form-label">Masa Kerja (Total)</label>
                        <input type="text" name="masa_kerja_total" class="form-control" value="{{ old('masa_kerja_total', $guru->masa_kerja_total) }}">
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>

<script>
    // Script Preview Image (Sama dengan Index)
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
