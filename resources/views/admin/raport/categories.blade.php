@extends('layouts.admin')
@section('title', 'Kategori Raport & Sertifikat')

@section('content')
<style>
    /* Custom Styling Tema Hijau Modern */
    .card-folder {
        border: none;
        border-radius: 10px;
        transition: all 0.2s ease-in-out;
        overflow: hidden;
    }
    .card-folder:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
        transform: translateY(-2px);
    }
    .folder-header {
        background-color: #fff;
        border-bottom: 1px solid #f0f0f0;
        padding: 1rem 1.25rem;
    }
    .folder-header button:focus {
        text-decoration: none;
        box-shadow: none;
    }
    .btn-action {
        width: 32px; height: 32px;
        display: inline-flex;
        align-items: center; justify-content: center;
        border-radius: 50%;
        transition: all 0.2s;
    }
    .btn-action:hover { background-color: #eaecf4; }
    
    .list-group-item-action:hover {
        background-color: #f8f9fc;
    }
    /* Indikator Garis Kiri Hijau */
    .border-left-success-custom {
        border-left: 4px solid #1cc88a !important;
    }
</style>

<div class="container-fluid">
    
    {{-- HEADER & TOMBOL KEMBALI --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Manajemen Kategori</h1>
            <p class="mb-0 text-muted small mt-1">Atur struktur folder untuk Raport dan Sertifikat siswa.</p>
        </div>
        
        <div>
            <a href="{{ route('dashboard') }}" class="btn btn-light text-secondary btn-sm shadow-sm mr-2 font-weight-bold border">
                <i class="fas fa-arrow-left fa-sm mr-1"></i> Dashboard
            </a>
            <button class="btn btn-success btn-sm shadow-sm font-weight-bold px-3" data-toggle="modal" data-target="#addModal">
                <i class="fas fa-plus fa-sm mr-1"></i> Tambah Baru
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-left-success shadow-sm rounded-lg mb-4" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-left-danger shadow-sm rounded-lg mb-4" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
        </div>
    @endif

    {{-- KONTEN UTAMA --}}
    <div class="card shadow-sm mb-4 border-0 rounded-lg">
        <div class="card-header py-3 bg-white border-bottom d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-success"><i class="fas fa-sitemap mr-2"></i> Struktur Folder</h6>
            <span class="badge badge-light text-secondary border">Total: {{ $categories->count() }} Item Utama</span>
        </div>
        
        <div class="card-body bg-gray-100">
            <div class="accordion" id="folderAccordion">
                @forelse($categories as $root)
                    <div class="card card-folder mb-3 shadow-sm border-left-success-custom">
                        
                        {{-- HEADER: FOLDER UTAMA --}}
                        <div class="card-header folder-header d-flex justify-content-between align-items-center" id="heading{{ $root->id }}">
                            <div class="flex-grow-1">
                                @if($root->type == 'folder')
                                    <button class="btn text-left font-weight-bold text-dark p-0 d-flex align-items-center" 
                                            type="button" data-toggle="collapse" data-target="#collapse{{ $root->id }}">
                                        <div class="mr-3 bg-light rounded-circle d-flex align-items-center justify-content-center" style="width:40px; height:40px;">
                                            <i class="fas fa-folder text-warning fa-lg"></i>
                                        </div>
                                        <div>
                                            <span style="font-size: 1.05rem;">{{ $root->nama_kategori }}</span>
                                           
                                        </div>
                                    </button>
                                @else
                                    <div class="d-flex align-items-center pl-0">
                                        <div class="mr-3 bg-light rounded-circle d-flex align-items-center justify-content-center" style="width:40px; height:40px;">
                                            <i class="fas fa-file-alt text-secondary fa-lg"></i>
                                        </div>
                                        <div>
                                            <span class="font-weight-bold text-dark" style="font-size: 1.05rem;">{{ $root->nama_kategori }}</span>
                                            <div class="small text-muted font-weight-normal mt-1">File Root (Single)</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            
                            {{-- AKSI ROOT --}}
                            <div class="ml-3 border-left pl-3">
                                <button class="btn btn-action text-info mr-1" onclick="editCategory({{ $root->id }}, '{{ $root->nama_kategori }}', '{{ $root->parent_id }}')" title="Edit Nama">
                                    <i class="fas fa-pen fa-sm"></i>
                                </button>
                                <form action="{{ route('admin.raport-categories.destroy', $root->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-action text-danger" onclick="return confirm('Hapus item ini? Jika folder, isinya harus kosong dulu.')" title="Hapus">
                                        <i class="fas fa-trash fa-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- BODY: ISI FOLDER --}}
                        @if($root->type == 'folder')
                            <div id="collapse{{ $root->id }}" class="collapse bg-white" data-parent="#folderAccordion">
                                <div class="card-body p-0">
                                    <ul class="list-group list-group-flush">
                                        @forelse($root->children as $child)
                                            <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center pl-5 py-3 border-light">
                                                <div class="d-flex align-items-center">
                                                    {{-- Garis konektor visual --}}
                                                    <span style="border-left: 2px solid #e3e6f0; height: 20px; margin-right: 15px; margin-left: 5px;"></span>
                                                    
                                                    @if($child->type == 'folder')
                                                        <i class="fas fa-folder text-warning mr-3"></i> 
                                                    @else
                                                        <i class="fas fa-file-pdf text-danger mr-3"></i> 
                                                    @endif
                                                    <span class="text-gray-800">{{ $child->nama_kategori }}</span>
                                                </div>
                                                
                                                <div>
                                                    <button class="btn btn-sm text-gray-500 hover-info mr-2" onclick="editCategory({{ $child->id }}, '{{ $child->nama_kategori }}', '{{ $child->parent_id }}')">
                                                        <i class="fas fa-pen fa-xs"></i>
                                                    </button>
                                                    <form action="{{ route('admin.raport-categories.destroy', $child->id) }}" method="POST" class="d-inline">
                                                        @csrf @method('DELETE')
                                                        <button class="btn btn-sm text-gray-500 hover-danger" onclick="return confirm('Hapus item ini?')">
                                                            <i class="fas fa-trash fa-xs"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </li>
                                        @empty
                                            
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-5">
                        <img src="https://illustrations.popsy.co/gray/folder.svg" width="150" class="mb-4 opacity-75">
                        <h5 class="text-gray-700 font-weight-bold">Belum Ada Kategori</h5>
                        <p class="text-muted">Silakan buat Folder atau File baru untuk memulai.</p>
                        <button class="btn btn-success shadow-sm mt-2 px-4" data-toggle="modal" data-target="#addModal">
                            <i class="fas fa-plus mr-2"></i> Buat Baru
                        </button>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH --}}
<div class="modal fade" id="addModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('admin.raport-categories.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white border-0">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-plus-circle mr-2"></i> Tambah Baru</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body p-4">
                    
                    {{-- Nama --}}
                    <div class="form-group">
                        <label class="font-weight-bold small text-uppercase text-gray-600">Nama Item</label>
                        <input type="text" name="nama_kategori" class="form-control bg-light border-0" placeholder="Contoh: Sertifikat Lomba / Semester 1" required>
                    </div>

                    <div class="row">
                        {{-- Tipe --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold small text-uppercase text-gray-600">Tipe</label>
                                <select name="type" class="form-control bg-light border-0" id="typeSelect" onchange="toggleParentSelect()">
                                    <option value="file">📄 File (Tempat Upload)</option>    
                                    <option value="folder">📁 Folder (Wadah)</option>
                                </select>
                            </div>
                        </div>
                        
                        {{-- Parent --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold small text-uppercase text-gray-600">Masuk ke Folder:</label>
                                <select name="parent_id" class="form-control bg-light border-0" id="parentSelect" disabled>
                                    <option value="">-- Sebagai Root (Utama) --</option>
                                    {{--@foreach($folders as $f)
                                        <option value="{{ $f->id }}">{{ $f->nama_kategori }}</option>
                                    @endforeach--}}
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-light border small text-muted mb-0">
                        <i class="fas fa-info-circle mr-1 text-info"></i> <strong>Tips:</strong> Gunakan Folder untuk mengelompokkan jenis upload (Misal: Folder "Prestasi" berisi file "Juara Lomba").
                    </div>

                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light text-gray-600 font-weight-bold" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 font-weight-bold shadow-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDIT --}}
<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="" method="POST" class="modal-content border-0 shadow-lg" id="editForm">
            @csrf @method('PUT')
            <div class="modal-header bg-info text-white border-0">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-pen mr-2"></i> Edit Nama</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-4">
                <div class="form-group">
                    <label class="font-weight-bold small text-uppercase text-gray-600">Nama Kategori</label>
                    <input type="text" name="nama_kategori" id="editNama" class="form-control bg-light border-0" required>
                </div>
                <p class="small text-muted mb-0">
                    <i class="fas fa-exclamation-triangle text-warning mr-1"></i> Mengubah nama kategori tidak akan menghapus file yang ada di dalamnya.
                </p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light text-gray-600 font-weight-bold" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-info px-4 font-weight-bold shadow-sm">Update Nama</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Logic Toggle Dropdown Parent saat Tipe berubah
    function toggleParentSelect() {
        let type = document.getElementById('typeSelect').value;
        let parentSelect = document.getElementById('parentSelect');
        
        if (type === 'file') {
            // Jika File, Boleh masuk ke folder
            parentSelect.disabled = false;
        } else {
            // Jika Folder, defaultnya di Root
            parentSelect.value = "";
            parentSelect.disabled = false;
        }
    }

    // Logic Modal Edit
    function editCategory(id, nama, parentId) {
        let url = "{{ route('admin.raport-categories.update', ':id') }}";
        url = url.replace(':id', id);
        
        $('#editForm').attr('action', url);
        $('#editNama').val(nama);
        
        $('#editModal').modal('show');
    }
</script>
@endsection