@extends('layouts.admin')
@section('title', 'Tambah Akun Kepsek')

@section('content')
<div class="card shadow mb-4" style="max-width: 700px; margin: 0 auto;">
    <div class="card-header py-3 bg-white border-bottom-success">
        <h6 class="m-0 font-weight-bold text-success"><i class="fas fa-user-plus mr-2"></i>Buat Akun Baru</h6>
    </div>
    <div class="card-body">
        {{-- PENTING: Tambahkan enctype untuk upload file --}}
        <form action="{{ route('admin.kepala-sekolah.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                {{-- Kolom Kiri: Input Data --}}
                <div class="col-md-8">
                    <div class="form-group mb-3">
                        <label class="small font-weight-bold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" required placeholder="Contoh: Bpk. H. Ahmad">
                    </div>

                    <div class="form-group mb-3">
                        <label class="small font-weight-bold">Username</label>
                        <input type="text" name="username" class="form-control" required placeholder="Contoh: kepsek">
                    </div>

                    <div class="form-group mb-3">
                        <label class="small font-weight-bold">Email</label>
                        <input type="email" name="email" class="form-control" required placeholder="email@sekolah.id">
                    </div>

                    <div class="form-group mb-3">
                        <label class="small font-weight-bold">Password</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
                </div>

                {{-- Kolom Kanan: Upload Foto --}}
                <div class="col-md-4 text-center">
                    <label class="small font-weight-bold d-block">Foto Profil</label>
                    
                    {{-- Preview Image Container --}}
                    <div class="mb-2 d-flex justify-content-center align-items-center bg-light border rounded" style="width: 150px; height: 150px; margin: 0 auto; overflow: hidden;">
                        <img id="preview" src="#" alt="Preview" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                        <i id="placeholder-icon" class="fas fa-camera fa-2x text-secondary"></i>
                    </div>

                    <input type="file" name="foto" class="form-control-sm w-100" accept="image/*" onchange="previewImage(this)">
                    <small class="text-muted d-block mt-1">Max 2MB (JPG/PNG)</small>
                </div>
            </div>

            <hr>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.kepala-sekolah.index') }}" class="btn btn-secondary mr-2">Batal</a>
                <button type="submit" class="btn btn-success"><i class="fas fa-save mr-1"></i> Simpan Akun</button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview').src = e.target.result;
                document.getElementById('preview').style.display = 'block';
                document.getElementById('placeholder-icon').style.display = 'none';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection