@extends('layouts.admin')
@section('title', 'Manajemen Kelas')

@section('content')
<div class="row d-flex justify-content-between mb-4">
    <div class="col-md-6 ">
        <h1 class="h3 text-gray-800">Daftar Kelas</h1>
    </div>
    <div class=" text-right d-flex justify-content-end">
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
<button type="button" class="btn btn-success mb-3" data-toggle="modal" data-target="#createKelasModal">
    <i class="fas fa-plus"></i> Buat Kelas Baru
</button>  

<div class="row">
    @foreach($kelas as $k)
    <div class="col-md-4 mb-4">
        <div class="card shadow h-100 border-left-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h4 class="font-weight-bold text-success mb-0">{{ $k->nama_kelas }}</h4>
                    
                    {{-- Dropdown Menu --}}
                    <div class="dropdown no-arrow">
                        <button class="btn btn-link btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v text-gray-400"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right shadow" aria-labelledby="dropdownMenuButton">
                            
                            {{-- BUTTON EDIT --}}
                            <a class="dropdown-item btn-edit-kelas" href="javascript:void(0)" 
                               data-id="{{ $k->id }}"
                               data-nama="{{ $k->nama_kelas }}"
                               data-wali="{{ $k->wali_kelas_id }}"
                               data-tingkat="{{ $k->tingkat }}">
                                <i class="fas fa-edit mr-2 text-warning"></i> Edit Kelas
                            </a>

                            <a class="dropdown-item" href="{{ route('kelas.show', $k->id) }}">
                                <i class="fas fa-users mr-2 text-info"></i> Detail Siswa
                            </a>

                            <div class="dropdown-divider"></div>
                            
                            <form action="{{ route('kelas.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Hapus kelas ini? Siswa akan dikeluarkan (tidak terhapus).')">
                                @csrf @method('DELETE')
                                <button class="dropdown-item text-danger">
                                    <i class="fas fa-trash mr-2"></i> Hapus Kelas
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <p class="mb-1 text-muted">
                    <i class="fas fa-chalkboard-teacher mr-1"></i> 
                    {{ $k->waliKelas->nama_lengkap ?? 'Belum ada Wali Kelas' }}
                </p>
                <span class="badge badge-secondary">Tingkat {{ $k->tingkat }}</span>

                <a href="{{ route('kelas.show', $k->id) }}" class="btn btn-sm btn-outline-success w-100 mt-3">Detail & Anggota</a>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- MODAL CREATE --}}
<div class="modal fade" id="createKelasModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buat Kelas Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('kelas.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Kelas</label>
                        <input type="text" name="nama_kelas" class="form-control" placeholder="1A" required>
                    </div>
                    <div class="form-group">
                        <label>Wali Kelas</label>
                        <select name="wali_kelas_id" class="form-control">
                            <option value="">-- Pilih Guru --</option>
                            @foreach($gurus as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tingkat</label>
                        <select name="tingkat" class="form-control" required>
                            @foreach(range(1, 6) as $num)
                                <option value="{{ $num }}">{{ $num }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDIT --}}
<div class="modal fade" id="editKelasModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">Edit Kelas</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
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
                        <select name="wali_kelas_id" id="edit_wali_kelas" class="form-control">
                            <option value="">-- Pilih Guru --</option>
                            @foreach($gurus as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tingkat</label>
                        <select name="tingkat" id="edit_tingkat" class="form-control" required>
                            @foreach(range(1, 6) as $num)
                                <option value="{{ $num }}">{{ $num }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Pastikan jQuery sudah dimuat di layout utama Anda
    $(document).ready(function() {
        
        // Event Listener untuk Tombol Edit
        // Menggunakan delegation 'body' agar tetap jalan meski elemen dirender ulang
        $('body').on('click', '.btn-edit-kelas', function() {
            
            // 1. Ambil data
            let id = $(this).data('id');
            let nama = $(this).data('nama');
            let wali = $(this).data('wali');
            let tingkat = $(this).data('tingkat');

            // 2. Isi Form Modal
            $('#edit_nama_kelas').val(nama);
            $('#edit_wali_kelas').val(wali);
            $('#edit_tingkat').val(tingkat);

            // 3. Update URL Action Form
            // Ganti :id dengan ID yang diklik
            let url = "{{ route('kelas.update', ':id') }}";
            url = url.replace(':id', id);
            $('#formEditKelas').attr('action', url);

            // 4. Buka Modal (Support BS4 & BS5)
            $('#editKelasModal').modal('show');
        });

    });
</script>
@endsection
