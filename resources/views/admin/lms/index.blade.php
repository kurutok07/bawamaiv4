@extends('layouts.admin')

@section('title', 'Manajemen LMS')

@section('content')
<style>
    /* --- STYLE KHUSUS UNTUK CARD LMS --- */
    .lms-card {
        transition: transform 0.2s, box-shadow 0.2s;
        cursor: pointer;
        position: relative;
        border: 1px solid #e3e6f0;
        border-radius: 15px;
        overflow: hidden;
        background: white;
    }
    
    .lms-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        border-color: #4e73df;
    }

    /* Area yang bisa diklik (Cover + Judul) */
    .clickable-area {
        padding: 20px;
        text-align: center;
        height: 100%;
        text-decoration: none !important;
        color: inherit;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    /* Icon Besar */
    .lms-icon {
        font-size: 3.5rem;
        margin-bottom: 15px;
    }

    /* Warna Icon */
    .icon-folder { color: #f6c23e; } /* Kuning */
    .icon-pdf { color: #e74a3b; }    /* Merah */
    .icon-video { color: #36b9cc; }  /* Cyan */
    .icon-link { color: #858796; }   /* Abu */

    /* Modal Styles */
    .modal-preview-content {
        height: 75vh;
    }
    iframe.preview-frame {
        width: 100%;
        height: 100%;
        border: none;
        border-radius: 8px;
    }
    
    /* Tombol Aksi Mini (Edit/Delete) */
    .action-buttons {
        position: absolute;
        top: 10px;
        right: 10px;
        display: flex;
        gap: 5px;
        z-index: 10;
    }

    .btn-mini {
        background: rgba(255, 255, 255, 0.9);
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        border: 1px solid #ddd;
        cursor: pointer;
    }
    
    .btn-edit:hover { background: #36b9cc; color: white !important; }
    .btn-delete:hover { background: #e74a3b; color: white !important; }

    /* Badge Kelas (Baru) */
    .badge-audiens {
        position: absolute;
        top: 10px;
        left: 10px;
        font-size: 0.7rem;
        padding: 4px 8px;
        border-radius: 12px;
        font-weight: 700;
        z-index: 9;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .badge-umum { background: #eef2ff; color: #4e73df; border: 1px solid #c7d2fe; }
    .badge-kelas { background: #ecfdf5; color: #10b981; border: 1px solid #a7f3d0; }
</style>

<div class="container-fluid">
    
    {{-- HEADER & BREADCRUMB --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Manajemen LMS</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mt-2 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('lms-items.index') }}">Home</a></li>
                    
                    @if($currentFolder)
                        @if($currentFolder->parent)
                             <li class="breadcrumb-item">...</li>
                             <li class="breadcrumb-item"><a href="{{ route('lms-items.index', ['parent_id' => $currentFolder->parent_id]) }}">{{ $currentFolder->parent->title }}</a></li>
                        @endif
                        <li class="breadcrumb-item active">{{ $currentFolder->title }}</li>
                    @endif
                </ol>
            </nav>
        </div>
        <div class="col-md-6 text-end">
            {{-- Logic Tombol Kembali Berdasarkan Role --}}
            <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>

    </div>

    {{-- Tombol Tambah --}}
    <div class="d-flex w-100 justify-content-between mb-3 align-items-center">
        <div>
            @if(Auth::user()->role == 'guru')
                <span class="badge bg-info text-white p-2">
                    <i class="fas fa-user-tie me-1"></i> Mode Guru
                </span>
            @endif
        </div>
        <a href="{{ route('lms-items.create', ['parent_id' => $currentFolder ? $currentFolder->id : '']) }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Materi Baru
        </a>
    </div>
        

    {{-- CONTAINER PUTIH UTAMA --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                {{ $currentFolder ? 'Isi Folder: ' . $currentFolder->title : 'File Manager (Root)' }}
            </h6>
            @if(Auth::user()->role == 'guru')
                <small class="text-muted">Menampilkan materi Umum & Kelas Ajar Anda.</small>
            @endif
        </div>
        <div class="card-body">
            
            <div class="row">
                
                {{-- TOMBOL KEMBALI (Jika di dalam folder) --}}
                @if($currentFolder)
                <div class="col-xl-2 col-md-3 col-6 mb-4">
                    <div class="card lms-card shadow-sm h-100 bg-light" style="border-style: dashed;">
                        <a href="{{ route('lms-items.index', ['parent_id' => $currentFolder->parent_id]) }}" class="clickable-area">
                            <i class="fas fa-arrow-up lms-icon text-secondary"></i>
                            <h6 class="font-weight-bold text-secondary">...Kembali</h6>
                        </a>
                    </div>
                </div>
                @endif

                {{-- LOOPING ITEMS --}}
                @forelse($items as $item)
                    <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                        <div class="card lms-card shadow-sm h-100">
                            
                            {{-- BADGE AUDIENS (BARU) --}}
                            @if($item->kelas)
                                <div class="badge-audiens badge-kelas" title="Hanya untuk siswa kelas ini">
                                    <i class="fas fa-lock me-1"></i> {{ $item->kelas->nama_kelas }}
                                </div>
                            @else
                                <div class="badge-audiens badge-umum" title="Dapat dilihat semua siswa">
                                    <i class="fas fa-globe me-1"></i> Umum
                                </div>
                            @endif

                            {{-- ACTION BUTTONS --}}
                            <div class="action-buttons">
                                <a href="{{ route('lms-items.edit', $item->id) }}" class="btn-mini btn-edit text-info" title="Edit">
                                    <i class="fas fa-pen fa-xs"></i>
                                </a>
                                <form action="{{ route('lms-items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus item ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-mini btn-delete text-danger" title="Hapus" style="border:none;">
                                        <i class="fas fa-trash-alt fa-xs"></i>
                                    </button>
                                </form>
                            </div>

                            {{-- CONTENT CLICKABLE --}}
                            @if($item->type == 'folder')
                                <a href="{{ route('lms-items.index', ['parent_id' => $item->id]) }}" class="clickable-area">
                                    <i class="fas fa-folder lms-icon icon-folder"></i>
                                    <h6 class="font-weight-bold text-dark mb-0">{{ Str::limit($item->title, 20) }}</h6>
                                    <small class="text-muted">{{ $item->children()->count() }} items</small>
                                </a>
                            @else
                                <div class="clickable-area" 
                                     onclick="openPreview('{{ $item->title }}', '{{ $item->type }}', '{{ $item->type == 'file' ? asset($item->content) : $item->content }}')">
                                    
                                    @if($item->type == 'video')
                                        <i class="fas fa-play-circle lms-icon icon-video"></i>
                                    @elseif($item->type == 'file')
                                        <i class="fas fa-file-pdf lms-icon icon-pdf"></i>
                                    @else
                                        <i class="fas fa-link lms-icon icon-link"></i>
                                    @endif
                                    
                                    <h6 class="font-weight-bold text-dark mb-0">{{ Str::limit($item->title, 20) }}</h6>
                                    <small class="text-muted text-uppercase">{{ $item->type }}</small>
                                </div>
                            @endif

                        </div>
                    </div>
                @empty
                    @if(!$currentFolder)
                        <div class="col-12 text-center py-5 text-muted">
                            <i class="fas fa-folder-open fa-3x mb-3 text-gray-300"></i>
                            <p>Belum ada materi. Klik tombol <strong>Tambah</strong> di atas.</p>
                        </div>
                    @endif
                @endforelse

            </div>
        </div>
    </div>
</div>

{{-- MODAL PREVIEW (Tetap Sama) --}}
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="previewTitle">Preview</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0 modal-preview-content">
                <iframe id="previewIframe" class="preview-frame" src="" allowfullscreen></iframe>
                <div id="linkFallback" class="text-center p-5 d-none">
                    <i class="fas fa-external-link-alt fa-3x text-primary mb-3"></i>
                    <h4>Ini adalah Tautan Eksternal</h4>
                    <p>Website ini tidak mengizinkan preview di dalam frame.</p>
                    <a href="#" id="externalLinkBtn" target="_blank" class="btn btn-primary">Buka di Tab Baru</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function openPreview(title, type, contentUrl) {
        document.getElementById('previewTitle').innerText = title;
        var iframe = document.getElementById('previewIframe');
        var fallback = document.getElementById('linkFallback');
        var linkBtn = document.getElementById('externalLinkBtn');

        iframe.classList.remove('d-none');
        fallback.classList.add('d-none');
        iframe.src = ''; 

        if(type === 'link') {
            iframe.src = contentUrl;
        } else {
            iframe.src = contentUrl;
        }
        $('#previewModal').modal('show');
    }

    $('#previewModal').on('hidden.bs.modal', function () {
        document.getElementById('previewIframe').src = '';
    });
</script>

@endsection