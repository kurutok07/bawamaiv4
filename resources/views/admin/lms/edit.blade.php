@extends('layouts.admin')

@section('title', 'Edit Materi LMS')

@section('content')
<div class="container-fluid">
    
    {{-- Tombol Kembali --}}
    <div class="mb-3">
        <a href="{{ route('lms-items.index', ['parent_id' => $item->parent_id]) }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4" style="max-width: 800px; margin: 0 auto;">
        <div class="card-header py-3 bg-primary text-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-edit me-1"></i> Edit Item: {{ $item->title }}
            </h6>
        </div>
        <div class="card-body">
            
            <form action="{{ route('lms-items.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT') 

                {{-- Judul --}}
                <div class="form-group mb-3">
                    <label class="font-weight-bold small">Judul Materi / Folder <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" required value="{{ old('title', $item->title) }}">
                </div>

                {{-- TARGET AUDIENS (BARU) --}}
                <div class="form-group mb-4 p-3 bg-light border rounded">
                    <label class="font-weight-bold text-dark small"><i class="fas fa-users me-1"></i> Target Audiens</label>
                    <select name="kelas_id" class="form-control">
                        <option value="" {{ is_null($item->kelas_id) ? 'selected' : '' }}>Semua Siswa (Publik / Umum)</option>
                        
                        @foreach($daftarKelas as $k)
                            <option value="{{ $k->id }}" {{ $item->kelas_id == $k->id ? 'selected' : '' }}>
                                Khusus Kelas: {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted d-block mt-1">
                        @if(Auth::user()->role == 'guru')
                            <i class="fas fa-info-circle"></i> Hanya menampilkan kelas yang Anda ajar.
                        @else
                            <i class="fas fa-info-circle"></i> Ubah jika ingin membatasi atau membuka akses materi ini.
                        @endif
                    </small>
                </div>

                {{-- Tipe Item (Read Only) --}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold small">Tipe Item</label>
                            <input type="text" class="form-control text-uppercase font-weight-bold" value="{{ $item->type }}" disabled style="background-color: #eaecf4; color: #6e707e;">
                            <small class="text-muted" style="font-size: 10px">Tipe tidak dapat diubah.</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        {{-- Cover Image --}}
                        <div class="form-group mb-3">
                            <label class="font-weight-bold small">Ganti Cover (Opsional)</label>
                            <input type="file" name="cover_image" class="form-control form-control-sm">
                            
                            @if($item->cover_image)
                                <div class="mt-2 p-2 border rounded bg-light d-inline-block">
                                    <small class="d-block mb-1 text-muted">Cover Saat Ini:</small>
                                    <img src="{{ asset($item->cover_image) }}" alt="Cover" style="height: 60px; border-radius: 5px;">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <hr>

                {{-- DYNAMIC FIELDS BERDASARKAN TIPE --}}
                
                {{-- KASUS 1: FILE PDF --}}
                @if($item->type == 'file')
                    <div class="form-group bg-white border p-3 rounded">
                        <label class="font-weight-bold text-primary small">Ganti File PDF (Opsional)</label>
                        <input type="file" name="file_upload" class="form-control">
                        
                        <div class="alert alert-info mt-3 py-2 mb-0 d-flex align-items-center">
                            <i class="fas fa-file-pdf fa-2x me-3"></i>
                            <div>
                                <small class="d-block font-weight-bold">File Saat Ini:</small>
                                <a href="{{ asset($item->content) }}" target="_blank" class="text-decoration-none">Klik untuk melihat file lama</a>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- KASUS 2: VIDEO / LINK --}}
                @if($item->type == 'video' || $item->type == 'link')
                    <div class="form-group bg-white border p-3 rounded">
                        <label class="font-weight-bold text-primary small">URL / Link (Youtube atau Website)</label>
                        <input type="url" name="url_link" class="form-control" value="{{ $item->content }}">
                        <small class="text-muted">Edit link di atas jika ingin mengganti tujuan.</small>
                    </div>
                @endif

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('lms-items.index', ['parent_id' => $item->parent_id]) }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection