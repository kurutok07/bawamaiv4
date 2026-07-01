@extends('layouts.admin')

@section('title', 'Manajemen Akun Staff & Pimpinan')

@section('content')

{{-- HEADER HALAMAN --}}
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
            <i class="fas fa-users-cog text-success mr-2"></i> Manajemen Akun
        </h1>
        <p class="mb-0 text-gray-600 small">Kelola akses Staff, Kepala Sekolah, dan Yayasan</p>
    </div>
    
    <div>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm shadow-sm mr-2">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Kembali
        </a>
        <button class="btn btn-success btn-sm shadow-sm" data-toggle="modal" data-target="#addAccountModal">
            <i class="fas fa-user-plus fa-sm text-white-50 mr-1"></i> Tambah Akun
        </button>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success border-left-success shadow-sm alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

{{-- CARD TABEL --}}
<div class="card shadow mb-4 border-0">
    <div class="card-header py-3 bg-white d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-success">Daftar Pengguna Lainnya</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="bg-light text-dark small font-weight-bold text-uppercase">
                    <tr>
                        <th class="pl-4 py-3" width="25%">Nama Lengkap</th>
                        <th class="py-3">Username</th>
                        <th class="py-3">Role / Jabatan</th>
                        <th class="py-3">Email</th>
                        <th class="text-center py-3" width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-dark">
                    @forelse($users as $user)
                    <tr>
                        <td class="pl-4 align-middle font-weight-bold" style="color: #4e73df;">{{ $user->name }}</td>
                        <td class="align-middle">
                            <span style="background-color: #f8f9fc; color: #5a5c69; border: 1px solid #e3e6f0; padding: 4px 8px; border-radius: 4px; font-family: monospace; font-size: 0.85rem;">
                                {{ $user->username }}
                            </span>
                        </td>
                        <td class="align-middle">
                            {{-- LOGIC BADGE MANUAL STYLING (PASTI MUNCUL) --}}
                            @switch($user->role)
                                @case('kepala_sekolah')
                                    <span style="background-color: #4e73df; color: #ffffff; padding: 5px 10px; border-radius: 15px; font-size: 0.7rem; font-weight: 700; display: inline-block;">
                                        <i class="fas fa-user-tie mr-1"></i> KEPALA SEKOLAH
                                    </span>
                                    @break
                                @case('yayasan')
                                    <span style="background-color: #f6c23e; color: #2e2e2e; padding: 5px 10px; border-radius: 15px; font-size: 0.7rem; font-weight: 700; display: inline-block;">
                                        <i class="fas fa-crown mr-1"></i> YAYASAN
                                    </span>
                                    @break
                                @case('perpus')
                                    <span style="background-color: #1cc88a; color: #ffffff; padding: 5px 10px; border-radius: 15px; font-size: 0.7rem; font-weight: 700; display: inline-block;">
                                        <i class="fas fa-book mr-1"></i> PERPUSTAKAAN
                                    </span>
                                    @break
                                @case('admin_qurana')
                                    <span style="background-color: #1cc88a; color: #ffffff; padding: 5px 10px; border-radius: 15px; font-size: 0.7rem; font-weight: 700; display: inline-block;">
                                        <i class="fas fa-book mr-1"></i> Admin Qurana
                                    </span>
                                    @break

                                @case('uks')
                                    <span style="background-color: #36b9cc; color: #ffffff; padding: 5px 10px; border-radius: 15px; font-size: 0.7rem; font-weight: 700; display: inline-block;">
                                        <i class="fas fa-notes-medical mr-1"></i> PETUGAS UKS
                                    </span>
                                    @break
                                @default
                                    <span style="background-color: #858796; color: #ffffff; padding: 5px 10px; border-radius: 15px; font-size: 0.7rem; font-weight: 700; display: inline-block;">
                                        {{ strtoupper($user->role) }}
                                    </span>
                            @endswitch
                        </td>
                        <td class="align-middle text-muted small">{{ $user->email ?? '-' }}</td>
                        <td class="text-center align-middle">
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-outline-success edit-btn" 
                                    data-id="{{ $user->id }}" 
                                    data-name="{{ $user->name }}"
                                    data-username="{{ $user->username }}"
                                    data-email="{{ $user->email }}"
                                    data-role="{{ $user->role }}"
                                    data-toggle="modal" data-target="#editAccountModal"
                                    title="Edit Akun">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <form action="{{ route('accounts.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus akun {{ $user->name }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Hapus Akun"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center justify-content-center text-gray-300">
                                <i class="fas fa-users-slash fa-4x mb-3" style="color: #dddfeb;"></i>
                                <h5 class="font-weight-bold" style="color: #858796;">Belum ada data akun tambahan</h5>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ========================================================== --}}
{{-- MODAL TAMBAH (DESAIN BARU: BLUE THEME) --}}
{{-- ========================================================== --}}
<div class="modal fade" id="addAccountModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{ route('accounts.store') }}" method="POST" class="modal-content border-0 shadow-lg">
            @csrf
            
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e73df, #22be49);">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-user-plus mr-2"></i> Tambah Akun Baru
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-4" style="background-color: #f8f9fc;">
                {{-- Form Nama, Username, Role, Email (SAMA SEPERTI SEBELUMNYA) --}}
                <div class="form-group mb-3">
                    <label class="small font-weight-bold text-uppercase text-secondary">Nama Lengkap</label>
                    <div class="input-group shadow-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-0"><i class="fas fa-id-card text-primary"></i></span>
                        </div>
                        <input type="text" name="name" class="form-control border-0 bg-white" placeholder="Contoh: Budi Santoso" required style="height: 45px;">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="small font-weight-bold text-uppercase text-secondary">Username</label>
                        <div class="input-group shadow-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-0"><i class="fas fa-user text-primary"></i></span>
                            </div>
                            <input type="text" name="username" class="form-control border-0 bg-white" minlength="10" placeholder="Tanpa spasi" required style="height: 45px;">
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="small font-weight-bold text-uppercase text-secondary">Jabatan</label>
                        <div class="input-group shadow-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-0"><i class="fas fa-briefcase text-primary"></i></span>
                            </div>
                            <select name="role" class="form-control border-0 bg-white" required style="height: 45px;">
                                <option value="" disabled selected>- Pilih -</option>
                                <option value="kepala_sekolah">Kepala Sekolah</option>
                                <option value="yayasan">Yayasan</option>
                                <option value="perpus">Petugas Perpus</option>
                                <option value="admin_qurana">Admin Qurana</option>
                                <option value="uks">Petugas UKS</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label class="small font-weight-bold text-uppercase text-secondary">Email (Opsional)</label>
                    <div class="input-group shadow-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-0"><i class="fas fa-envelope text-primary"></i></span>
                        </div>
                        <input type="email" name="email" class="form-control border-0 bg-white" placeholder="email@sekolah.sch.id" style="height: 45px;">
                    </div>
                </div>

                {{-- PASSWORD (UPDATED: MINLENGTH 8) --}}
                <div class="form-group mb-0">
                    <label class="small font-weight-bold text-uppercase text-secondary">Password</label>
                    <div class="input-group shadow-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-0"><i class="fas fa-lock text-primary"></i></span>
                        </div>
                        {{-- Tambahan: minlength="8" --}}
                        <input type="password" name="password" class="form-control border-0 bg-white" placeholder="Minimal 10 karakter" required minlength="10" style="height: 45px;">
                    </div>
                    <small class="text-muted mt-1 ml-1"><i class="fas fa-info-circle fa-xs"></i> Wajib minimal 10 karakter.</small>
                </div>
            </div>

            <div class="modal-footer border-0 bg-white">
                <button type="button" class="btn btn-light font-weight-bold" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary font-weight-bold px-4 shadow">Simpan Akun</button>
            </div>
        </form>
    </div>  
</div>

{{-- ========================================================== --}}
{{-- MODAL EDIT (DESAIN BARU: YELLOW/WARNING THEME) --}}
{{-- ========================================================== --}}
<div class="modal fade" id="editAccountModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="editForm" action="" method="POST" class="modal-content border-0 shadow-lg">
            @csrf @method('PUT')
            
            <div class="modal-header text-dark" style="background: linear-gradient(135deg, #f6c23e, #22be49);">
                <h5 class="modal-title font-weight-bold text-white">
                    <i class="fas fa-user-edit mr-2"></i> Edit Data Akun
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-4" style="background-color: #fff9e6;">
                {{-- Form Nama, Username, Role, Email (SAMA SEPERTI SEBELUMNYA) --}}
                <div class="form-group mb-3">
                    <label class="small font-weight-bold text-uppercase text-secondary">Nama Lengkap</label>
                    <div class="input-group shadow-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-0"><i class="fas fa-id-card text-warning"></i></span>
                        </div>
                        <input type="text" name="name" id="edit_name" class="form-control border-0 bg-white" required style="height: 45px;">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="small font-weight-bold text-uppercase text-secondary">Username</label>
                        <div class="input-group shadow-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-0"><i class="fas fa-user text-warning"></i></span>
                            </div>
                            <input type="text" name="username" id="edit_username" class="form-control border-0 bg-white" minlength="10" required style="height: 45px;">
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="small font-weight-bold text-uppercase text-secondary">Jabatan</label>
                        <div class="input-group shadow-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-0"><i class="fas fa-briefcase text-warning"></i></span>
                            </div>
                            <select name="role" id="edit_role" class="form-control border-0 bg-white" required style="height: 45px;">
                                <option value="kepala_sekolah">Kepala Sekolah</option>
                                <option value="yayasan">Yayasan</option>
                                <option value="perpus">Petugas Perpus</option>
                                <option value="uks">Petugas UKS</option>
                                <option value="admin_qurana">Admin Qurana</option>

                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label class="small font-weight-bold text-uppercase text-secondary">Email</label>
                    <div class="input-group shadow-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-0"><i class="fas fa-envelope text-warning"></i></span>
                        </div>
                        <input type="email" name="email" id="edit_email" class="form-control border-0 bg-white" style="height: 45px;">
                    </div>
                </div>

                {{-- Password Info --}}
                <div class="alert alert-warning small mb-3 border-0 shadow-sm" style="background-color: #fff3cd; color: #856404;">
                    <i class="fas fa-info-circle mr-1"></i> Kosongkan password jika tidak ingin mengubahnya.
                </div>

                {{-- Password Baru (UPDATED: MINLENGTH 8) --}}
                <div class="form-group mb-0">
                    <label class="small font-weight-bold text-uppercase text-secondary">Password Baru</label>
                    <div class="input-group shadow-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-0"><i class="fas fa-key text-warning"></i></span>
                        </div>
                        {{-- Tambahan: minlength="8" --}}
                        <input type="password" name="password" class="form-control border-0 bg-white" placeholder="Isi hanya jika ganti password (Min. 10)" minlength="10" style="height: 45px;">
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 bg-white">
                <button type="button" class="btn btn-light font-weight-bold" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-warning font-weight-bold text-white px-4 shadow">Update Data</button>
            </div>
        </form>
    </div>
</div>


@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.edit-btn').on('click', function() {
            const id = $(this).data('id');
            $('#editForm').attr('action', '/admin/accounts-others/' + id);
            
            $('#edit_name').val($(this).data('name'));
            $('#edit_username').val($(this).data('username'));
            $('#edit_email').val($(this).data('email'));
            $('#edit_role').val($(this).data('role'));
        });
    });
</script>
@endsection