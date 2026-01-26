@extends('layouts.admin')

@section('title', 'Data Guru')

@section('content')

<style>
    /* --- CSS KHUSUS HALAMAN INI --- */
    
    /* Layout Header */
    .page-header-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    /* Layout Utama Grid (Desktop) */
    .admin-responsive-wrapper {
        display: grid;
        grid-template-columns: 350px 1fr; /* Kiri 350px, Kanan sisa layar */
        gap: 25px;
        align-items: start;
    }

    /* Style Card Form */
    .form-sidebar {
        background: white;
        border-radius: 10px;
        padding: 25px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
    }

    /* Style Grid Kartu Guru */
    .guru-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 20px;
    }

    /* --- RESPONSIVE MOBILE (Layar < 900px) --- */
    @media (max-width: 900px) {
        .admin-responsive-wrapper {
            grid-template-columns: 1fr; /* Jadi 1 Kolom */
        }

        /* Pada mobile, sidebar form kita taruh di atas atau bawah? 
           Biasanya user ingin lihat data dulu, tapi kalau input prioritas, taruh atas.
           Disini saya biarkan urutan HTML (Form di atas) */
        
        .page-header-flex {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
        
        .page-header-flex .btn-back {
            width: 100%;
            justify-content: center;
        }
    }

    /* Styling Tambahan Form */
    .upload-area {
        border: 2px dashed #d1d5db;
        border-radius: 50%;
        width: 120px; 
        height: 120px;
        margin: 0 auto 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        overflow: hidden;
        position: relative;
    }
    
    .upload-area:hover {
        border-color: #3b82f6;
    }

    .form-control {
        margin-bottom: 15px;
    }
</style>

{{-- HEADER --}}
<div class="page-header-flex">
    <div style="text-align: left;">
        <h1 class="page-title" style="margin: 0; font-size: 1.75rem; font-weight: 800; color: #111827;">Data Guru</h1>
        <p class="page-subtitle" style="margin: 5px 0 0; color: #6b7280;">Kelola profil pengajar dan staf.</p>
    </div>
    <a href="{{ route('dashboard') }}" class="btn btn-back" style="background-color: #6b7280; color: white; display: flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 6px; text-decoration: none;">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

{{-- WRAPPER UTAMA --}}
<div class="admin-responsive-wrapper">
    
    {{-- KOLOM KIRI: FORM INPUT & IMPORT --}}
    <div style="display: flex; flex-direction: column; gap: 20px;">
        
        {{-- Card 1: Form Tambah --}}
        <div class="form-sidebar">
            <h3 style="margin-bottom: 20px; text-align: center; border-bottom: 1px solid #eee; padding-bottom: 15px; font-size: 1.25rem; font-weight: 700; color: #374151;">
                <i class="fas fa-user-plus text-primary"></i> Tambah Guru
            </h3>
            
            <form action="{{ route('guru.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="form-group" style="text-align: center; margin-bottom: 20px;">
                    <label for="fotoInput">
                        <div class="upload-area">
                            <img id="previewImg" src="{{ asset('img/no-image.png') }}" 
                                 style="width: 100%; height: 100%; object-fit: cover;"
                                 onerror="this.src='https://placehold.co/150x150?text=Foto';">
                        </div>
                        <div style="font-size: 0.85rem; color: #3b82f6; font-weight: 600;">Klik untuk upload foto</div>
                    </label>
                    <input type="file" name="foto" id="fotoInput" style="display: none;" accept="image/*" onchange="previewFile(this)">
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; color: #4b5563;">NIP / NUPTK</label>
                    <input type="number" name="nip" class="form-control" placeholder="199801..." required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; color: #4b5563;">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="form-control" placeholder="Nama tanpa gelar" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div class="form-group">
                        <label class="form-label" style="font-weight: 600; color: #4b5563;">Gelar Depan</label>
                        <input type="text" name="gelar_depan" class="form-control" placeholder="Drs.">
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-weight: 600; color: #4b5563;">Gelar Blkng</label>
                        <input type="text" name="gelar_belakang" class="form-control" placeholder="S.Pd.">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; color: #4b5563;">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-control" required>
                        <option value="">-- Pilih --</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; color: #4b5563;">No. HP</label>
                    <input type="text" name="no_hp" class="form-control" placeholder="0812...">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 10px; font-weight: bold; margin-top: 10px;">Simpan Data</button>
            </form>
        </div>

        {{-- Card 2: Import Excel --}}
        <div class="form-sidebar">
            <h5 style="font-weight: 700; margin-bottom: 15px; color: #374151;">Import Data</h5>
            <form action="{{ route('guru.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="input-group mb-2">
                    <input type="file" name="file" class="form-control" style="padding: 5px;" required>
                </div>
                <button class="btn btn-success btn-sm btn-block w-100 mb-2" type="submit">
                    <i class="fas fa-file-excel"></i> Import Excel
                </button>
            </form>
            <a href="{{asset('templates/template_data_guru.xlsx')}}" download class="btn btn-sm btn-warning btn-block w-100" style="color: white;">
                <i class="fas fa-download"></i> Download Template
            </a>    
        </div>
        
    </div>
    
    {{-- KOLOM KANAN: LIST GURU --}}
    <div>
        {{-- Flash Messages --}}
        @if(session('success'))
            <div style="background: #d1fae5; color: #065f46; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 5px solid #059669;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background: #fee2e2; color: #b91c1c; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 5px solid #b91c1c;">
                <ul style="margin-left: 20px; margin-bottom: 0;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Grid Card Guru --}}
        <div class="guru-grid">
            @foreach($gurus as $guru)
            <div class="admin-card" style="padding: 0; position: relative; overflow: hidden; background: white; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border: 1px solid #f3f4f6;">
                
                {{-- Header Warna --}}
                <div style="height: 80px; background: linear-gradient(135deg, #4b95d2 0%, #2563eb 100%);"></div>
                
                <div style="padding: 0 20px 20px 20px; text-align: center; margin-top: -45px;">
                    @php
                        $imgSrc = $guru->foto ? asset('storage/' . $guru->foto) : asset('assets/no-image.jpg'); // Pastikan path placeholder benar
                    @endphp

                    <img src="{{ $imgSrc }}" 
                         alt="Foto {{ $guru->nama_lengkap }}" 
                         style="width: 90px; height: 90px; border-radius: 50%; object-fit: cover; border: 4px solid white; box-shadow: 0 4px 6px rgba(0,0,0,0.1); background: white;"
                         onerror="this.onerror=null; this.src='https://placehold.co/150x150?text=No+Image';">

                    <h4 style="margin: 10px 0 5px 0; font-size: 1.1rem; font-weight: 700; color: #1f2937;">
                        {{ $guru->gelar_depan }} {{ $guru->nama_lengkap }}{{ $guru->gelar_belakang ? ', '.$guru->gelar_belakang : '' }}
                    </h4>
                    
                    <span style="background: #f3f4f6; color: #4b5563; padding: 2px 10px; border-radius: 10px; font-size: 0.8rem; font-weight: 600;">
                        NIP: {{ $guru->nip }}
                    </span>

                    <div style="margin: 15px 0;">
                        @if($guru->jenis_kelamin == 'L')
                            <span class="badge" style="background: #dbeafe; color: #1e40af; padding: 5px 10px; border-radius: 20px; font-size: 0.8rem;">
                                <i class="fas fa-mars"></i> Laki-laki
                            </span>
                        @else
                            <span class="badge" style="background: #fce7f3; color: #9d174d; padding: 5px 10px; border-radius: 20px; font-size: 0.8rem;">
                                <i class="fas fa-venus"></i> Perempuan
                            </span>
                        @endif
                    </div>

                    <div style="border-top: 1px solid #f3f4f6; padding-top: 15px; display: flex; justify-content: center; gap: 8px;">
                        <a href="{{ route('guru.edit', $guru->id) }}" class="btn btn-sm" style="background: #f3f4f6; color: #374151; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                            <i class="fas fa-edit"></i>
                        </a>
                        
                        <form action="{{ route('guru.destroy', $guru->id) }}" method="POST" onsubmit="return confirm('Yakin hapus guru ini?');">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm" style="background: #fee2e2; color: #ef4444; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 50%; border: none; cursor: pointer;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach            
        </div>
    </div>

</div>

<script>
    function previewFile(input){
        var file = input.files[0];
        if(file){
            var reader = new FileReader();
            reader.onload = function(){
                document.getElementById('previewImg').src = reader.result;
            }
            reader.readAsDataURL(file);
        }
    }
</script>

@endsection