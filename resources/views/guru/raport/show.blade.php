@extends('layouts.admin')

@section('styles')
<style>
    /* 1. Avatar Inisial Siswa */
    .avatar-circle {
        width: 40px;
        height: 40px;
        background-color: #4e73df;
        color: white;
        text-align: center;
        line-height: 40px;
        border-radius: 50%;
        font-weight: bold;
        font-size: 14px;
        display: inline-block;
        margin-right: 10px;
    }
    
    /* 2. Soft Badges (Warna Pastel) */
.badge-soft-success {
        background-color: #d1e7dd;
        color: #0f5132;
        border: 1px solid #badbcc;
    }
    .badge-soft-secondary {
        background-color: #e2e3e5;
        color: #41464b;
        border: 1px solid #d3d6d8;
    }
    
    /* 3. Hover Effect */
    .table-hover tbody tr:hover {
        background-color: #f8f9fc;
        transform: scale(1.005);
        transition: all 0.2s ease-in-out;
    }

    /* 4. Tab Navigasi */
    .nav-pills .nav-link.active {
        background-color: #4e73df;
        box-shadow: 0 4px 6px rgba(78, 115, 223, 0.3);}
</style>
@endsection

@section('content')
<div class="container-fluid">
    
    {{-- Header Section --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Input Raport</h1>
            <p class="mb-0 text-gray-600">
                Kelas <span class="font-weight-bold text-primary">{{ $kelas->nama_kelas }}</span> &bull; 
                Tahun Ajaran {{ $kelas->tahunAjaran->tahun }}
            </p>
        </div>
        <a href="{{ route('guru.raport.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-left-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- CARD UTAMA --}}
    <div class="card shadow mb-4 border-bottom-primary">
        <div class="card-header py-3 bg-white d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Siswa</h6>
            
            {{-- NAVIGASI KATEGORI (TABS) --}}
            <ul class="nav nav-pills card-header-pills">
                @foreach($categories as $cat)
                <li class="nav-item">
                    <a class="nav-link btn-sm {{ $selectedCategoryId == $cat->id ? 'active' : '' }}" 
                       href="{{ route('guru.raport.show', ['kelas_id' => $kelas->id, 'category_id' => $cat->id]) }}">
                       @if($selectedCategoryId == $cat->id) <i class="fas fa-folder-open mr-1"></i> @endif
                       {{ $cat->nama_kategori }}
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%" class="text-center">No.</th>
                            <th width="30%" class="text-center">Siswa</th>
                            <th width="10%" class="text-center"> NIS </th>
                            <th width="20%" class="text-center">Status File</th>
                            <th width="15%" class="text-center">Terakhir Update</th>
                            <th width="20%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kelas->siswas as $index => $siswa)

                        <tr>
                            <td class="align-middle text-center">{{ $index + 1 }}</td>
                            
                            {{-- KOLOM NAMA DENGAN AVATAR --}}
                            <td class="align-middle text-center">
                                
                                <div class="font-weight-bold text-gray-800">{{ $siswa->nama_lengkap }}</div>
                                    
                            </td>
                            <td class="align-middle text-center"> 
                                <div class="small text-muted ">{{ $siswa->nis }}</div>
                            </td>
                            {{-- STATUS FILE --}}
                            <td class="align-middle text-center">
                                @if(isset($existingFiles[$siswa->id]))
                                    {{-- Ganti badge-soft-success jadi badge-success --}}
                                    <span class="badge badge-success px-3 py-2 rounded-pill">
                                        <i class="fas fa-check-circle mr-1"></i> Tersedia
                                    </span>
                                @else
                                    {{-- Ganti badge-soft-secondary jadi badge-secondary --}}
                                    <span class="badge badge-secondary px-3 py-2 rounded-pill">
                                        <i class="fas fa-times-circle mr-1"></i> Kosong
                                    </span>
                                @endif
                            </td>
                            {{-- TERAKHIR UPDATE --}}
                            <td class="align-middle text-center small text-muted">
                                @if(isset($existingFiles[$siswa->id]))
                                    {{ $existingFiles[$siswa->id]->updated_at->format('d/m/Y') }}<br>
                                    {{ $existingFiles[$siswa->id]->updated_at->format('H:i') }} WIB
                                @else
                                    -
                                @endif
                            </td>

                            {{-- TOMBOL AKSI --}}
                            <td class="align-middle text-center">
                                <div class="btn-group" role="group">
                                    {{-- Tombol Upload --}}
                                    <button class="btn btn-sm {{ isset($existingFiles[$siswa->id]) ? 'btn-warning' : 'btn-primary' }} shadow-sm" 
                                            onclick="openUploadModal({{ $siswa->id }}, '{{ $siswa->nama_lengkap }}')"
                                            data-toggle="tooltip" title="{{ isset($existingFiles[$siswa->id]) ? 'Ganti File' : 'Upload File' }}">
                                        <i class="fas fa-upload"></i>
                                    </button>

                                    @if(isset($existingFiles[$siswa->id]))
                                        {{-- Tombol Lihat --}}
                                        <a href="{{ asset('storage/' . $existingFiles[$siswa->id]->file_path) }}" 
                                           target="_blank" class="btn btn-sm btn-info shadow-sm"
                                           data-toggle="tooltip" title="Lihat PDF">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        {{-- Tombol Hapus --}}
                                        <form action="{{ route('guru.raport.destroy', $existingFiles[$siswa->id]->id) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Yakin ingin menghapus raport ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger shadow-sm"
                                                    data-toggle="tooltip" title="Hapus File">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <p class="text-muted">Belum ada siswa di kelas ini.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL UPLOAD --}}
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('guru.raport.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
                <input type="hidden" name="raport_category_id" value="{{ $selectedCategoryId }}">
                <input type="hidden" name="student_id" id="modalStudentId">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-file-upload mr-2"></i> Upload Raport
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                
                <div class="modal-body p-4">
                    {{-- Info Siswa --}}
                    <div class="text-center mb-4">
                        <div class="avatar-circle mx-auto mb-2" style="background-color: #4e73df; width: 60px; height: 60px; line-height: 60px; font-size: 24px;">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h5 class="font-weight-bold text-gray-800" id="textStudentName">Nama Siswa</h5>
                        <p class="text-muted small">Silakan pilih file PDF raport untuk siswa ini.</p>
                    </div>

                    {{-- Input File Custom --}}
                    <div class="form-group">
                        <label class="font-weight-bold text-gray-700">File PDF</label>
                        <div class="custom-file">
                            <input type="file" name="file_raport" class="custom-file-input" id="customFile" accept="application/pdf" required>
                            <label class="custom-file-label" for="customFile">Pilih file...</label>
                        </div>
                        <small class="form-text text-muted mt-2">
                            <i class="fas fa-info-circle"></i> Maksimal ukuran file 2MB.
                        </small>
                    </div>
                </div>
                
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Script agar nama file muncul di input custom bootstrap
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });

    // Script Modal
    function openUploadModal(studentId, studentName) {
        $('#modalStudentId').val(studentId);
        $('#textStudentName').text(studentName); // Update text nama di tengah modal
        
        // Reset input file biar bersih
        $('.custom-file-input').val('');
        $('.custom-file-label').html('Pilih file...');
        
        $('#uploadModal').modal('show');
    }

    // Aktifkan Tooltip Bootstrap
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
</script>
@endsection