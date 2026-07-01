@extends('layouts.admin')

@section('title', 'Raport Siswa - ' . $kelas->nama_kelas)

@section('content')
<style>
    /* --- CSS KHUSUS HALAMAN INI --- */
    
    /* Sidebar Folder */
    .folder-list { 
        max-height: 70vh; 
        overflow-y: auto; 
    }
    
    .folder-item { 
        display: flex; 
        align-items: center; 
        padding: 12px 15px;
        border-radius: 8px; 
        transition: all 0.2s ease;
        text-decoration: none !important;
        color: #5a5c69;
        font-weight: 600;
        margin-bottom: 5px;
    }
    
    .folder-item:hover { 
        background-color: #f8f9fc; 
        color: #4e73df; 
        transform: translateX(3px);
    }
    
    .folder-item.active { 
        background-color: #e8f5e9; 
        color: #1b5e20; 
        border-left: 4px solid #2e7d32; 
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .folder-item.active i { color: #2e7d32 !important; }
    
    /* File Card */
    .file-card { 
        transition: transform 0.2s, box-shadow 0.2s; 
        border: 1px solid #eaecf4; 
        border-radius: 12px;
        background: #fff;
    }
    
    .file-card:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 10px 20px rgba(0,0,0,0.1); 
        border-color: #4e73df;
    }
    
    .file-icon-wrapper {
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #fdfdfe;
        border-bottom: 1px solid #f8f9fc;
        border-radius: 12px 12px 0 0;
    }
    
    .file-icon { font-size: 3rem; color: #e74a3b; } /* Merah PDF */
</style>

<div class="container-fluid">
    
    {{-- HEADER: INFO KELAS & NAVIGASI --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 font-weight-bold">E-Raport & Arsip</h1>
            <p class="mb-0 text-muted">
                Kelas: <span class="badge badge-success font-weight-bold text-dark">{{ $kelas->nama_kelas }}</span> 
                &nbsp;|&nbsp; 
                Wali Kelas: <span class="font-weight-bold text-dark">{{ $kelas->waliKelas->nama_lengkap ?? '-' }}</span>
            </p>
        </div>
        
        <div class="d-flex gap-2 mt-3 mt-sm-0">
            {{-- DROPDOWN GANTI KELAS --}}
            <div class="dropdown mr-2">
                <button class="btn btn-white border shadow-sm dropdown-toggle font-weight-bold text-success" type="button" data-toggle="dropdown">
                    <i class="fas fa-history mr-1"></i> Ganti Kelas
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in-up" style="min-width: 250px;">
                    <h6 class="dropdown-header">Riwayat Kelas Anda</h6>
                    @foreach($riwayatKelas as $rk)
                        <a class="dropdown-item d-flex justify-content-between align-items-center {{ $rk->id == $kelas->id ? 'active' : '' }}" 
                           href="{{ route('siswa.raport.show', ['kelas_id' => $rk->id]) }}">
                            <span>{{ $rk->nama_kelas }}</span>
                            <small class="text-muted ml-2">{{ $rk->tahunAjaran->tahun ?? '-' }}</small>
                        </a>
                    @endforeach
                </div>
            </div>

            <a href="{{ route('siswa.raport.index') }}" class="btn btn-secondary shadow-sm font-weight-bold">
                <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        
        {{-- SIDEBAR KIRI: KATEGORI / FOLDER --}}
        <div class="col-lg-3 mb-4">
            <div class="card shadow border-0 h-100">
                <div class="card-header py-3 bg-white border-bottom-0">
                    <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-folder-tree mr-2 text-warning"></i>Direktori Raport</h6>
                </div>
                <div class="card-body p-2 folder-list">
                    
            

                    <hr class="my-2 mx-2">

                    {{-- LOOPING KATEGORI UTAMA --}}
                    @forelse($rootCategories as $cat)
                        @php
                            // Cek Aktif: Jika ID di URL sama dengan ID kategori ini
                            $isActive = (request('folder_id') == $cat->id);
                        @endphp

                        <a href="{{ route('siswa.raport.show', ['kelas_id' => $kelas->id, 'folder_id' => $cat->id]) }}" 
                           class="folder-item {{ $isActive ? 'active' : '' }}">
                            {{-- LOGIC ICON (FIXED) --}}
                            <i class="fas fa-folder{{ $isActive ? '-open' : '' }} mr-3 fa-lg text-warning"></i>
                            <span>{{ $cat->nama_kategori }}</span>
                        </a>
                        
                        {{-- LOOPING SUB-FOLDER (ANAK) --}}
                        @if($cat->children->count() > 0)
                            <div class="ml-4 border-left pl-2 mb-2 mt-1">
                                @foreach($cat->children as $child)
                                    @php $isChildActive = (request('folder_id') == $child->id); @endphp
                                    <a href="{{ route('siswa.raport.show', ['kelas_id' => $kelas->id, 'folder_id' => $child->id]) }}" 
                                       class="folder-item small py-1 text-muted {{ $isChildActive ? 'active' : '' }}"
                                       style="padding: 8px 10px;">
                                        <i class="fas fa-folder{{ $isChildActive ? '-open' : '' }} mr-2 text-warning"></i> {{ $child->nama_kategori }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    @empty
                        <div class="text-center text-muted py-4 small">
                            <i class="fas fa-folder-open mb-2 fa-2x text-gray-300"></i><br>
                            Kategori belum dibuat Admin.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- CONTENT KANAN: LIST FILE --}}
        <div class="col-lg-9">
            <div class="card shadow border-0" style="min-height: 500px;">
                <div class="card-header py-3 bg-white d-flex align-items-center justify-content-between border-bottom-0">
                    <h6 class="m-0 font-weight-bold text-dark">
                        @if($selectedFolder)
                            <i class="fas fa-folder-open text-warning mr-2"></i> {{ $selectedFolder->nama_kategori }}
                        @else
                            <i class="fas fa-th-large mr-2 text-success"></i> Semua Dokumen
                        @endif
                    </h6>
                    <span class="badge badge-light border px-3 py-2 text-dark">{{ $files->count() }} File</span>
                </div>
                
                <div class="card-body bg-light">
                    @if($files->isEmpty())
                        {{-- TAMPILAN JIKA KOSONG --}}
                        <div class="text-center py-5 mt-5">
                            <div class="mb-3">
                                <span class="fa-stack fa-3x">
                                    <i class="fas fa-circle fa-stack-2x text-white"></i>
                                    <i class="fas fa-folder-open fa-stack-1x text-gray-300"></i>
                                </span>
                            </div>
                            <h5 class="text-gray-600 font-weight-bold">Folder Kosong</h5>
                            <p class="text-muted small">Wali kelas belum mengunggah dokumen raport di kategori ini.</p>
                        </div>
                    @else
                        {{-- GRID FILE --}}
                        <div class="row">
                            @foreach($files as $file)
                                <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                                    <div class="card file-card h-100">
                                        <div class="file-icon-wrapper">
                                            <i class="fas fa-file-pdf file-icon"></i>
                                        </div>
                                        <div class="card-body text-center p-3 d-flex flex-column">
                                            <h6 class="font-weight-bold text-dark mb-1 text-truncate" title="{{ $file->nama_file }}">
                                                {{ $file->nama_file }}
                                            </h6>
                                            {{-- Tampilkan nama kategori kecil di bawah --}}
                                            <small class="text-success mb-2" style="font-size: 10px;">
                                                <i class="fas fa-tag mr-1"></i> {{ $file->category->nama_kategori ?? 'Umum' }}
                                            </small>
                                            
                                            <small class="text-muted mb-3 d-block">{{ $file->created_at->format('d M Y') }}</small>
                                            
                                            <div class="mt-auto">
                                                <button onclick="previewPdf('{{ asset('storage/' . $file->file_path) }}', '{{ $file->nama_file }}')" 
                                                        class="btn btn-sm btn-success btn-block rounded-pill font-weight-bold shadow-sm">
                                                    <i class="fas fa-eye mr-1"></i> Lihat
                                                </button>
                                                <a href="{{ asset('storage/' . $file->file_path) }}" download 
                                                   class="btn btn-sm btn-light btn-block rounded-pill mt-2 border">
                                                    <i class="fas fa-download mr-1"></i> Unduh
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

{{-- MODAL PREVIEW PDF (FULLSCREEN) --}}
<div class="modal fade" id="pdfModal" tabindex="-1" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-xl" style="height: 95%; max-width: 95%;">
        <div class="modal-content h-100 border-0 shadow-lg">
            <div class="modal-header bg-success text-white py-2 align-items-center">
                <h6 class="modal-title font-weight-bold text-white mb-0" id="pdfTitle">
                    <i class="fas fa-file-pdf mr-2 text-danger"></i> Document Preview
                </h6>
                <button type="button" class="close text-white opacity-100" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0 bg-secondary d-flex align-items-center justify-content-center">
                {{-- IFRAME PDF --}}
                <iframe id="pdfFrame" src="" width="100%" height="100%" style="border:none;"></iframe>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function previewPdf(url, title) {
        // Set Judul Modal
        document.getElementById('pdfTitle').innerHTML = '<i class="fas fa-file-pdf mr-2 text-danger"></i> ' + title;
        // Set Source Iframe
        document.getElementById('pdfFrame').src = url;
        // Tampilkan Modal
        $('#pdfModal').modal('show');
    }

    // Reset iframe saat modal ditutup (Supaya memori bersih)
    $('#pdfModal').on('hidden.bs.modal', function () {
        document.getElementById('pdfFrame').src = "";
    });
</script>
@endsection