@extends('layouts.admin')

@section('title', 'Data Siswa')

@section('content')

{{-- Header Page --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 text-gray-800 mb-0">Data Siswa</h1>
        <p class="text-muted small mb-0">Kelola data peserta didik dan orang tua wali.</p>
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
    
    {{-- KOLOM KIRI (Form Tambah) --}}
    {{-- Di HP dia akan lebar penuh (col-12), di Desktop dia 4 kolom (col-lg-4) --}}
    <div class="col-12 col-lg-4 mb-4">
        
        {{-- Card Tambah Siswa --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold"><i class="fas fa-user-plus me-1"></i> Tambah Siswa</h6>
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

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">NIS <span class="text-danger">*</span></label>
                            <input type="text" name="nis" class="form-control" placeholder="12345" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">NISN</label>
                            <input type="text" name="nisn" class="form-control" placeholder="0012...">
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
                            <option value="">-- Pilih --</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>

                    <hr class="my-4 border-secondary-subtle">
                    <p class="text-uppercase text-muted small fw-bold mb-3">Data Wali</p>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Wali</label>
                        <input type="text" name="nama_wali" class="form-control" placeholder="Ayah / Ibu">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">No. HP Wali</label>
                        <input type="text" name="no_hp_wali" class="form-control" placeholder="0812...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat lengkap..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-1"></i> Simpan Data Siswa
                    </button>
                </form>
            </div>
        </div>

        {{-- Card Import Excel --}}
        <div class="card shadow border-left-success">
            <div class="card-body">
                <h6 class="font-weight-bold text-success mb-3"><i class="fas fa-file-excel"></i> Import Siswa</h6>
                <form action="{{ route('siswa.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="input-group mb-2">
                        <input type="file" name="file" class="form-control form-control-sm" required>
                        <button class="btn btn-success btn-sm" type="submit">Upload</button>
                    </div>
                    <a href="{{asset('templates/template_data_siswa.xlsx')}}" download class="small text-muted text-decoration-underline">Download Template Excel</a>
                </form>
            </div>
        </div>
    </div>
    
    {{-- KOLOM KANAN (List Siswa) --}}
    <div class="col-12 col-lg-8">
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
            @foreach($siswas as $siswa)
            <div class="col">
                <div class="card h-100 shadow-sm border-0 position-relative overflow-hidden">
                    {{-- Banner Header Gradient --}}
                    <div style="height: 80px; background: linear-gradient(135deg, #3b82f6, #60a5fa);"></div>
                    
                    <div class="card-body text-center pt-0 position-relative">
                        {{-- Foto Profil --}}
                        <div class="position-relative d-inline-block" style="margin-top: -45px;">
                            @php
                                // LOGIKA PENENTUAN GAMBAR (Sesuai referensi Guru):
                                // 1. Jika ada data foto, gunakan path storage.
                                // 2. Jika tidak ada, gunakan placeholder lokal.
                                $imgSrc = $siswa->foto ? asset('storage/' . $siswa->foto) : asset('img/no-image.png');
                            @endphp

                            <img src="{{ $imgSrc }}" 
                                alt="Foto {{ $siswa->nama_lengkap }}" 
                                class="profile-img"
                                style="width: 90px; height: 90px; border-radius: 50%; object-fit: cover; border: 4px solid white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);"
                                onerror="this.onerror=null; this.src='{{ asset('assets/no-image.jpg') }}';">
                        </div>
                        <h5 class="mt-3 mb-1 fw-bold text-dark text-truncate">{{ $siswa->nama_lengkap }}</h5>
                        <p class="text-muted small mb-2">NIS: {{ $siswa->nis }}</p>

                        <div class="mb-3">
                            @if($siswa->jenis_kelamin == 'L')
                                <span class="badge bg-blue-100 text-primary rounded-pill px-3">
                                    <i class="fas fa-mars"></i> Laki-laki
                                </span>
                            @else
                                <span class="badge bg-pink-100 text-danger rounded-pill px-3">
                                    <i class="fas fa-venus"></i> Perempuan
                                </span>
                            @endif
                        </div>
                        
                        @if($siswa->nama_wali)
                        <div class="bg-light rounded p-2 mb-3">
                            <small class="text-muted d-block text-truncate">
                                <i class="fas fa-user-friends me-1"></i> Wali: {{ Str::limit($siswa->nama_wali, 15) }}
                            </small>
                            <small class="text-muted d-block text-truncate">
                                <i class="fas fa-phone me-1"></i> {{ $siswa->no_hp_wali ?? '-' }}
                            </small>
                        </div>
                        @endif
                    </div>

                    {{-- Action Buttons --}}
                    <div class="card-footer bg-white border-top-0 d-flex justify-content-center gap-2 pb-3">
                        <a href="{{ route('siswa.edit', $siswa->id) }}" class="btn btn-outline-primary btn-sm rounded-circle" title="Edit">
                            <i class="fas fa-pen"></i>
                        </a>
                        
                        <form action="{{ route('siswa.destroy', $siswa->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data {{ $siswa->nama_lengkap }}?');">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm rounded-circle" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        {{-- Pagination (jika ada) --}}
        <div class="mt-4">
            {{-- {{ $siswas->links() }} --}}
        </div>
    </div>

</div>

{{-- CSS Tambahan untuk Badge Background --}}
<style>
    .bg-blue-100 { background-color: #dbeafe !important; }
    .bg-pink-100 { background-color: #fce7f3 !important; }
</style>

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