@extends('layouts.admin')
@section('title', 'Detail Kelas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 text-gray-800 mb-0">Kelas: {{ $kelas->nama_kelas }}</h1>
        <p class="text-muted">
            Wali Kelas: {{ $kelas->waliKelas->nama_lengkap ?? '-' }} | 
            <span class="badge bg-warning text-dark">
                Tahun Ajaran: {{ $activeTa->tahun_ajaran }} - {{ $activeTa->semester }}
            </span>
        </p>
    </div>
    <a href="{{ route('kelas.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

{{-- Feedback Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    {{-- KOLOM KIRI (UTAMA) --}}
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white">
                <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="siswa-tab" data-toggle="tab" href="#siswa" role="tab">                            <i class="fas fa-users me-1"></i> Daftar Siswa ({{ $siswas->count() }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="guru-tab" data-toggle="tab" href="#guru" role="tab">                            <i class="fas fa-chalkboard-teacher me-1"></i> Guru Pengajar ({{ $gurusPengajar->count() }})
                        </a>
                    </li>
                    {{-- TAB BARU: JADWAL --}}
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="jadwal-tab" data-toggle="tab" href="#jadwal" role="tab">
                            <i class="fas fa-calendar-alt me-1"></i> Jadwal Pelajaran
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="card-body">
                <div class="tab-content" id="myTabContent">
                    
                    {{-- TAB 1: DAFTAR SISWA --}}
                    <div class="tab-pane fade show active" id="siswa" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="dataTableSiswa">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>NISN</th>
                                        <th>Nama Siswa</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($siswas as $index => $siswa)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $siswa->nisn }}</td>
                                        <td>{{ $siswa->nama_lengkap }}</td>
                                        <td class="text-center">
                                            <form action="{{ route('kelas.removeSiswa', ['id_kelas' => $kelas->id, 'id_siswa' => $siswa->id]) }}" 
                                                method="POST" onsubmit="return confirm('Keluarkan siswa ini dari kelas?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" title="Keluarkan"><i class="fas fa-times"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Belum ada siswa di kelas ini.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TAB 2: DAFTAR GURU PENGAJAR --}}
                    <div class="tab-pane fade" id="guru" role="tabpanel">
                        <div class="alert alert-info small">
                            <i class="fas fa-info-circle"></i> Guru yang ditambahkan di sini akan memiliki akses untuk mengupload materi LMS khusus untuk kelas ini.
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>NIP/NUPTK</th>
                                        <th>Nama Guru</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($gurusPengajar as $index => $guru)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $guru->nuptk }}</td>
                                        <td>{{ $guru->nama_lengkap }} {{ $guru->gelar_belakang }}</td>
                                        <td class="text-center">
                                            <form action="{{ route('kelas.removeGuru', ['id_kelas' => $kelas->id, 'id_guru' => $guru->id]) }}" 
                                                method="POST" onsubmit="return confirm('Hapus akses guru ini dari kelas?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" title="Hapus Akses"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Belum ada guru pengajar yang ditambahkan.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TAB 3: JADWAL PELAJARAN (BARU) --}}
                    <div class="tab-pane fade" id="jadwal" role="tabpanel">
                        
                        {{-- Bagian Upload / Ganti Jadwal --}}
                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body">
                                <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-upload mr-2"></i> Update Jadwal Pelajaran</h6>
                                
                                {{-- Kita gunakan route UPDATE yang sudah kita buat di Controller --}}
                                <form action="{{ route('kelas.update', $kelas->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    
                                    {{-- Hidden Inputs (Agar data lain tidak hilang saat update jadwal) --}}
                                    <input type="hidden" name="nama_kelas" value="{{ $kelas->nama_kelas }}">
                                    <input type="hidden" name="tingkat" value="{{ $kelas->tingkat }}">
                                    <input type="hidden" name="wali_kelas_id" value="{{ $kelas->wali_kelas_id }}">

                                    <div class="form-group mb-2">
                                        <label class="small font-weight-bold">Pilih File PDF</label>
                                        <input type="file" name="file_jadwal" class="form-control" accept="application/pdf" required>
                                        <small class="text-muted">Maksimal 2MB. Format .pdf</small>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary btn-sm mt-2">
                                        <i class="fas fa-save mr-1"></i> Simpan Jadwal
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Bagian Preview Jadwal --}}
                        @if($kelas->file_jadwal)
                            <div class="border rounded p-2">
                                <div class="d-flex justify-content-between align-items-center mb-2 px-2">
                                    <h6 class="font-weight-bold m-0 text-primary">Preview Jadwal Saat Ini</h6>
                                    <a href="{{ asset($kelas->file_jadwal) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-external-link-alt mr-1"></i> Buka Fullscreen
                                    </a>
                                </div>
                                <iframe src="{{ asset($kelas->file_jadwal) }}" width="100%" height="500px" style="border: none;"></iframe>
                            </div>
                        @else
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-calendar-times fa-3x mb-3 text-gray-300"></i>
                                <p>Belum ada jadwal pelajaran yang diupload untuk kelas ini.</p>
                            </div>
                        @endif

                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN (SIDEBAR ACTION) --}}
    <div class="col-md-4">
        
        {{-- Card Tambah Siswa --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-success text-white">
                <h6 class="m-0 font-weight-bold"><i class="fas fa-user-plus me-1"></i> Tambah Siswa</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('kelas.addSiswa', $kelas->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="small fw-bold">Pilih Siswa (Non-Kelas)</label>
                        <select name="siswa_id" class="form-select select2" required>
                            <option value="">-- Cari Nama Siswa --</option>
                            @foreach($siswaNonKelas as $snk)
                                <option value="{{ $snk->id }}">{{ $snk->nisn }} - {{ $snk->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success w-100 btn-sm">
                        <i class="fas fa-plus-circle"></i> Masukkan Siswa
                    </button>
                </form>
                
                <hr>
                
                <p class="small text-muted mb-2">Atau Import Excel (.xlsx)</p>
                <form action="{{ route('kelas.importSiswa', $kelas->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="input-group mb-2">
                        <input type="file" name="file" class="form-control form-control-sm" required>
                        <button class="btn btn-outline-success btn-sm" type="submit">Upload</button>
                    </div>
                    <div class="text-center">
                        <a href="{{asset('templates/template_data_kelas.xlsx')}}" download class="small text-decoration-underline text-success">Download Template</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Card Tambah Guru --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-info text-white">
                <h6 class="m-0 font-weight-bold"><i class="fas fa-chalkboard-teacher me-1"></i> Tambah Guru Pengajar</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('kelas.addGuru', $kelas->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="small fw-bold">Pilih Guru</label>
                        <select name="guru_id" class="form-select select2" required>
                            <option value="">-- Cari Nama Guru --</option>
                            @foreach($guruNonPengajar as $gnp)
                                <option value="{{ $gnp->id }}">{{ $gnp->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-info text-white w-100 btn-sm">
                        <i class="fas fa-plus-circle"></i> Assign Guru
                    </button>
                </form>
                <small class="text-muted mt-2 d-block">
                    Guru yang ditambahkan akan bisa mengupload materi khusus untuk kelas ini.
                </small>
            </div>
        </div>

    </div>
</div>
@endsection