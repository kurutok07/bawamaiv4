@extends('layouts.admin')

@section('title', 'Tambah Materi LMS')

@section('content')
<div class="container-fluid">
    
    {{-- Tombol Kembali --}}
    <div class="mb-3">
        <a href="{{ route('lms-items.index', ['parent_id' => $parentId]) }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4" style="max-width: 800px; margin: 0 auto;">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-plus-circle me-1"></i>
                Tambah Item di: {{ $parent ? $parent->title : 'Halaman Utama (Root)' }}
            </h6>
        </div>
        <div class="card-body">
            
            <form action="{{ route('lms-items.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                {{-- INPUT HIDDEN: PARENT ID --}}
                <input type="hidden" name="parent_id" value="{{ $parentId }}">

                {{-- Judul --}}
                <div class="form-group mb-3">
                    <label class="font-weight-bold small">Judul Materi / Folder <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" required placeholder="Contoh: Modul Matematika Bab 1" autofocus>
                </div>

                {{-- TARGET AUDIENS (BARU) --}}
                <div class="form-group mb-4 p-3 bg-light border rounded">
                    <label class="font-weight-bold text-dark small"><i class="fas fa-users me-1"></i> Target Audiens (Siapa yang bisa melihat?)</label>
                    <select name="kelas_id" class="form-control">
                        <option value="">Semua Siswa (Publik / Umum)</option>
                        
                        @foreach($daftarKelas as $k)
                            <option value="{{ $k->id }}">Khusus Kelas: {{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted d-block mt-1">
                        @if(Auth::user()->role == 'guru')
                            <i class="fas fa-info-circle"></i> Anda hanya dapat memilih kelas yang Anda ajar saat ini.
                        @else
                            <i class="fas fa-info-circle"></i> Kosongkan jika materi ini bersifat umum untuk seluruh sekolah.
                        @endif
                    </small>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        {{-- Pilihan Tipe --}}
                        <div class="form-group mb-3">
                            <label class="font-weight-bold small">Tipe Item <span class="text-danger">*</span></label>
                            <select name="type" id="typeSelector" class="form-control" required>
                                <option value="folder">Folder (Bisa diisi sub-materi lagi)</option>
                                <option value="file">File PDF / Dokumen</option>
                                <option value="video">Video Youtube</option>
                                <option value="link">Link Eksternal (Website lain)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        {{-- Input Cover Image (Opsional) --}}
                        <div class="form-group mb-3">
                            <label class="font-weight-bold small">Cover / Icon (Opsional)</label>
                            <input type="file" name="cover_image" class="form-control form-control-sm">
                            <small class="text-muted" style="font-size: 10px">Format: JPG/PNG. Max: 2MB.</small>
                        </div>
                    </div>
                </div>

                <hr>

                {{-- DYNAMIC FIELDS --}}
                
                {{-- 1. Input untuk File --}}
                <div id="input-file" class="form-group d-none bg-white border p-3 rounded">
                    <label class="font-weight-bold text-primary small">Upload File (PDF)</label>
                    <input type="file" name="file_upload" class="form-control">
                    <small class="text-danger">* Wajib diisi untuk tipe File. Maksimal 10MB.</small>
                </div>

                {{-- 2. Input untuk Link/Video --}}
                <div id="input-url" class="form-group d-none bg-white border p-3 rounded">
                    <label class="font-weight-bold text-primary small">Masukkan URL (Youtube / Link Website)</label>
                    <input type="url" name="url_link" class="form-control" placeholder="https://www.youtube.com/watch?v=...">
                    <small class="text-danger">* Pastikan link diawali dengan https://</small>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> Simpan Materi
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

{{-- SCRIPT SEDERHANA UNTUK GANTI TAMPILAN INPUT --}}
<script>
    document.getElementById('typeSelector').addEventListener('change', function() {
        var type = this.value;
        var inputFile = document.getElementById('input-file');
        var inputUrl = document.getElementById('input-url');

        // Reset display
        inputFile.classList.add('d-none');
        inputUrl.classList.add('d-none');

        // Reset required attribute agar tidak error saat submit tipe lain
        document.querySelector('input[name="file_upload"]').removeAttribute('required');
        document.querySelector('input[name="url_link"]').removeAttribute('required');

        if (type === 'file') {
            inputFile.classList.remove('d-none');
            document.querySelector('input[name="file_upload"]').setAttribute('required', 'required');
        } else if (type === 'video' || type === 'link') {
            inputUrl.classList.remove('d-none');
            document.querySelector('input[name="url_link"]').setAttribute('required', 'required');
        }
        // Jika folder, tidak perlu input apa-apa selain judul & cover
    });
</script>

@endsection