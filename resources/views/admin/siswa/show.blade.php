@extends('layouts.admin')

@section('title', 'Detail Siswa: ' . $siswa->nama_lengkap)

@section('content')

{{-- HEADER & TOMBOL AKSI --}}
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Profil Siswa</h1>
        <p class="text-muted small mb-0 mt-1">Detail data lengkap sesuai Dapodik.</p>
    </div>
    
    <div class="d-flex gap-2">
        <a href="{{ route('siswa.index') }}" class="btn btn-secondary shadow-sm btn-sm mr-2">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Kembali
        </a>   
        @if(Auth::user()->role == 'admin')
        <a href="{{ route('siswa.edit', $siswa->id) }}" class="btn btn-warning shadow-sm btn-sm mr-2 text-dark font-weight-bold">
            <i class="fas fa-pen fa-sm mr-1"></i> Edit Data
        </a>
        @endif

    </div>
</div>

<div class="row">

    {{-- === KOLOM KIRI: KARTU PROFIL UTAMA === --}}
    <div class="col-xl-4 col-lg-5 mb-4">
        
        {{-- Card Profil --}}
        <div class="card shadow mb-4 border-0">
            {{-- Banner Gradient --}}
            <div style="height: 100px; background: {{ $siswa->jenis_kelamin == 'L' ? 'linear-gradient(135deg, #4e73df 10%, #224abe 100%)' : 'linear-gradient(135deg, #e74a3b 10%, #be2617 100%)' }}; border-radius: 0.35rem 0.35rem 0 0;"></div>
            
            <div class="card-body text-center pt-0 position-relative">
                {{-- Foto Profil Floating --}}
                <div class="position-relative d-inline-block" style="margin-top: -50px;">
                    <img src="{{ $siswa->foto ? asset($siswa->foto) : asset('img/no-image.png') }}" 
                         class="img-thumbnail rounded-circle bg-white"
                         style="width: 120px; height: 120px; object-fit: cover; border: 4px solid white; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);"
                         onerror="this.src='{{ asset('img/no-image.png') }}'">
                </div>

                <h4 class="mt-3 mb-1 font-weight-bold text-dark">{{ $siswa->nama_lengkap }}</h4>
                <div class="mb-3">
                    <span class="badge badge-light border text-dark font-monospace px-2 py-1">
                        NISN : {{ $siswa->nisn }}
                    </span>
                    <span class="badge badge-light border text-dark font-monospace px-2 py-1 ml-1">
                        NIK : {{ $siswa->nik ?? '-' }}
                    </span>
                    <span class="badge badge-light border text-dark font-monospace px-2 py-1 ml-1">
                        NIPD : {{ $siswa->nipd ?? '-' }}
                    </span>
                </div>

                {{-- Status Kelas --}}
                <div class="mb-4">
                    @php $kelasAktif = $siswa->kelas->first(); @endphp
                    @if($kelasAktif)
                        <div class="p-2 bg-success text-white rounded shadow-sm d-inline-block px-4">
                            <i class="fas fa-chalkboard-teacher mr-1"></i> Kelas <strong>{{ $kelasAktif->nama_kelas }}</strong>
                        </div>
                    @else
                        <div class="p-2 bg-secondary text-white rounded shadow-sm d-inline-block px-4">
                            Belum Masuk Kelas
                        </div>
                    @endif
                </div>

                {{-- Info Grid Kecil --}}
                <div class="row text-left border-top pt-3">
                    <div class="col-6 mb-3">
                        <small class="text-uppercase text-muted font-weight-bold d-block" style="font-size: 0.65rem;">Jenis Kelamin</small>
                        <span class="font-weight-bold text-dark">
                            <i class="fas {{ $siswa->jenis_kelamin == 'L' ? 'fa-mars text-success' : 'fa-venus text-danger' }} mr-1"></i>
                            {{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                        </span>
                    </div>
                    <div class="col-6 mb-3">
                        <small class="text-uppercase text-muted font-weight-bold d-block" style="font-size: 0.65rem;">Agama</small>
                        <span class="font-weight-bold text-dark">{{ $siswa->agama ?? '-' }}</span>
                    </div>
                    <div class="col-12">
                        <small class="text-uppercase text-muted font-weight-bold d-block" style="font-size: 0.65rem;">Tempat, Tgl Lahir</small>
                        <span class="font-weight-bold text-dark">
                            <i class="fas fa-birthday-cake text-warning mr-1"></i>
                            {{ $siswa->tempat_lahir }}, {{ \Carbon\Carbon::parse($siswa->tanggal_lahir)->translatedFormat('d F Y') }}
                        </span>
                    </div>
                </div>
            </div>
            {{-- Footer Card --}}
            <div class="card-footer bg-light text-center">
                <small class="text-muted">Terdaftar sejak: {{ $siswa->created_at->format('d M Y') }}</small>
            </div>
        </div>

        {{-- Kontak Singkat --}}
        <div class="card shadow mb-4 border-0 border-left-info">
            <div class="card-body">
                <h6 class="font-weight-bold text-info mb-3"><i class="fas fa-address-book mr-2"></i> Kontak Cepat</h6>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><i class="fas fa-phone fa-fw mr-2 text-gray-400"></i> {{ $siswa->hp ?? '-' }} (Siswa)</li>
                    <li class="mb-2"><i class="fas fa-envelope fa-fw mr-2 text-gray-400"></i> {{ $siswa->email ?? '-' }}</li>
                    <li class="mb-0"><i class="fas fa-phone-square fa-fw mr-2 text-gray-400"></i> {{ $siswa->ayah->no_hp ?? ($siswa->ibu->no_hp ?? ($siswa->wali->no_hp ?? '-')) }} (Ortu)</li>
                </ul>
            </div>
        </div>

    </div>

    {{-- === KOLOM KANAN: TABS DATA LENGKAP === --}}
    <div class="col-xl-8 col-lg-7">
        
        <div class="card shadow mb-4 border-0">
            <div class="card-header border-bottom-0 bg-white pb-0">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link link-success active font-weight-bold" id="pribadi-tab" data-toggle="tab" href="#pribadi" role="tab">
                            <i class="fas fa-user mr-1"></i> Data Pribadi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link link-success font-weight-bold" id="keluarga-tab" data-toggle="tab" href="#keluarga" role="tab">
                            <i class="fas fa-users mr-1"></i> Keluarga
                        </a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link link-success font-weight-bold" id="akademik-tab" data-toggle="tab" href="#akademik" role="tab">
                            <i class="fas fa-graduation-cap mr-1"></i> Akademik
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link link-success font-weight-bold" id="kesejahteraan-tab" data-toggle="tab" href="#kesejahteraan" role="tab">
                            <i class="fas fa-hand-holding-heart mr-1"></i> Kesejahteraan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link link-success font-weight-bold" id="kesehatan-tab" data-toggle="tab" href="#kesehatan" role="tab">
                            <i class="fas fa-heartbeat mr-1"></i> Kesehatan
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="card-body">
                <div class="tab-content" id="myTabContent">

                    {{-- TAB 1: DATA PRIBADI & ALAMAT --}}
                    <div class="tab-pane fade show active" id="pribadi" role="tabpanel">
                        <h6 class="font-weight-bold text-success mb-3">Detail Alamat & Tempat Tinggal</h6>
                        <div class="row mb-4">
                            <div class="col-md-12 mb-3">
                                <label class="small text-muted font-weight-bold text-uppercase">Alamat Jalan</label>
                                <div class="font-weight-bold border rounded p-2 bg-light">{{ $siswa->alamat ?? '-' }}</div>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <label class="small text-muted font-weight-bold text-uppercase">RT / RW</label>
                                <div class="font-weight-bold">{{ $siswa->rt ?? '-' }} / {{ $siswa->rw ?? '-' }}</div>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <label class="small text-muted font-weight-bold text-uppercase">Dusun</label>
                                <div class="font-weight-bold">{{ $siswa->dusun ?? '-' }}</div>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <label class="small text-muted font-weight-bold text-uppercase">Kelurahan</label>
                                <div class="font-weight-bold">{{ $siswa->kelurahan ?? '-' }}</div>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <label class="small text-muted font-weight-bold text-uppercase">Kecamatan</label>
                                <div class="font-weight-bold">{{ $siswa->kecamatan ?? '-' }}</div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="small text-muted font-weight-bold text-uppercase">Kode Pos</label>
                                <div class="font-weight-bold">{{ $siswa->kode_pos ?? '-' }}</div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="small text-muted font-weight-bold text-uppercase">Jenis Tinggal</label>
                                <div class="font-weight-bold">{{ $siswa->jenis_tinggal ?? '-' }}</div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="small text-muted font-weight-bold text-uppercase">Transportasi</label>
                                <div class="font-weight-bold">{{ $siswa->alat_transportasi ?? '-' }}</div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="small text-muted font-weight-bold text-uppercase">Jarak Sekolah</label>
                                <div class="font-weight-bold">{{ $siswa->jarak_ke_sekolah ? $siswa->jarak_ke_sekolah . ' KM' : '-' }}</div>
                            </div>
                        </div>

                        <h6 class="font-weight-bold text-success mb-3">Koordinat Zonasi</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center p-2 border rounded">
                                    <i class="fas fa-map-marker-alt text-danger fa-2x mr-3"></i>
                                    <div>
                                        <div class="small text-muted font-weight-bold">Lintang (Latitude)</div>
                                        <div class="font-weight-bold">{{ $siswa->lintang ?? 'Belum diset' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center p-2 border rounded">
                                    <i class="fas fa-map-marker-alt text-danger fa-2x mr-3"></i>
                                    <div>
                                        <div class="small text-muted font-weight-bold">Bujur (Longitude)</div>
                                        <div class="font-weight-bold">{{ $siswa->bujur ?? 'Belum diset' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 2: KELUARGA --}}
                    <div class="tab-pane fade" id="keluarga" role="tabpanel">
                        <div class="row">
                            {{-- AYAH --}}
                            <div class="col-md-6 mb-4">
                                <div class="card border-left-success h-100 shadow-sm">
                                    <div class="card-header bg-white font-weight-bold text-success">
                                        <i class="fas fa-male mr-2"></i> Data Ayah
                                    </div>
                                    <div class="card-body">
                                        @if($siswa->ayah)
                                            <table class="table table-borderless table-sm mb-0">
                                                <tr><td class="text-muted w-50">Nama:</td><td class="font-weight-bold">{{ $siswa->ayah->nama }}</td></tr>
                                                <tr><td class="text-muted">NIK:</td><td>{{ $siswa->ayah->nik ?? '-' }}</td></tr>
                                                <tr><td class="text-muted">Lahir:</td><td>{{ $siswa->ayah->tahun_lahir ?? '-' }}</td></tr>
                                                <tr><td class="text-muted">Pendidikan:</td><td>{{ $siswa->ayah->jenjang_pendidikan ?? '-' }}</td></tr>
                                                <tr><td class="text-muted">Pekerjaan:</td><td>{{ $siswa->ayah->pekerjaan ?? '-' }}</td></tr>
                                                <tr><td class="text-muted">Penghasilan:</td><td>{{ $siswa->ayah->penghasilan ?? '-' }}</td></tr>
                                            </table>
                                        @else
                                            <p class="text-muted text-center my-4 font-italic">Data Ayah belum diisi.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- IBU --}}
                            <div class="col-md-6 mb-4">
                                <div class="card border-left-danger h-100 shadow-sm">
                                    <div class="card-header bg-white font-weight-bold text-danger">
                                        <i class="fas fa-female mr-2"></i> Data Ibu
                                    </div>
                                    <div class="card-body">
                                        @if($siswa->ibu)
                                            <table class="table table-borderless table-sm mb-0">
                                                <tr><td class="text-muted w-50">Nama:</td><td class="font-weight-bold">{{ $siswa->ibu->nama }}</td></tr>
                                                <tr><td class="text-muted">NIK:</td><td>{{ $siswa->ibu->nik ?? '-' }}</td></tr>
                                                <tr><td class="text-muted">Lahir:</td><td>{{ $siswa->ibu->tahun_lahir ?? '-' }}</td></tr>
                                                <tr><td class="text-muted">Pendidikan:</td><td>{{ $siswa->ibu->jenjang_pendidikan ?? '-' }}</td></tr>
                                                <tr><td class="text-muted">Pekerjaan:</td><td>{{ $siswa->ibu->pekerjaan ?? '-' }}</td></tr>
                                                <tr><td class="text-muted">Penghasilan:</td><td>{{ $siswa->ibu->penghasilan ?? '-' }}</td></tr>
                                            </table>
                                        @else
                                            <p class="text-muted text-center my-4 font-italic">Data Ibu belum diisi.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- WALI --}}
                            <div class="col-md-12">
                                <div class="card border-left-warning shadow-sm">
                                    <div class="card-header bg-white font-weight-bold text-warning">
                                        <i class="fas fa-user-friends mr-2"></i> Data Wali
                                    </div>
                                    <div class="card-body">
                                        @if($siswa->wali)
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <table class="table table-borderless table-sm mb-0">
                                                        <tr><td class="text-muted w-50">Nama:</td><td class="font-weight-bold">{{ $siswa->wali->nama }}</td></tr>
                                                        <tr><td class="text-muted">NIK:</td><td>{{ $siswa->wali->nik ?? '-' }}</td></tr>
                                                        <tr><td class="text-muted">Lahir:</td><td>{{ $siswa->wali->tahun_lahir ?? '-' }}</td></tr>
                                                    </table>
                                                </div>
                                                <div class="col-md-6">
                                                    <table class="table table-borderless table-sm mb-0">
                                                        <tr><td class="text-muted w-50">Pendidikan:</td><td>{{ $siswa->wali->jenjang_pendidikan ?? '-' }}</td></tr>
                                                        <tr><td class="text-muted">Pekerjaan:</td><td>{{ $siswa->wali->pekerjaan ?? '-' }}</td></tr>
                                                        <tr><td class="text-muted">Penghasilan:</td><td>{{ $siswa->wali->penghasilan ?? '-' }}</td></tr>
                                                    </table>
                                                </div>
                                            </div>
                                        @else
                                            <p class="text-muted text-center font-italic mb-0">Tidak ada data wali.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 3: AKADEMIK --}}
                    <div class="tab-pane fade" id="akademik" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <h6 class="font-weight-bold text-success mb-3">Registrasi Sekolah</h6>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center pl-0">
                                        <span class="text-muted">Sekolah Asal</span>
                                        <span class="font-weight-bold">{{ $siswa->sekolah_asal ?? '-' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center pl-0">
                                        <span class="text-muted">No. Peserta UN</span>
                                        <span class="font-weight-bold">{{ $siswa->no_peserta_un ?? '-' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center pl-0">
                                        <span class="text-muted">No. Seri Ijazah</span>
                                        <span class="font-weight-bold">{{ $siswa->no_seri_ijazah ?? '-' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center pl-0">
                                        <span class="text-muted">No. SKHUN</span>
                                        <span class="font-weight-bold">{{ $siswa->skhun ?? '-' }}</span>
                                    </li>
                                </ul>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <h6 class="font-weight-bold text-success mb-3">Legalitas</h6>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center pl-0">
                                        <span class="text-muted">No. Reg. Akta Lahir</span>
                                        <span class="font-weight-bold">{{ $siswa->no_registrasi_akta_lahir ?? '-' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center pl-0">
                                        <span class="text-muted">No. Kartu Keluarga (KK)</span>
                                        <span class="font-weight-bold">{{ $siswa->no_kk ?? '-' }}</span>
                                    </li>
                                </ul>
                            </div>

                            <div class="col-12 mt-2">
                                <h6 class="font-weight-bold text-success mb-3">Riwayat Kelas</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="bg-light text-dark">
                                            <tr>
                                                <th>Tahun Ajaran</th>
                                                <th>Tingkat</th>
                                                <th>Nama Kelas</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($siswa->kelas as $k)
                                                <tr>
                                                    <td>{{ $k->tahunAjaran->tahun_ajaran ?? '-' }} ({{ $k->tahunAjaran->semester ?? '-' }})</td>
                                                    <td>{{ $k->tingkat }}</td>
                                                    <td class="font-weight-bold">{{ $k->nama_kelas }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 4: KESEJAHTERAAN --}}
                    <div class="tab-pane fade" id="kesejahteraan" role="tabpanel">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="card h-100 {{ $siswa->penerima_kip ? 'border-success' : 'border-light' }} shadow-sm">
                                    <div class="card-body text-center">
                                        <h6 class="font-weight-bold mb-3">Kartu Indonesia Pintar (KIP)</h6>
                                        @if($siswa->penerima_kip)
                                            <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                                            <p class="mb-1 font-weight-bold text-success">Penerima Aktif</p>
                                            <div class="badge badge-light border mb-2">{{ $siswa->no_kip }}</div>
                                            <small class="d-block text-muted">A.n: {{ $siswa->nama_di_kip }}</small>
                                        @else
                                            <i class="fas fa-times-circle text-secondary fa-3x mb-3 opacity-50"></i>
                                            <p class="text-muted">Bukan Penerima</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card h-100 {{ $siswa->penerima_kps ? 'border-success' : 'border-light' }} shadow-sm">
                                    <div class="card-body text-center">
                                        <h6 class="font-weight-bold mb-3">Kartu Perlindungan Sosial (KPS)</h6>
                                        @if($siswa->penerima_kps)
                                            <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                                            <p class="mb-1 font-weight-bold text-success">Penerima Aktif</p>
                                            <div class="badge badge-light border">{{ $siswa->no_kps }}</div>
                                        @else
                                            <i class="fas fa-times-circle text-secondary fa-3x mb-3 opacity-50"></i>
                                            <p class="text-muted">Bukan Penerima</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card h-100 border-light shadow-sm">
                                    <div class="card-body text-center">
                                        <h6 class="font-weight-bold mb-3">Kartu Keluarga Sejahtera (KKS)</h6>
                                        @if($siswa->no_kks)
                                            <i class="fas fa-id-card text-info fa-3x mb-3"></i>
                                            <p class="mb-1 font-weight-bold text-info">Terdaftar</p>
                                            <div class="badge badge-light border">{{ $siswa->no_kks }}</div>
                                        @else
                                            <i class="fas fa-times-circle text-secondary fa-3x mb-3 opacity-50"></i>
                                            <p class="text-muted">Tidak Ada Data</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mt-3">
                                <div class="card bg-gray-100 border-0">
                                    <div class="card-body">
                                        <h6 class="font-weight-bold"><i class="fas fa-university mr-2"></i> Data Rekening Bank (PIP)</h6>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <small class="text-muted d-block">Nama Bank</small>
                                                <strong>{{ $siswa->bank ?? '-' }}</strong>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted d-block">Nomor Rekening</small>
                                                <strong>{{ $siswa->no_rekening_bank ?? '-' }}</strong>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted d-block">Atas Nama</small>
                                                <strong>{{ $siswa->rekening_atas_nama ?? '-' }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 5: KESEHATAN (REKAM MEDIS AKTIF) --}}
                    <div class="tab-pane fade" id="kesehatan" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="font-weight-bold text-success m-0"><i class="fas fa-notes-medical mr-2"></i> Rekam Medis & Riwayat Penyakit</h6>
                            <div>
                            @if(Auth::user()->role == 'uks')

                            <button class="btn btn-success btn-sm shadow-sm" data-toggle="modal" data-target="#addHealthLogModal">
                                <i class="fas fa-plus fa-sm mr-1"></i> Input penyakit
                            </button>
                            <button class="btn btn-outline-primary btn-sm shadow-sm" data-toggle="modal" data-target="#updateProfileModal">
                                <i class="fas fa-edit fa-sm mr-1"></i> Lengkapi Profil Kesehatan
                            </button>
                            @endif


                            </div>
                        </div>

                        {{-- A. DATA FISIK (Dari Tabel Siswa) --}}
                        <div class="row mb-4">
                            <div class="col-md-3 mb-3">
                                <div class="p-3 border rounded text-center bg-light shadow-sm">
                                    <i class="fas fa-ruler-vertical text-info mb-2"></i>
                                    <div class="small text-muted font-weight-bold text-uppercase" style="font-size: 0.65rem;">Tinggi</div>
                                    <div class="h6 font-weight-bold mb-0 text-dark">{{ $siswa->tinggi_badan ?? '-' }} cm</div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="p-3 border rounded text-center bg-light shadow-sm">
                                    <i class="fas fa-weight text-info mb-2"></i>
                                    <div class="small text-muted font-weight-bold text-uppercase" style="font-size: 0.65rem;">Berat</div>
                                    <div class="h6 font-weight-bold mb-0 text-dark">{{ $siswa->berat_badan ?? '-' }} kg</div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="p-3 border rounded text-center bg-light shadow-sm">
                                    <i class="fas fa-head-side-mask text-info mb-2"></i>
                                    <div class="small text-muted font-weight-bold text-uppercase" style="font-size: 0.65rem;">Lingkar Kepala</div>
                                    <div class="h6 font-weight-bold mb-0 text-dark">{{ $siswa->lingkar_kepala ?? '-' }} cm</div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="p-3 border rounded text-center bg-white shadow-sm border-left-danger">
                                    <i class="fas fa-tint text-danger mb-2"></i>
                                    <div class="small text-muted font-weight-bold text-uppercase" style="font-size: 0.65rem;">Gol. Darah</div>
                                    <div class="h6 font-weight-bold mb-0 text-dark">{{ $siswa->healthProfile->golongan_darah ?? '-' }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- B. PROFIL KESEHATAN KHUSUS --}}
                        <div class="card bg-gray-100 border-0 mb-4 shadow-sm">
<div class="card-body">
                                <div class="row">
                                    {{-- Kolom Kiri: Riwayat --}}
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <small class="text-muted font-weight-bold d-block text-uppercase" style="font-size: 0.6rem;">Riwayat Alergi:</small>
                                                <span class="text-dark font-weight-bold">{{ $siswa->healthProfile->riwayat_alergi ?? 'Tidak Ada' }}</span>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <small class="text-muted font-weight-bold d-block text-uppercase" style="font-size: 0.6rem;">Penyakit Bawaan:</small>
                                                <span class="text-dark font-weight-bold">{{ $siswa->healthProfile->penyakit_bawaan ?? 'Tidak Ada' }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Kolom Kanan: File Psikotest (BARU) --}}
                                    <div class="col-md-4 border-left">
                                        <small class="text-muted font-weight-bold d-block text-uppercase mb-2" style="font-size: 0.6rem;">Dokumen Psikotest:</small>
                                        @if($siswa->healthProfile && $siswa->healthProfile->file_psikotest)
                                            <a href="{{ asset('storage/' . $siswa->healthProfile->file_psikotest) }}" target="_blank" class="btn btn-sm btn-danger shadow-sm btn-block">
                                                <i class="fas fa-file-pdf mr-1"></i> Download PDF
                                            </a>
                                        @else
                                            <div class="btn btn-sm btn-light disabled btn-block text-muted border">
                                                <i class="fas fa-times-circle mr-1"></i> Belum Ada File
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- C. TIMELINE RIWAYAT PENYAKIT/KEJADIAN --}}
                    <h6 class="font-weight-bold text-dark small text-uppercase mb-3"><i class="fas fa-history mr-2"></i> Log Riwayat Kesehatan</h6>
                    <div class="table-responsive">
                    <table class="table table-bordered table-hover shadow-sm">
                        <thead class="bg-success text-white small">
                            <tr>
                                <th width="12%">Tanggal</th>
                                <th width="20%">Keluhan</th>
                                <th width="20%">Diagnosa</th>
                                <th width="30%">Tindakan & Obat</th>
                                <th width="18%" class="text-center">Aksi</th> {{-- Kolom Baru --}}
                            </tr>
                        </thead>
                        <tbody class="small text-dark">
                            @forelse($siswa->healthLogs as $log)
                                <tr>
                                    <td class="font-weight-bold">{{ \Carbon\Carbon::parse($log->tanggal_periksa)->format('d/m/Y') }}</td>
                                    <td>{{ $log->keluhan }}</td>
                                    <td><span class="badge badge-warning text-dark px-2">{{ $log->diagnosa ?? '-' }}</span></td>
                                    <td>
                                        <div class="font-weight-bold text-primary">{{ $log->tindakan ?? '' }}</div>
                                        <small class="text-muted">Obat: {{ $log->obat_diberikan ?? '-' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            {{-- Button Edit --}}
                                            <button type="button" 
                                                class="btn btn-sm btn-outline-primary edit-health-btn"
                                                data-id="{{ $log->id }}"
                                                data-tanggal="{{ $log->tanggal_periksa }}"
                                                data-keluhan="{{ $log->keluhan }}"
                                                data-diagnosa="{{ $log->diagnosa }}"
                                                data-tindakan="{{ $log->tindakan }}"
                                                data-obat="{{ $log->obat_diberikan }}"
                                                data-toggle="modal" 
                                                data-target="#editHealthLogModal">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            {{-- Form Hapus --}}
                                            <form action="{{ route('siswa.health-log.destroy', $log->id) }}" method="POST" onsubmit="return confirm('Hapus catatan medis ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger ml-1">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="fas fa-notes-medical fa-3x text-gray-200 mb-3"></i>
                                        <p class="text-muted mb-0">Belum ada riwayat pemeriksaan/sakit.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                    {{-- MODAL UPDATE PROFIL KESEHATAN --}}
                    <div class="modal fade" id="updateProfileModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            {{-- Tambahkan enctype="multipart/form-data" AGAR BISA UPLOAD FILE --}}
                            <form action="{{ route('siswa.health-profile.update', $siswa->id) }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg">
                                @csrf
                                <div class="modal-header bg-primary text-white border-0">
                                    <h5 class="modal-title font-weight-bold"><i class="fas fa-user-shield mr-2"></i> Profil Kesehatan & Psikotest</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="form-group">
                                        <label class="small font-weight-bold">Golongan Darah</label>
                                        <select name="golongan_darah" class="form-control">
                                            <option value="">- Pilih -</option>
                                            @foreach(['A', 'B', 'AB', 'O'] as $gol)
                                                <option value="{{ $gol }}" {{ ($siswa->healthProfile->golongan_darah ?? '') == $gol ? 'selected' : '' }}>{{ $gol }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="small font-weight-bold">Riwayat Alergi</label>
                                        <textarea name="riwayat_alergi" class="form-control" rows="2" placeholder="Alergi obat, makanan, dll">{{ $siswa->healthProfile->riwayat_alergi ?? '' }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label class="small font-weight-bold">Penyakit Bawaan</label>
                                        <textarea name="penyakit_bawaan" class="form-control" rows="2" placeholder="Asma, Jantung, dll">{{ $siswa->healthProfile->penyakit_bawaan ?? '' }}</textarea>
                                    </div>
                                    
                                    <hr>
                                    
                                    {{-- INPUT FILE PSIKOTEST BARU --}}
                                    <div class="form-group">
                                        <label class="small font-weight-bold text-danger"><i class="fas fa-file-pdf mr-1"></i> Upload Hasil Psikotest (PDF)</label>
                                        
                                        {{-- Tampilkan info jika file sudah ada --}}
                                        @if($siswa->healthProfile && $siswa->healthProfile->file_psikotest)
                                            <div class="alert alert-success py-2 px-3 small mb-2">
                                                <i class="fas fa-check mr-1"></i> File saat ini: 
                                                <a href="{{ asset('storage/' . $siswa->healthProfile->file_psikotest) }}" target="_blank" class="font-weight-bold text-success text-decoration-none">Lihat File</a>
                                            </div>
                                        @endif

                                        <div class="custom-file">
                                            <input type="file" name="file_psikotest" class="custom-file-input" id="psikotestFile" accept="application/pdf">
                                            <label class="custom-file-label" for="psikotestFile">Pilih file PDF (Max 5MB)...</label>
                                        </div>
                                        <small class="text-muted mt-1 d-block">Biarkan kosong jika tidak ingin mengubah file yang sudah ada.</small>
                                    </div>

                                </div>
                                <div class="modal-footer border-0">
                                    <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary px-4 font-weight-bold">Simpan Profil</button>
                                </div>
                            </form>
                        </div>
                    </div>
                     {{-- MODAL TAMBAH LOG KESEHATAN --}}

                    <div class="modal fade" id="addHealthLogModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <form action="{{ route('siswa.health-log.store') }}" method="POST" class="modal-content border-0 shadow-lg">
                                @csrf
                                <input type="hidden" name="siswa_id" value="{{ $siswa->id }}">
                                <div class="modal-header bg-success text-white border-0">
                                    <h5 class="modal-title font-weight-bold"><i class="fas fa-plus-circle mr-2"></i> Input Rekam Medis</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="form-group">
                                        <label class="small font-weight-bold">Tanggal Pemeriksaan</label>
                                        <input type="date" name="tanggal_periksa" class="form-control" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="small font-weight-bold">Keluhan</label>
                                        <input type="text" name="keluhan" class="form-control" placeholder="Contoh: Demam, Pusing, Luka Jatuh" required>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="small font-weight-bold">Diagnosa (Opsional)</label>
                                                <input type="text" name="diagnosa" class="form-control" placeholder="Flu / Maag">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="small font-weight-bold">Obat (Opsional)</label>
                                                <input type="text" name="obat_diberikan" class="form-control" placeholder="Paracetamol">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="small font-weight-bold">Tindakan</label>
                                        <textarea name="tindakan" class="form-control" rows="2" placeholder="Contoh: Istirahat di UKS / Dirujuk ke Puskesmas"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-success px-4 font-weight-bold">Simpan Rekam Medis</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    {{-- MODAL EDIT LOG KESEHATAN --}}
                    <div class="modal fade" id="editHealthLogModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <form id="editHealthForm" action="" method="POST" class="modal-content border-0 shadow-lg">
                            @csrf
                            @method('PUT')
                            <div class="modal-header bg-primary text-white border-0">
                                <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-2"></i> Edit Rekam Medis</h5>
                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="form-group">
                                    <label class="small font-weight-bold">Tanggal Pemeriksaan</label>
                                    <input type="date" name="tanggal_periksa" id="edit_tanggal" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label class="small font-weight-bold">Keluhan</label>
                                    <input type="text" name="keluhan" id="edit_keluhan" class="form-control" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="small font-weight-bold">Diagnosa</label>
                                            <input type="text" name="diagnosa" id="edit_diagnosa" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="small font-weight-bold">Obat</label>
                                            <input type="text" name="obat_diberikan" id="edit_obat" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="small font-weight-bold">Tindakan</label>
                                    <textarea name="tindakan" id="edit_tindakan" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer border-0 pt-0">
                                <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary px-4 font-weight-bold">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                    </div>
                    </div>
                    
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
    $(document).ready(function() {
        $('.edit-health-btn').on('click', function() {
            // Ambil data dari attribute tombol
            const id = $(this).data('id');
            const tanggal = $(this).data('tanggal');
            const keluhan = $(this).data('keluhan');
            const diagnosa = $(this).data('diagnosa');
            const tindakan = $(this).data('tindakan');
            const obat = $(this).data('obat');

            // Set action form secara dinamis
            $('#editHealthForm').attr('action', '/admin/siswa-health/log-update/' + id);

            // Isi inputan modal
            $('#edit_tanggal').val(tanggal);
            $('#edit_keluhan').val(keluhan);
            $('#edit_diagnosa').val(diagnosa);
            $('#edit_tindakan').val(tindakan);
            $('#edit_obat').val(obat);
        });
    });
</script>
<script>
    $(document).ready(function() {
        // 1. Cek apakah ada hash di URL (misal: #kesehatan)
        var hash = window.location.hash;
        
        if (hash) {
            // 2. Cari tab-link yang href-nya sama dengan hash
            // Lalu perintahkan untuk "show"
            $('.nav-tabs a[href="' + hash + '"]').tab('show');
        }

        // 3. (Opsional) Update hash di URL saat user klik tab secara manual
        // Agar jika user refresh sendiri, dia tetap di tab terakhir
        $('.nav-tabs a').on('click', function() {
            window.location.hash = $(this).attr('href');
        });
    });
</script>
<script>
    // Agar nama file muncul di input type file Bootstrap 4
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
</script>
@endsection