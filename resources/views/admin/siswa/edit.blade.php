@extends('layouts.admin')

@section('title', 'Edit Data Siswa')

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
            
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('siswa.update', $siswa->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    {{-- === KOLOM KIRI: Identitas Siswa === --}}
                    <div class="col-md-6 border-end-md">
                        <h6 class="text-primary fw-bold mb-3"><i class="fas fa-id-card me-1"></i> Data Identitas</h6>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">NISN <span class="text-danger">*</span></label>
                                <input type="text" name="nisn" class="form-control" value="{{ old('nisn', $siswa->nisn) }}" required>
                                <small class="text-muted" style="font-size: 10px">Digunakan untuk login siswa.</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">NIK</label>
                                <input type="text" name="nik" class="form-control" value="{{ old('nik', $siswa->nik) }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap', $siswa->nama_lengkap) }}" required>
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
                            <select name="jenis_kelamin" class="form-control" required>
                                <option value="L" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                    </div>

                    {{-- === KOLOM KANAN: Data Orang Tua & Kelas === --}}
                    <div class="col-md-6 ps-md-4">
                        
                        {{-- Bagian Kelas (Pindah Kelas) --}}
                        <div class="bg-light p-3 rounded border mb-4">
                            <h6 class="text-dark fw-bold mb-2 small text-uppercase">Posisi Kelas (Tahun Ini)</h6>
                            <select name="kelas_id" class="form-select border-primary">
                                <option value="">-- Tidak Masuk Kelas --</option>
                                @foreach($daftarKelas as $kelas)
                                    <option value="{{ $kelas->id }}" 
                                        {{ (isset($currentKelasID) && $currentKelasID == $kelas->id) ? 'selected' : '' }}>
                                        {{ $kelas->nama_kelas }} (Tingkat {{ $kelas->tingkat }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted fst-italic">Ubah pilihan ini untuk memindahkan siswa ke kelas lain.</small>
                        </div>

                        <h6 class="text-primary fw-bold mb-3"><i class="fas fa-user-friends me-1"></i> Data Orang Tua / Wali</h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">Nama Wali</label>
                                <input type="text" name="nama_wali" class="form-control" value="{{ old('nama_wali', $siswa->nama_wali) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">No. HP Wali</label>
                                <input type="text" name="no_hp_wali" class="form-control" value="{{ old('no_hp_wali', $siswa->no_hp_wali) }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="2">{{ old('alamat', $siswa->alamat) }}</textarea>
                        </div>

                        <hr>

                        {{-- Update Foto --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Foto Profil</label>
                            <div class="d-flex align-items-center gap-3">
                                <div>
                                    <img src="{{ $siswa->foto ? asset($siswa->foto) : asset('img/no-image.png') }}" 
     class="profile-img bg-white"
     style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
     onerror="this.src='{{ asset('img/no-image.png') }}'">
     
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" name="foto" class="form-control form-control-sm">
                                    <small class="text-muted d-block mt-1">Biarkan kosong jika tidak ingin mengganti foto.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a href="{{ route('siswa.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    @media (min-width: 768px) {
        .border-end-md {
            border-right: 1px solid #e3e6f0;
        }
    }
</style>
@endsection