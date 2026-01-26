@extends('layouts.admin') {{-- Pastikan layout admin Anda sudah memuat Bootstrap JS --}}

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

    /* Tombol Hapus Kecil di Pojok */
    .btn-delete-mini {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(255, 255, 255, 0.8);
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #e74a3b;
        z-index: 10; /* Agar di atas clickable area */
        transition: all 0.2s;
    }
    .btn-delete-mini:hover {
        background: #e74a3b;
        color: white;
    }

    /* Warna Icon */
    .icon-folder { color: #f6c23e; } /* Kuning */
    .icon-pdf { color: #e74a3b; }    /* Merah */
    .icon-video { color: #36b9cc; }  /* Cyan */
    .icon-link { color: #858796; }   /* Abu */

    /* Modal Styles */
    .modal-preview-content {
        height: 75vh; /* Tinggi Modal */
    }
    iframe.preview-frame {
        width: 100%;
        height: 100%;
        border: none;
        border-radius: 8px;
    }
    /* Update CSS Tombol Mini agar bisa berjejer */
    .action-buttons {
        position: absolute;
        top: 10px;
        right: 10px;
        display: flex;
        gap: 5px; /* Jarak antar tombol */
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
            <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

    </div>
    <div class="d-flex w-100 justify-content-end mb-3">
        <a href="{{ route('lms-items.create', ['parent_id' => $currentFolder ? $currentFolder->id : '']) }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Item
        </a>
    </div>
        

    {{-- CONTAINER PUTIH UTAMA --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary">
                {{ $currentFolder ? 'Isi Folder: ' . $currentFolder->title : 'File Manager (Root)' }}
            </h6>
        </div>
        <div class="card-body">
            
            {{-- GRID SYSTEM --}}
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
                            
                            {{-- TOMBOL DELETE (Pojok Kanan Atas) --}}
                            <div class="action-buttons">
                                    
                                    {{-- 1. Tombol Edit --}}
                                    <a href="{{ route('lms-items.edit', $item->id) }}" class="btn-mini btn-edit text-info" title="Edit">
                                        <i class="fas fa-pen fa-xs"></i>
                                    </a>

                                    {{-- 2. Tombol Delete --}}
                                    <form action="{{ route('lms-items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus item ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-mini btn-delete text-danger" title="Hapus" style="border:none;">
                                            <i class="fas fa-trash-alt fa-xs"></i>
                                        </button>
                                    </form>

                                </div>
                            {{-- LOGIC KLIK: JIKA FOLDER -> BUKA LINK, JIKA FILE -> BUKA MODAL --}}
                            
                            @if($item->type == 'folder')
                                {{-- TYPE: FOLDER (Navigasi Biasa) --}}
                                <a href="{{ route('lms-items.index', ['parent_id' => $item->id]) }}" class="clickable-area">
                                    <i class="fas fa-folder lms-icon icon-folder"></i>
                                    <h6 class="font-weight-bold text-dark mb-0">{{ Str::limit($item->title, 20) }}</h6>
                                    <small class="text-muted">{{ $item->children()->count() }} items</small>
                                </a>

                            @else
                                {{-- TYPE: FILE/VIDEO (Buka Modal Preview) --}}
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
                            <i class="fas fa-folder-open fa-3x mb-3"></i>
                            <p>Belum ada folder atau materi. Silakan tambah baru.</p>
                        </div>
                    @endif
                @endforelse

            </div>
        </div>
    </div>
</div>

{{-- MODAL PREVIEW (Bootstrap Modal) --}}
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
                {{-- IFRAME akan dimuat di sini lewat JS --}}
                <iframe id="previewIframe" class="preview-frame" src="" allowfullscreen></iframe>
                
                {{-- Pesan fallback jika link --}}
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

{{-- JAVASCRIPT UNTUK MODAL --}}
<script>
    function openPreview(title, type, contentUrl) {
        // 1. Set Judul Modal
        document.getElementById('previewTitle').innerText = title;
        
        var iframe = document.getElementById('previewIframe');
        var fallback = document.getElementById('linkFallback');
        var linkBtn = document.getElementById('externalLinkBtn');

        // 2. Reset Tampilan
        iframe.classList.remove('d-none');
        fallback.classList.add('d-none');
        iframe.src = ''; // Clear dulu biar loading kelihatan

        // 3. Logic Berdasarkan Tipe
        if(type === 'link') {
            // Kalau link eksternal, kadang tidak bisa di-iframe (security headers)
            // Jadi kita kasih opsi buka tab baru, TAPI kita coba load dulu
            iframe.src = contentUrl;
            
            // Opsional: Langsung tampilkan tombol buka tab baru buat jaga-jaga
            // fallback.classList.remove('d-none');
            // iframe.classList.add('d-none');
            // linkBtn.href = contentUrl;
        } else {
            // Untuk PDF dan Youtube Embed
            iframe.src = contentUrl;
        }

        // 4. Tampilkan Modal (Menggunakan jQuery Bootstrap bawaan template admin biasanya)
        $('#previewModal').modal('show');
    }

    // Bersihkan iframe saat modal ditutup agar video youtube berhenti main
    $('#previewModal').on('hidden.bs.modal', function () {
        document.getElementById('previewIframe').src = '';
    });
</script>

@endsection