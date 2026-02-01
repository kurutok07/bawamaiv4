@extends('layouts.admin')
@section('title', 'Edit Akun Kepsek')

@section('content')
<div class="card shadow mb-4" style="max-width: 700px; margin: 0 auto;">
    <div class="card-header py-3 bg-white border-bottom-warning">
        <h6 class="m-0 font-weight-bold text-warning"><i class="fas fa-edit mr-2"></i>Edit Akun</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.kepala-sekolah.update', $kepsek->id) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group mb-3">
                        <label class="small font-weight-bold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="{{ $kepsek->name }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="small font-weight-bold">Username</label>
                        <input type="text" name="username" class="form-control" value="{{ $kepsek->username }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="small font-weight-bold">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $kepsek->email }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="small font-weight-bold">Password Baru <span class="text-muted font-weight-normal">(Opsional)</span></label>
                        <input type="password" name="password" class="form-control" minlength="6" placeholder="Biarkan kosong jika tidak diganti">
                    </div>
                </div>

                <div class="col-md-4 text-center">
                    <label class="small font-weight-bold d-block">Foto Profil</label>
                    
                    <div class="mb-2 d-flex justify-content-center align-items-center bg-light border rounded" style="width: 150px; height: 150px; margin: 0 auto; overflow: hidden;">
                        @if($kepsek->foto_profil)
                            <img id="preview" src="{{ asset($kepsek->foto_profil) }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <img id="preview" src="#" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                            <i id="placeholder-icon" class="fas fa-user fa-3x text-secondary"></i>
                        @endif
                    </div>

                    <input type="file" name="foto" class="form-control-sm w-100" accept="image/*" onchange="previewImage(this)">
                </div>
            </div>

            <hr>

            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.kepala-sekolah.index') }}" class="btn btn-secondary mr-2">Batal</a>
                <button type="submit" class="btn btn-warning"><i class="fas fa-sync-alt mr-1"></i> Update Akun</button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var img = document.getElementById('preview');
                img.src = e.target.result;
                img.style.display = 'block';
                
                var icon = document.getElementById('placeholder-icon');
                if(icon) icon.style.display = 'none';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection