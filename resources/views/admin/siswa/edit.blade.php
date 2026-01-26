@extends('layouts.admin')

@section('title', 'Edit Siswa')

@section('content')
<div class="container-fluid">
    
    {{-- Tombol Kembali --}}
    <div class="mb-3">
        <a href="{{ route('siswa.index') }}" class="btn btn-link text-decoration-none ps-0">
            <i class="fas fa-arrow-left"></i> Kembali ke Data Siswa
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-user-edit me-1"></i> Edit Data Siswa: {{ $siswa->nama_lengkap }}
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('siswa.update', $siswa->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    {{-- KOLOM KIRI: Data Akademik & Pribadi --}}
                    <div class="col-md-6 border-end-md">
                        <h6 class="text-primary fw-bold mb-3">Data Pribadi Siswa</h6>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">NIS <span class="text-danger">*</span></label>
                                <input type="text" name="nis" class="form-control @error('nis') is-invalid @enderror" value="{{ old('nis', $siswa->nis) }}">
                                @error('nis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">NISN</label>
                                <input type="text" name="nisn" class="form-control" value="{{ old('nisn', $siswa->nisn) }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap', $siswa->nama_lengkap) }}">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $siswa->tempat_lahir) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', $siswa->tanggal_lahir) }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="jenis_kelamin" class="form-control">
                                <option value="L" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                    </div>

                    {{-- KOLOM KANAN: Data Wali & Foto --}}
                    <div class="col-md-6 ps-md-4">
                        <h6 class="text-primary fw-bold mb-3">Data Wali & Alamat</h6>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Nama Wali</label>
                            <input type="text" name="nama_wali" class="form-control" value="{{ old('nama_wali', $siswa->nama_wali) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">No. HP Wali</label>
                            <input type="text" name="no_hp_wali" class="form-control" value="{{ old('no_hp_wali', $siswa->no_hp_wali) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="3">{{ old('alamat', $siswa->alamat) }}</textarea>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Update Foto</label>
                            <div class="d-flex align-items-center gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div>
                                        @php
                                            // LOGIKA:
                                            // 1. Cek apakah di database kolom 'foto' terisi.
                                            // 2. Jika ya, gunakan asset storage.
                                            // 3. Jika tidak, gunakan gambar default 'assets/no-image.jpg'.
                                            $imgSrc = $siswa->foto ? asset('storage/' . $siswa->foto) : asset('assets/no-image.jpg');
                                        @endphp

                                        <img src="{{ $imgSrc }}" 
                                            alt="Foto Siswa" 
                                            width="80" height="80" 
                                            class="rounded-circle object-fit-cover shadow-sm border"
                                            {{-- Onerror: Jaga-jaga jika file fisik di storage terhapus, otomatis balik ke default --}}
                                            onerror="this.onerror=null; this.src='{{ asset('assets/no-image.jpg') }}';">
                                    </div>
                                    <div class="flex-grow-1">
                                        <input type="file" name="foto" class="form-control">
                                        <small class="text-muted fst-italic">Kosongkan jika tidak ingin mengubah foto.</small>
                                    </div>
                                </div>
                            
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a href="{{ route('siswa.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> Update Data Siswa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Helper agar garis pemisah hanya muncul di desktop */
    @media (min-width: 768px) {
        .border-end-md {
            border-right: 1px solid #e3e6f0;
        }
    }
</style>
@endsection