@extends('layouts.admin')
@section('title', 'Manajemen Kelas')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="h3 text-gray-800">Daftar Kelas</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

{{-- Notifikasi --}}
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- Tombol Tambah --}}
<button type="button" id="btnOpenModal" class="btn btn-primary mb-3" data-toggle="modal" data-target="#createKelasModal">
    <i class="fas fa-plus"></i> Buat Kelas Baru
</button>  

<div class="row">
    @foreach($kelas as $k)
    <div class="col-md-4 mb-4">
        <div class="card shadow h-100 border-left-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h4 class="font-weight-bold text-primary mb-0">{{ $k->nama_kelas }}</h4>
                    
                    {{-- FIX: Menggunakan data-toggle (BS4) bukan data-bs-toggle (BS5) --}}
                    <div class="dropdown no-arrow">
                        <button class="btn btn-link btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v text-gray-400"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right shadow" aria-labelledby="dropdownMenuButton">
                            
                            {{-- BUTTON EDIT: Memicu Modal Edit --}}
                            <a class="dropdown-item btn-edit-kelas" href="#" 
                               data-id="{{ $k->id }}"
                               data-nama="{{ $k->nama_kelas }}"
                               data-wali="{{ $k->wali_kelas_id }}"
                               data-toggle="modal" 
                               data-target="#editKelasModal">
                                <i class="fas fa-edit me-2"></i> Edit Kelas
                            </a>

                            <a class="dropdown-item" href="{{ route('kelas.show', $k->id) }}">
                                <i class="fas fa-users me-2"></i> Detail Siswa
                            </a>

                            <div class="dropdown-divider"></div>
                            
                            <form action="{{ route('kelas.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Hapus kelas ini? Siswa akan dikeluarkan (tidak terhapus).')">
                                @csrf @method('DELETE')
                                <button class="dropdown-item text-danger">Hapus Kelas</button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <p class="mb-1 text-muted">
                    <i class="fas fa-chalkboard-teacher me-1"></i> 
                    {{ $k->waliKelas->nama_lengkap ?? 'Belum ada Wali Kelas' }}
                </p>

                {{-- INFO JUMLAH SISWA --}}
                
                <a href="{{ route('kelas.show', $k->id) }}" class="btn btn-sm btn-outline-primary w-100 mt-3">Detail & Anggota</a>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- MODAL CREATE --}}
<div class="modal fade" id="createKelasModal" tabindex="-1" role="dialog" aria-labelledby="createLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createLabel">Buat Kelas Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('kelas.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Kelas</label>
                        <input type="text" name="nama_kelas" class="form-control" placeholder="X RPL 1" required>
                    </div>
                    <div class="form-group">
                        <label>Wali Kelas</label>
                        <select name="wali_kelas_id" class="form-control" required>
                            <option value="">-- Pilih Guru --</option>
                            @foreach($gurus as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDIT (BARU) --}}
<div class="modal fade" id="editKelasModal" tabindex="-1" role="dialog" aria-labelledby="editLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editLabel">Edit Kelas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{-- Form Action akan di-update lewat JS --}}
            <form id="formEditKelas" action="" method="POST">
                @csrf 
                @method('PUT') 
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Kelas</label>
                        <input type="text" name="nama_kelas" id="edit_nama_kelas" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Wali Kelas</label>
                        <select name="wali_kelas_id" id="edit_wali_kelas" class="form-control" required>
                            <option value="">-- Pilih Guru --</option>
                            @foreach($gurus as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

{{-- SCRIPT --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        // Debugging jQuery
        if (typeof jQuery != 'undefined') {
            console.log("✅ jQuery Ready");
        }

        // Logic untuk Modal Edit
        // Kita pakai jQuery karena Bootstrap 4 sangat bergantung pada jQuery
        jQuery('.btn-edit-kelas').on('click', function() {
            // 1. Ambil data dari tombol yang diklik
            var id = jQuery(this).data('id');
            var nama = jQuery(this).data('nama');
            var wali = jQuery(this).data('wali');

            // 2. Isi value input di dalam modal
            jQuery('#edit_nama_kelas').val(nama);
            jQuery('#edit_wali_kelas').val(wali);

            // 3. Update Action URL pada Form
            // Hati-hati: Pastikan route kamu 'kelas.update'. 
            // URL biasanya /kelas/{id}
            var url = "{{ route('kelas.update', ':id') }}";
            url = url.replace(':id', id);
            jQuery('#formEditKelas').attr('action', url);

            console.log("Edit setup for ID: " + id);
        });
    });
</script>