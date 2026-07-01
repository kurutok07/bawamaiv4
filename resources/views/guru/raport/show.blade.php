@extends('layouts.admin')

@section('content')
<style>
    /* Styling Tabel & Animasi */
    .table-hover tbody tr:hover {
        background-color: #f8f9fc;
        transform: translateY(-2px);
        transition: all 0.2s ease;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    .avatar-initial {
        width: 40px; height: 40px;
        background: #4e73df; color: #fff;
        border-radius: 50%; display: flex;
        align-items: center; justify-content: center;
        font-weight: bold; margin-right: 15px;
        font-size: 1.1rem;
    }
    /* Agar Dropdown tidak kepotong di tabel responsif */
    .table-responsive {
        overflow: visible !important; /* PENTING: Biar dropdown bisa keluar */
        min-height: 300px; /* Jaga-jaga biar ada ruang ke bawah */
    }
    
    /* PDF Viewer Full Height */
    #pdfViewerIframe { height: 75vh; width: 100%; border: none; }
</style>

<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Dokumen Siswa</h1>
            <p class="mb-0 small text-muted">
                Kelas <span class="font-weight-bold text-primary">{{ $kelas->nama_kelas }}</span> &bull; 
                TA {{ $kelas->tahunAjaran->tahun }}
            </p>
        </div>
        <a href="{{ route('guru.raport.index') }}" class="btn btn-light text-primary btn-sm shadow-sm font-weight-bold border">
            <i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-left-success shadow-sm alert-dismissible fade show rounded-lg">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="card shadow-sm mb-4 border-0">
        {{-- TABS NAVIGASI --}}
        <div class="card-header border-bottom-0 pb-0 bg-white pt-4 px-4">
            <ul class="nav nav-tabs border-bottom-0" id="folderTabs">
                @forelse($rootCategories as $folder)
                    <li class="nav-item">
                        <a class="nav-link border-0 pb-3 {{ ($selectedFolder && $selectedFolder->id == $folder->id) ? 'active font-weight-bold border-bottom-primary text-primary' : 'text-muted' }}" 
                           style="{{ ($selectedFolder && $selectedFolder->id == $folder->id) ? 'border-bottom: 3px solid #4e73df !important;' : '' }}"
                           href="{{ route('guru.raport.show', ['kelas_id' => $kelas->id, 'folder_id' => $folder->id]) }}">
                            @if($folder->type == 'folder') <i class="fas fa-folder {{ ($selectedFolder && $selectedFolder->id == $folder->id) ? 'text-primary' : 'text-warning' }} mr-1"></i> @endif
                            {{ $folder->nama_kategori }}
                        </a>
                    </li>
                @empty
                    <li class="nav-item"><span class="nav-link disabled">Belum ada kategori</span></li>
                @endforelse
            </ul>
        </div>

        <div class="card-body px-4 pb-4">
            
            @if($selectedFolder)
                
                {{-- TOOLBAR (SEARCH & FILTER) --}}
                <div class="row mb-4 align-items-center">
                    {{-- Info Folder --}}
                    <div class="col-md-5 mb-2 mb-md-0">
                        <div class="small text-secondary bg-gray-100 p-2 rounded d-inline-block">
                            <i class="fas fa-info-circle mr-1"></i> Mode: <strong>{{ $selectedFolder->type == 'folder' ? 'Multi File' : 'Single File' }}</strong>
                        </div>
                    </div>

                    {{-- Search & Filter --}}
                    <div class="col-md-7">
                        <div class="d-flex justify-content-md-end">
                            {{-- Input Search --}}
                            <div class="input-group mr-2" style="max-width: 250px;">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-gray-400"></i></span>
                                </div>
                                <input type="text" id="searchInput" class="form-control border-left-0" placeholder="Cari nama siswa...">
                            </div>

                            {{-- Dropdown Filter --}}
                            <select id="filterSelect" class="custom-select" style="max-width: 180px;">
                                <option value="all">Semua Siswa</option>
                                <option value="no_file">❌ Belum Upload</option>
                                <option value="has_file">✅ Sudah Upload</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- TABLE WRAPPER --}}
                <div class="table-responsive"> 
                    <table class="table table-borderless align-middle" id="studentTable">
                        <thead class="text-secondary small text-uppercase font-weight-bold border-bottom">
                            <tr>
                                <th width="5%" class="text-center pb-3">No</th>
                                <th width="35%" class="pb-3">Siswa</th>
                                <th width="40%" class="pb-3">Dokumen Tersedia</th>
                                <th width="20%" class="text-right pb-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kelas->siswas as $index => $siswa)
                            @php 
                                $keyID = $siswa->user_id; 
                                $studentFiles = $files[$keyID] ?? collect([]);
                                $count = $studentFiles->count();
                            @endphp
                            
                            {{-- Tambahkan Data Attribute untuk Filter JS --}}
                            <tr class="border-bottom student-row" data-name="{{ strtolower($siswa->nama_lengkap) }}" data-has-file="{{ $count > 0 ? 1 : 0 }}">
                                <td class="text-center align-middle text-muted row-number">{{ $index + 1 }}</td>
                                
                                {{-- NAMA SISWA --}}
                                <td class="align-middle py-3">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $siswa->foto ? asset($siswa->foto) : asset('img/no-image.png') }}" 
                                        class="profile-img bg-white"
                                        style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                                        onerror="this.src='{{ asset('img/no-image.png') }}'">

                                        <div>
                                            <div class="font-weight-bold text-dark">{{ $siswa->nama_lengkap }}</div>
                                            <div class="small text-muted">NISN: {{ $siswa->nisn }}</div>
                                        </div>
                                    </div>
                                </td>
                                
                                {{-- STATUS FILE / DROPDOWN --}}
                                <td class="align-middle">
                                    @if($count == 0)
                                        {{-- REVISI WARNA: Badge Putih dengan Border & Text Abu --}}
                                        <span class="badge badge-secondary px-3 py-2 font-weight-normal" style="background-color: #eaecf4; color: #5a5c69; border: 1px solid #d1d3e2;">
                                                <i class="fas fa-times-circle mr-1" style="color: #858796;"></i> Belum ada file
                                            </span>
                                        @elseif($count == 1)
                                        {{-- JIKA CUMA 1 FILE --}}
                                        @php $file = $studentFiles->first(); @endphp
                                        <div class="d-flex align-items-center">
                                            <button class="btn btn-sm btn-outline-primary border-0 bg-soft-primary font-weight-bold mr-2 text-left"
                                                    onclick="previewPdf('{{ asset('storage/' . $file->file_path) }}', '{{ $file->nama_file }}')"
                                                    title="{{ $file->judul }}">
                                                <i class="fas fa-file-pdf mr-2"></i> {{ Str::limit($file->judul ?? $file->nama_file, 25) }}
                                            </button>
                                            <form action="{{ route('guru.raport.destroy', $file->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm text-danger btn-link p-0" title="Hapus"><i class="fas fa-times"></i></button>
                                            </form>
                                        </div>
                                    @else
                                        {{-- JIKA BANYAK FILE (DROPDOWN FIX) --}}
                                        <div class="dropdown">
                                            {{-- FIX: data-display="static" agar dropdown keluar dari tabel --}}
                                            <button class="btn btn-sm btn-primary dropdown-toggle shadow-sm px-3" type="button" 
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" 
                                                    data-display="static">
                                                <i class="fas fa-folder-open mr-1"></i> Lihat {{ $count }} Dokumen
                                            </button>
                                            
                                            <div class="dropdown-menu shadow-lg border-0 p-0" style="min-width: 300px;">
                                                <div class="px-3 py-2 bg-light border-bottom font-weight-bold text-gray-700 small">
                                                    Daftar File Siswa
                                                </div>
                                                <div style="max-height: 250px; overflow-y: auto;">
                                                    @foreach($studentFiles as $file)
                                                        <div class="dropdown-item d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                                                            <div class="text-truncate mr-2" style="max-width: 200px; cursor: pointer;" 
                                                                 onclick="previewPdf('{{ asset('storage/' . $file->file_path) }}', '{{ $file->nama_file }}')">
                                                                <i class="fas fa-file-pdf text-danger mr-2"></i> 
                                                                <span class="text-dark small font-weight-bold">{{ $file->judul ?? $file->nama_file }}</span>
                                                            </div>
                                                            <form action="{{ route('guru.raport.destroy', $file->id) }}" method="POST" onsubmit="return confirm('Hapus file ini?')">
                                                                @csrf @method('DELETE')
                                                                <button class="btn btn-sm text-gray-400 hover-danger p-0"><i class="fas fa-trash-alt small"></i></button>
                                                            </form>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>

                                {{-- TOMBOL UPLOAD --}}
                                <td class="text-right align-middle">
                                    <button class="btn btn-light text-primary btn-sm font-weight-bold border shadow-sm hover-scale" 
                                            onclick="openUploadModal({{ $siswa->id }}, '{{ $siswa->nama_lengkap }}')">
                                        <i class="fas fa-cloud-upload-alt mr-1"></i> Upload
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                            
                            {{-- Pesan Jika Tidak Ditemukan (Default Hidden) --}}
                            <tr id="noResultRow" style="display: none;">
                                <td colspan="4" class="text-center py-5">
                                    <div class="text-gray-500">
                                        <i class="fas fa-search fa-2x mb-3"></i>
                                        <p class="mb-0">Siswa tidak ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            @else
                <div class="text-center py-5">
                    <img src="https://illustrations.popsy.co/gray/folder.svg" width="150" class="mb-3 opacity-50">
                    <h5 class="text-gray-600">Belum ada kategori</h5>
                    <p class="small text-muted">Silakan hubungi admin untuk membuat struktur folder.</p>
                </div>
            @endif

        </div>
    </div>
</div>

{{-- MODAL UPLOAD --}}
<div class="modal fade" id="uploadModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div id="loadingState" class="position-absolute w-100 h-100 d-none flex-column align-items-center justify-content-center bg-white rounded" style="z-index: 10; opacity: 0.95;">
                <div class="spinner-border text-primary mb-3" role="status"></div>
                <h6 class="font-weight-bold">Mengupload Dokumen...</h6>
            </div>

            <form action="{{ route('guru.raport.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                @csrf
                <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
                <input type="hidden" name="student_id" id="modalStudentId">

                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title font-weight-bold text-gray-800">Upload Dokumen</h5>
                    <button type="button" class="close" data-dismiss="modal" id="closeBtn">&times;</button>
                </div>
                
                <div class="modal-body">
                    <p class="text-muted small mb-4">Siswa: <strong id="modalStudentName" class="text-dark">-</strong></p>

                    <div class="form-group">
                        <label class="small font-weight-bold text-uppercase text-gray-500">Kategori</label>
                        <select name="raport_category_id" id="categorySelect" class="form-control bg-light border-0" required>
                            @if($selectedFolder)
                                <option value="{{ $selectedFolder->id }}" data-type="{{ $selectedFolder->type }}">
                                    📂 {{ $selectedFolder->nama_kategori }} (Utama)
                                </option>
                                @foreach($selectedFolder->children as $child)
                                    <option value="{{ $child->id }}" data-type="{{ $child->type }}">
                                        &nbsp;&nbsp; ↳ {{ $child->nama_kategori }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-uppercase text-gray-500">File PDF</label>
                        <div class="custom-file">
                            <input type="file" name="file_raport[]" class="custom-file-input" id="customFile" accept="application/pdf" required>
                            <label class="custom-file-label border-0 bg-light text-muted" for="customFile">Pilih file...</label>
                        </div>
                        <small class="form-text mt-2" id="uploadInfoText"></small>
                        <div id="fileListPreview" class="mt-3 d-none">
                            <ul class="list-group list-group-flush small" id="fileListUl" style="max-height: 100px; overflow-y: auto;"></ul>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-dismiss="modal" id="cancelBtn">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Upload Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL PREVIEW PDF --}}
<div class="modal fade" id="pdfPreviewModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered"> 
        <div class="modal-content border-0 shadow-lg h-100">
            <div class="modal-header bg-dark text-white border-0">
                <h6 class="modal-title font-weight-bold text-truncate" id="pdfModalTitle" style="max-width: 80%;">
                    <i class="fas fa-file-pdf mr-2"></i> Preview Dokumen
                </h6>
                <div>
                    <a href="#" id="btnDownloadPdf" class="btn btn-success btn-sm mr-2" download>
                        <i class="fas fa-download"></i> Download
                    </a>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
            </div>
            <div class="modal-body p-0 bg-light text-center">
                <iframe id="pdfViewerIframe" src="" allowfullscreen></iframe>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // --- 1. SEARCH & FILTER LOGIC (NEW) ---
    $(document).ready(function() {
        function filterTable() {
            let searchText = $('#searchInput').val().toLowerCase();
            let filterValue = $('#filterSelect').val(); // 'all', 'no_file', 'has_file'
            let visibleRows = 0;
            let rowNumber = 1;

            $('.student-row').each(function() {
                let name = $(this).data('name');
                let hasFile = $(this).data('has-file'); // 1 atau 0

                let matchName = name.includes(searchText);
                let matchFilter = false;

                if (filterValue === 'all') {
                    matchFilter = true;
                } else if (filterValue === 'no_file') {
                    matchFilter = (hasFile == 0);
                } else if (filterValue === 'has_file') {
                    matchFilter = (hasFile == 1);
                }

                if (matchName && matchFilter) {
                    $(this).show();
                    // Update Nomor Urut biar rapi saat difilter
                    $(this).find('.row-number').text(rowNumber++);
                    visibleRows++;
                } else {
                    $(this).hide();
                }
            });

            // Tampilkan pesan jika tidak ada data
            if (visibleRows === 0) {
                $('#noResultRow').show();
            } else {
                $('#noResultRow').hide();
            }
        }

        // Trigger saat ngetik atau ganti dropdown
        $('#searchInput').on('keyup', filterTable);
        $('#filterSelect').on('change', filterTable);
    });

    // --- 2. PREVIEW PDF ---
    function previewPdf(url, title) {
        $('#pdfModalTitle').html('<i class="fas fa-file-pdf mr-2"></i> ' + title);
        $('#pdfViewerIframe').attr('src', url);
        $('#btnDownloadPdf').attr('href', url);
        $('#pdfPreviewModal').modal('show');
    }
    $('#pdfPreviewModal').on('hidden.bs.modal', function () {
        $('#pdfViewerIframe').attr('src', '');
    });

    // --- 3. UPLOAD LOGIC ---
    function openUploadModal(id, name) {
        $('#modalStudentId').val(id);
        $('#modalStudentName').text(name);
        $('#customFile').val('');
        $('.custom-file-label').html('Pilih file...');
        $('#fileListPreview').addClass('d-none');
        $('#fileListUl').empty();
        $('#loadingState').addClass('d-none');
        $('#categorySelect').trigger('change'); 
        $('#uploadModal').modal('show');
    }

    $('#categorySelect').on('change', function() {
        let type = $(this).find(':selected').data('type');
        let fileInput = $('#customFile');
        let infoText = $('#uploadInfoText');

        fileInput.val('');
        $('.custom-file-label').html('Pilih file...');
        $('#fileListPreview').addClass('d-none');

        if (type === 'file') {
            fileInput.removeAttr('multiple');
            infoText.html('<span class="text-warning small"><i class="fas fa-exclamation-circle"></i> Single File (Mode Timpa)</span>');
        } else {
            fileInput.attr('multiple', 'multiple');
            infoText.html('<span class="text-info small"><i class="fas fa-check-circle"></i> Multiple File (Bisa upload banyak)</span>');
        }
    });

    $('#customFile').on('change', function() {
        let files = this.files;
        let count = files.length;
        let list = $('#fileListUl');
        list.empty();

        if (count > 0) {
            $(this).next('.custom-file-label').addClass("selected").html(count + ' file dipilih');
            $('#fileListPreview').removeClass('d-none');
            for (let i = 0; i < count; i++) {
                list.append('<li class="list-group-item px-0 py-1 border-0 bg-transparent"><i class="fas fa-check text-success mr-2"></i>' + files[i].name + '</li>');
            }
        } else {
            $(this).next('.custom-file-label').html('Pilih file...');
            $('#fileListPreview').addClass('d-none');
        }
    });

    $('#uploadForm').on('submit', function() {
        $('#loadingState').removeClass('d-none').addClass('d-flex');
        $('#closeBtn, #cancelBtn').prop('disabled', true);
    });
</script>
@endsection