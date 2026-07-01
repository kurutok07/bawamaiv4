@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    {{-- 1. HEADER DENGAN TOMBOL KEMBALI --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 text-gray-800">Pengaturan Website</h1>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Kembali ke Dashboard
        </a>
    </div>

    {{-- 2. LOGIKA PENENTUAN TAB AKTIF --}}
    @php
        // Cek session dari controller, kalau gak ada default ke 'general'
        $activeTab = session('active_tab', 'general');
    @endphp

    {{-- Navigasi Tab --}}
    <ul class="nav nav-tabs mb-4" id="settingTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link {{ $activeTab == 'general' ? 'active' : '' }}" 
               id="general-tab" data-toggle="tab" href="#general" role="tab">
               <i class="fas fa-cog mr-1"></i> Umum & Maintenance
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeTab == 'carousel' ? 'active' : '' }}" 
               id="carousel-tab" data-toggle="tab" href="#carousel" role="tab">
               <i class="fas fa-images mr-1"></i> Tampilan Carousel
            </a>
        </li>
    </ul>

    <div class="tab-content" id="settingTabContent">
        
        {{-- TAB 1: GENERAL --}}
        <div class="tab-pane fade {{ $activeTab == 'general' ? 'show active' : '' }}" id="general" role="tabpanel">
            <div class="card shadow mb-4 border-left-warning">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">Status Website</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.maintenance') }}" method="POST">
                        @csrf
                        <div class="form-check form-switch mb-3 custom-control custom-switch pl-5">
                            {{-- Bootstrap Switch Style --}}
                            <input type="checkbox" class="custom-control-input" id="maintenanceSwitch" 
                                   name="maintenance_mode" value="1" {{ $maintenanceMode == '1' ? 'checked' : '' }}>
                            <label class="custom-control-label font-weight-bold text-dark" for="maintenanceSwitch">
                                Aktifkan Mode Maintenance (Perbaikan)
                            </label>
                        </div>
                        
                        <div class="alert alert-warning border-0 bg-warning text-white fade show">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-exclamation-triangle fa-2x mr-3"></i>
                                <div>
                                    <strong>Peringatan:</strong><br>
                                    Jika aktif, pengunjung publik hanya akan melihat halaman "Sedang dalam perbaikan". 
                                    <br>    
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save mr-1"></i> Simpan Pengaturan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- TAB 2: CAROUSEL --}}
        <div class="tab-pane fade {{ $activeTab == 'carousel' ? 'show active' : '' }}" id="carousel" role="tabpanel">
            <div class="card shadow mb-4 border-left-primary">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Slide Landing Page ({{ $carousels->count() }}/10)</h6>
                    @if($carousels->count() < 10)
                        <button class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#addCarouselModal">
                            <i class="fas fa-plus mr-1"></i> Tambah Slide
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th width="5%">Urutan</th>
                                    <th width="40%">Preview Konten</th>
                                    <th width="20%">Tipe</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($carousels as $index => $c)
                                <tr>
                                    <td class="text-center align-middle font-weight-bold">{{ $index + 1 }}</td>
                                    <td class="align-middle">
                                        @if($c->type == 'image')
                                            <img src="{{ asset('storage/'.$c->file_path) }}" class="img-fluid rounded shadow-sm" style="max-height: 80px;">
                                        @else
                                            <div class="p-2 bg-light border rounded text-muted small font-italic">
                                                {{ Str::limit(strip_tags($c->html_content), 100) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        @if($c->type == 'image')
                                            <span class="badge badge-primary px-2 py-1"><i class="fas fa-image"></i> Gambar</span>
                                        @else
                                            <span class="badge badge-info px-2 py-1"><i class="fas fa-code"></i> HTML Custom</span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        <form action="{{ route('settings.carousel.delete', $c->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-danger btn-sm rounded-circle" onclick="return confirm('Hapus slide ini?')" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="fas fa-images fa-3x mb-3 text-gray-300"></i>
                                        <p>Belum ada slide carousel.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- MODAL TAMBAH CAROUSEL (Tetap Sama Logic JS-nya) --}}
<div class="modal fade" id="addCarouselModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('settings.carousel.store') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold">Tambah Slide Baru</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="font-weight-bold small text-uppercase">Tipe Slide</label>
                    <select name="type" class="form-control" id="typeSelector" onchange="toggleType()">
                        <option value="image">Gambar / Foto (Upload)</option>
                        {{--<option value="html">Teks / HTML Custom</option>--}}
                    </select>
                </div>
                
                <div id="inputImage" class="form-group">
                    <label class="font-weight-bold small text-uppercase">Upload Gambar</label>
                    <div class="custom-file">
                        <input type="file" name="image" class="custom-file-input" id="customFile" accept="image/*">
                        <label class="custom-file-label" for="customFile">Pilih file...</label>
                    </div>
                    <small class="text-muted">Format: JPG, PNG. Max 2MB.</small>
                </div>

                <div id="inputHtml" class="form-group d-none">
                    <label class="font-weight-bold small text-uppercase">Konten HTML</label>
                    <textarea name="html_content" class="form-control" rows="4" placeholder="<div class='text-center'>...</div>"></textarea>
                    <small class="text-muted">Masukkan kode HTML valid untuk slide custom.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary font-weight-bold">Simpan Slide</button>
            </div>
        </form>
    </div>
</div>

<script>
// Logic Toggle Input
function toggleType() {
    let type = document.getElementById('typeSelector').value;
    if(type == 'image') {
        document.getElementById('inputImage').classList.remove('d-none');
        document.getElementById('inputHtml').classList.add('d-none');
    } else {
        document.getElementById('inputImage').classList.add('d-none');
        document.getElementById('inputHtml').classList.remove('d-none');
    }
}

// Logic nama file di input Bootstrap 4
document.querySelector('.custom-file-input').addEventListener('change',function(e){
  var fileName = document.getElementById("customFile").files[0].name;
  var nextSibling = e.target.nextElementSibling;
  nextSibling.innerText = fileName;
});
</script>
@endsection