@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4" style="max-width: 800px; margin: 0 auto;">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Tambah Item di: {{ $parent ? $parent->title : 'Halaman Utama (Root)' }}
            </h6>
        </div>
        <div class="card-body">
            
            <form action="{{ route('lms-items.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                {{-- INPUT HIDDEN: PARENT ID --}}
                <input type="hidden" name="parent_id" value="{{ $parentId }}">

                {{-- Judul --}}
                <div class="form-group">
                    <label>Judul Materi / Folder</label>
                    <input type="text" name="title" class="form-control" required placeholder="Contoh: Qurana Jilid 1">
                </div>

                {{-- Pilihan Tipe --}}
                <div class="form-group">
                    <label>Tipe Item</label>
                    <select name="type" id="typeSelector" class="form-control" required>
                        <option value="folder">Folder (Bisa diisi sub-materi lagi)</option>
                        <option value="file">File PDF / Dokumen</option>
                        <option value="video">Video Youtube</option>
                        <option value="link">Link Eksternal (Website lain)</option>
                    </select>
                </div>

                {{-- Input Cover Image (Opsional) --}}
                <div class="form-group">
                    <label>Cover / Icon (Opsional)</label>
                    <input type="file" name="cover_image" class="form-control p-1">
                    <small class="text-muted">Jika kosong akan menggunakan icon default.</small>
                </div>

                {{-- DYNAMIC FIELDS --}}
                
                {{-- 1. Input untuk File --}}
                <div id="input-file" class="form-group d-none">
                    <label>Upload File (PDF)</label>
                    <input type="file" name="file_upload" class="form-control p-1">
                </div>

                {{-- 2. Input untuk Link/Video --}}
                <div id="input-url" class="form-group d-none">
                    <label>Masukkan URL (Youtube / Link)</label>
                    <input type="url" name="url_link" class="form-control" placeholder="https://...">
                </div>

                <hr>
                <a href="{{ url()->previous() }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
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

        if (type === 'file') {
            inputFile.classList.remove('d-none');
        } else if (type === 'video' || type === 'link') {
            inputUrl.classList.remove('d-none');
        }
        // Jika folder, tidak perlu input apa-apa selain judul & cover
    });
</script>

@endsection