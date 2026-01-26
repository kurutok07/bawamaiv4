@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4" style="max-width: 800px; margin: 0 auto;">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Edit Item: {{ $item->title }}</h6>
        </div>
        <div class="card-body">
            
            <form action="{{ route('lms-items.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT') {{-- PENTING: Untuk method Update --}}

                {{-- Judul --}}
                <div class="form-group">
                    <label>Judul Materi / Folder</label>
                    <input type="text" name="title" class="form-control" required value="{{ old('title', $item->title) }}">
                </div>

                {{-- Tipe Item (Read Only / Disabled) --}}
                <div class="form-group">
                    <label>Tipe Item</label>
                    <input type="text" class="form-control" value="{{ ucfirst($item->type) }}" disabled style="background-color: #eaecf4;">
                    <small class="text-muted">Tipe item tidak dapat diubah saat edit.</small>
                </div>

                {{-- Cover Image --}}
                <div class="form-group">
                    <label>Ganti Cover (Opsional)</label>
                    <input type="file" name="cover_image" class="form-control p-1">
                    
                    @if($item->cover_image)
                        <div class="mt-2">
                            <small>Cover Saat Ini:</small><br>
                            <img src="{{ asset($item->cover_image) }}" alt="Cover" style="height: 80px; border-radius: 10px; border: 1px solid #ddd;">
                        </div>
                    @endif
                </div>

                <hr>

                {{-- DYNAMIC FIELDS BERDASARKAN TIPE --}}
                
                {{-- KASUS 1: FILE PDF --}}
                @if($item->type == 'file')
                    <div class="form-group">
                        <label>Ganti File PDF (Opsional)</label>
                        <input type="file" name="file_upload" class="form-control p-1">
                        
                        <div class="alert alert-info mt-2 py-2">
                            <small><i class="fas fa-file-pdf"></i> File saat ini: 
                                <a href="{{ asset($item->content) }}" target="_blank">Lihat File Lama</a>
                            </small>
                        </div>
                    </div>
                @endif

                {{-- KASUS 2: VIDEO / LINK --}}
                @if($item->type == 'video' || $item->type == 'link')
                    <div class="form-group">
                        <label>URL / Link (Youtube atau Website)</label>
                        <input type="url" name="url_link" class="form-control" value="{{ $item->content }}">
                        <small class="text-muted">Edit link di atas jika ingin mengganti tujuan.</small>
                    </div>
                @endif

                <div class="mt-4">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection