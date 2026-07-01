@extends('layouts.admin')

@section('title', 'Edit Data Siswa')

@section('content')

<form action="{{ route('siswa.update', $siswa->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- HEADER & TOMBOL SAVE STICKY --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Edit Data Siswa</h1>
            <p class="text-muted small mb-0">Perbarui data siswa: <strong>{{ $siswa->nama_lengkap }}</strong></p>
        </div>
        <div>
            <a href="{{ route('siswa.show', $siswa->id) }}" class="btn btn-secondary shadow-sm mr-2">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Batal
            </a>
            <button type="submit" class="btn btn-warning shadow-lg px-4 font-weight-bold text-dark">
                <i class="fas fa-save fa-sm mr-2"></i> UPDATE DATA
            </button>
        </div>
    </div>

    {{-- ERROR FEEDBACK --}}
    @if($errors->any())
        <div class="alert alert-danger border-left-danger shadow-sm mb-4" role="alert">
            <h6 class="alert-heading font-weight-bold"><i class="fas fa-exclamation-triangle mr-1"></i> Terjadi Kesalahan:</h6>
            <ul class="mb-0 small pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">

        {{-- === KOLOM KIRI: FOTO & IDENTITAS UTAMA === --}}
        <div class="col-xl-3 col-md-4 mb-4">
            
            {{-- 1. FOTO PROFIL --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-dark text-white">
                    <h6 class="m-0 font-weight-bold small text-uppercase">Foto Profil</h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3 position-relative">
                        <img id="previewImg" src="{{ $siswa->foto ? asset($siswa->foto) : asset('img/no-image.png') }}" 
                             class="img-fluid rounded border shadow-sm"
                             style="width: 150px; height: 200px; object-fit: cover;"
                             onerror="this.src='https://placehold.co/150x200?text=No+Image'">
                    </div>
                    <div class="custom-file text-left">
                        <input type="file" name="foto" class="custom-file-input" id="fotoInput" accept="image/*" onchange="previewFile(this)">
                        <label class="custom-file-label" for="fotoInput">Ganti Foto...</label>
                    </div>
                    <small class="text-muted d-block mt-2">Biarkan kosong jika tidak ingin mengganti.</small>
                </div>
            </div>

            {{-- 2. AKUN LOGIN --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-warning text-dark">
                    <h6 class="m-0 font-weight-bold small text-uppercase">Kredensial Login</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="small font-weight-bold">NISN (Username) <span class="text-danger">*</span></label>
                        <input type="text" name="nisn" class="form-control" value="{{ old('nisn', $siswa->nisn) }}" required>
                        <small class="text-muted">Username login akan ikut berubah.</small>
                    </div>
                </div>
            </div>

        </div>

        {{-- === KOLOM KANAN: DATA LENGKAP === --}}
        <div class="col-xl-9 col-md-8">

            {{-- A. IDENTITAS PRIBADI --}}
            <div class="card shadow mb-4 border-left-primary">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">A. Identitas Peserta Didik</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama_lengkap" class="form-control text-uppercase" value="{{ old('nama_lengkap', $siswa->nama_lengkap) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select name="jenis_kelamin" class="form-control" required>
                                    <option value="L" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold">NIK / No. Kitas</label>
                                <input type="text" name="nik" class="form-control" value="{{ old('nik', $siswa->nik) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold">Agama</label>
                                <select name="agama" class="form-control">
                                    @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agama)
                                        <option value="{{ $agama }}" {{ old('agama', $siswa->agama) == $agama ? 'selected' : '' }}>{{ $agama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $siswa->tempat_lahir) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', $siswa->tanggal_lahir) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">Kebutuhan Khusus</label>
                                <input type="text" name="kebutuhan_khusus" class="form-control" value="{{ old('kebutuhan_khusus', $siswa->kebutuhan_khusus) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- B. DATA AKADEMIK --}}
            <div class="card shadow mb-4 border-left-success">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">B. Data Akademik & Sekolah</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group bg-light p-2 border rounded">
                                <label class="small font-weight-bold text-success">Rombel / Kelas Saat Ini</label>
                                <select name="kelas_id" class="form-control">
                                    <option value="">-- Tidak Masuk Kelas --</option>
                                    @foreach($daftarKelas as $k)
                                        <option value="{{ $k->id }}" {{ (isset($currentKelasID) && $currentKelasID == $k->id) ? 'selected' : '' }}>
                                            {{ $k->nama_kelas }} (Tingkat {{ $k->tingkat }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted ml-1">Ubah untuk memindahkan kelas.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold">Sekolah Asal</label>
                                <input type="text" name="sekolah_asal" class="form-control" value="{{ old('sekolah_asal', $siswa->sekolah_asal) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">No. SKHUN</label>
                                <input type="text" name="skhun" class="form-control" value="{{ old('skhun', $siswa->skhun) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">No. Peserta UN</label>
                                <input type="text" name="no_peserta_un" class="form-control" value="{{ old('no_peserta_un', $siswa->no_peserta_un) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">No. Seri Ijazah</label>
                                <input type="text" name="no_seri_ijazah" class="form-control" value="{{ old('no_seri_ijazah', $siswa->no_seri_ijazah) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">No. Registrasi Akta Lahir</label>
                                <input type="text" name="no_registrasi_akta_lahir" class="form-control" value="{{ old('no_registrasi_akta_lahir', $siswa->no_registrasi_akta_lahir) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">NIPD</label>
                                <input type="text" name="nipd" class="form-control" value="{{ old('nipd', $siswa->nipd) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- C. ALAMAT TEMPAT TINGGAL --}}
            <div class="card shadow mb-4 border-left-info">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">C. Alamat Tempat Tinggal</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="small font-weight-bold">Alamat (Jalan)</label>
                        <textarea name="alamat" class="form-control" rows="2">{{ old('alamat', $siswa->alamat) }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-6">
                            <div class="form-group">
                                <label class="small font-weight-bold">RT</label>
                                <input type="text" name="rt" class="form-control" value="{{ old('rt', $siswa->rt) }}">
                            </div>
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="form-group">
                                <label class="small font-weight-bold">RW</label>
                                <input type="text" name="rw" class="form-control" value="{{ old('rw', $siswa->rw) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">Dusun</label>
                                <input type="text" name="dusun" class="form-control" value="{{ old('dusun', $siswa->dusun) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">Kelurahan / Desa</label>
                                <input type="text" name="kelurahan" class="form-control" value="{{ old('kelurahan', $siswa->kelurahan) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">Kecamatan</label>
                                <input type="text" name="kecamatan" class="form-control" value="{{ old('kecamatan', $siswa->kecamatan) }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="small font-weight-bold">Kode Pos</label>
                                <input type="text" name="kode_pos" class="form-control" value="{{ old('kode_pos', $siswa->kode_pos) }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Jenis Tinggal</label>
                                <select name="jenis_tinggal" class="form-control">
                                    @foreach(['Bersama Orang Tua', 'Wali', 'Kos', 'Asrama', 'Panti Asuhan'] as $jenis)
                                        <option value="{{ $jenis }}" {{ old('jenis_tinggal', $siswa->jenis_tinggal) == $jenis ? 'selected' : '' }}>{{ $jenis }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Transportasi</label>
                                <select name="alat_transportasi" class="form-control">
                                    @foreach(['Jalan Kaki', 'Kendaraan Pribadi', 'Angkutan Umum', 'Jemputan Sekolah'] as $trans)
                                        <option value="{{ $trans }}" {{ old('alat_transportasi', $siswa->alat_transportasi) == $trans ? 'selected' : '' }}>{{ $trans }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">Telepon Rumah</label>
                                <input type="text" name="telepon" class="form-control" value="{{ old('telepon', $siswa->telepon) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">No. HP</label>
                                <input type="text" name="hp" class="form-control" value="{{ old('hp', $siswa->hp) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">E-Mail</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $siswa->email) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- D. DATA AYAH KANDUNG --}}
            <div class="card shadow mb-4" style="border-top: 4px solid #4e73df;">
                <div class="card-header py-3 bg-gray-100">
                    <h6 class="m-0 font-weight-bold text-gray-800"><i class="fas fa-male mr-2"></i>D. Data Ayah Kandung</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold">Nama Ayah</label>
                                <input type="text" name="ayah_nama" class="form-control" value="{{ old('ayah_nama', $siswa->ayah->nama ?? '') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold">NIK Ayah</label>
                                <input type="text" name="ayah_nik" class="form-control" value="{{ old('ayah_nik', $siswa->ayah->nik ?? '') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Tahun Lahir</label>
                                <input type="number" name="ayah_tahun_lahir" class="form-control" placeholder="YYYY" value="{{ old('ayah_tahun_lahir', $siswa->ayah->tahun_lahir ?? '') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Pendidikan</label>
                                <select name="ayah_pendidikan" class="form-control">
                                    <option value="">Pilih...</option>
                                    @foreach(['SD', 'SMP', 'SMA', 'D3', 'S1', 'S2', 'S3'] as $p)
                                        <option value="{{ $p }}" {{ old('ayah_pendidikan', $siswa->ayah->jenjang_pendidikan ?? '') == $p ? 'selected' : '' }}>{{ $p }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Pekerjaan</label>
                                <input type="text" name="ayah_pekerjaan" class="form-control" value="{{ old('ayah_pekerjaan', $siswa->ayah->pekerjaan ?? '') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Penghasilan</label>
                                <select name="ayah_penghasilan" class="form-control">
                                    @foreach(['< 500 Ribu', '500 Ribu - 1 Juta', '1 Juta - 2 Juta', '2 Juta - 5 Juta', '> 5 Juta'] as $ph)
                                        <option value="{{ $ph }}" {{ old('ayah_penghasilan', $siswa->ayah->penghasilan ?? '') == $ph ? 'selected' : '' }}>{{ $ph }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- E. DATA IBU KANDUNG --}}
            <div class="card shadow mb-4" style="border-top: 4px solid #e74a3b;">
                <div class="card-header py-3 bg-gray-100">
                    <h6 class="m-0 font-weight-bold text-gray-800"><i class="fas fa-female mr-2"></i>E. Data Ibu Kandung</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold">Nama Ibu</label>
                                <input type="text" name="ibu_nama" class="form-control" value="{{ old('ibu_nama', $siswa->ibu->nama ?? '') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold">NIK Ibu</label>
                                <input type="text" name="ibu_nik" class="form-control" value="{{ old('ibu_nik', $siswa->ibu->nik ?? '') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Tahun Lahir</label>
                                <input type="number" name="ibu_tahun_lahir" class="form-control" placeholder="YYYY" value="{{ old('ibu_tahun_lahir', $siswa->ibu->tahun_lahir ?? '') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Pendidikan</label>
                                <select name="ibu_pendidikan" class="form-control">
                                    <option value="">Pilih...</option>
                                    @foreach(['SD', 'SMP', 'SMA', 'D3', 'S1', 'S2', 'S3'] as $p)
                                        <option value="{{ $p }}" {{ old('ibu_pendidikan', $siswa->ibu->jenjang_pendidikan ?? '') == $p ? 'selected' : '' }}>{{ $p }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Pekerjaan</label>
                                <input type="text" name="ibu_pekerjaan" class="form-control" value="{{ old('ibu_pekerjaan', $siswa->ibu->pekerjaan ?? '') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Penghasilan</label>
                                <select name="ibu_penghasilan" class="form-control">
                                    @foreach(['< 500 Ribu', '500 Ribu - 1 Juta', '1 Juta - 2 Juta', '2 Juta - 5 Juta', '> 5 Juta'] as $ph)
                                        <option value="{{ $ph }}" {{ old('ibu_penghasilan', $siswa->ibu->penghasilan ?? '') == $ph ? 'selected' : '' }}>{{ $ph }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- F. DATA WALI --}}
            <div class="card shadow mb-4" style="border-top: 4px solid #f6c23e;">
                <div class="card-header py-3 bg-gray-100">
                    <h6 class="m-0 font-weight-bold text-gray-800"><i class="fas fa-user-friends mr-2"></i>F. Data Wali (Opsional)</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold">Nama Wali</label>
                                <input type="text" name="wali_nama" class="form-control" value="{{ old('wali_nama', $siswa->wali->nama ?? '') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold">NIK Wali</label>
                                <input type="text" name="wali_nik" class="form-control" value="{{ old('wali_nik', $siswa->wali->nik ?? '') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Tahun Lahir</label>
                                <input type="number" name="wali_tahun_lahir" class="form-control" placeholder="YYYY" value="{{ old('wali_tahun_lahir', $siswa->wali->tahun_lahir ?? '') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Pendidikan</label>
                                <select name="wali_pendidikan" class="form-control">
                                    <option value="">Pilih...</option>
                                    @foreach(['SD', 'SMP', 'SMA', 'D3', 'S1', 'S2', 'S3'] as $p)
                                        <option value="{{ $p }}" {{ old('wali_pendidikan', $siswa->wali->jenjang_pendidikan ?? '') == $p ? 'selected' : '' }}>{{ $p }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Pekerjaan</label>
                                <input type="text" name="wali_pekerjaan" class="form-control" value="{{ old('wali_pekerjaan', $siswa->wali->pekerjaan ?? '') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Penghasilan</label>
                                <select name="wali_penghasilan" class="form-control">
                                    @foreach(['< 500 Ribu', '500 Ribu - 1 Juta', '1 Juta - 2 Juta', '2 Juta - 5 Juta', '> 5 Juta'] as $ph)
                                        <option value="{{ $ph }}" {{ old('wali_penghasilan', $siswa->wali->penghasilan ?? '') == $ph ? 'selected' : '' }}>{{ $ph }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- G. DATA PERIODIK & ZONASI --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-secondary text-white">
                    <h6 class="m-0 font-weight-bold small text-uppercase">G. Data Periodik & Zonasi</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Tinggi Badan (cm)</label>
                                <input type="number" name="tinggi_badan" class="form-control" value="{{ old('tinggi_badan', $siswa->tinggi_badan) }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Berat Badan (kg)</label>
                                <input type="number" name="berat_badan" class="form-control" value="{{ old('berat_badan', $siswa->berat_badan) }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Lingkar Kepala (cm)</label>
                                <input type="number" name="lingkar_kepala" class="form-control" value="{{ old('lingkar_kepala', $siswa->lingkar_kepala) }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Jarak ke Sekolah (km)</label>
                                <input type="number" name="jarak_ke_sekolah" class="form-control" value="{{ old('jarak_ke_sekolah', $siswa->jarak_ke_sekolah) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold">Anak ke-berapa</label>
                                <input type="number" name="anak_ke" class="form-control" value="{{ old('anak_ke', $siswa->anak_ke) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold">Jml Saudara Kandung</label>
                                <input type="number" name="jml_saudara_kandung" class="form-control" value="{{ old('jml_saudara_kandung', $siswa->jml_saudara_kandung) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold">Lintang</label>
                                <input type="text" name="lintang" class="form-control" placeholder="-6.123456" value="{{ old('lintang', $siswa->lintang) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold">Bujur</label>
                                <input type="text" name="bujur" class="form-control" placeholder="106.123456" value="{{ old('bujur', $siswa->bujur) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- H. KESEJAHTERAAN & BANK --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-secondary text-white">
                    <h6 class="m-0 font-weight-bold small text-uppercase">H. Kesejahteraan & Bank</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold">Nomor KK (Kartu Keluarga)</label>
                                <input type="text" name="no_kk" class="form-control" value="{{ old('no_kk', $siswa->no_kk) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold">Nomor KKS (Kartu Keluarga Sejahtera)</label>
                                <input type="text" name="no_kks" class="form-control" value="{{ old('no_kks', $siswa->no_kks) }}">
                            </div>
                        </div>
                    </div>

                    <hr>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">Penerima KPS/PKH?</label>
                                <select name="penerima_kps" class="form-control">
                                    <option value="0" {{ old('penerima_kps', $siswa->penerima_kps) == 0 ? 'selected' : '' }}>Tidak</option>
                                    <option value="1" {{ old('penerima_kps', $siswa->penerima_kps) == 1 ? 'selected' : '' }}>Ya</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="small font-weight-bold">Nomor KPS</label>
                                <input type="text" name="no_kps" class="form-control" value="{{ old('no_kps', $siswa->no_kps) }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">Penerima KIP (Kartu Indonesia Pintar)?</label>
                                <select name="penerima_kip" class="form-control">
                                    <option value="0" {{ old('penerima_kip', $siswa->penerima_kip) == 0 ? 'selected' : '' }}>Tidak</option>
                                    <option value="1" {{ old('penerima_kip', $siswa->penerima_kip) == 1 ? 'selected' : '' }}>Ya</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">Nomor KIP</label>
                                <input type="text" name="no_kip" class="form-control" value="{{ old('no_kip', $siswa->no_kip) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">Nama tertera di KIP</label>
                                <input type="text" name="nama_di_kip" class="form-control" value="{{ old('nama_di_kip', $siswa->nama_di_kip) }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">Nama Bank (PIP)</label>
                                <input type="text" name="bank" class="form-control" placeholder="Contoh: BRI / BNI" value="{{ old('bank', $siswa->bank) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">No. Rekening Bank</label>
                                <input type="text" name="no_rekening_bank" class="form-control" value="{{ old('no_rekening_bank', $siswa->no_rekening_bank) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">Rekening Atas Nama</label>
                                <input type="text" name="rekening_atas_nama" class="form-control" value="{{ old('rekening_atas_nama', $siswa->rekening_atas_nama) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>

@endsection

@section('scripts')
<script>
    // Preview Foto
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