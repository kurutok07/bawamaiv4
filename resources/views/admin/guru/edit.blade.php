@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Data Guru: {{ $guru->nama_lengkap }}</h6>
        </div>
        <div class="card-body">
            
            {{-- Tampilkan Error Validasi (Jika ada) --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('guru.update', $guru->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Hidden Input untuk validasi unik user_id di controller --}}
                <input type="hidden" name="user_id_check" value="{{ $guru->user_id }}">

                <div class="row">
                    {{-- === KOLOM KIRI (Identitas & Kepegawaian) === --}}
                    <div class="col-md-6 border-right">
                        <h6 class="text-muted mb-3 font-weight-bold"><i class="fas fa-id-card"></i> Identitas Utama</h6>

                        <div class="form-group mb-3">
                            <label>Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap', $guru->nama_lengkap) }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Gelar Depan</label>
                                <input type="text" name="gelar_depan" class="form-control" value="{{ old('gelar_depan', $guru->gelar_depan) }}" placeholder="Cth: Dr.">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Gelar Belakang</label>
                                <input type="text" name="gelar_belakang" class="form-control" value="{{ old('gelar_belakang', $guru->gelar_belakang) }}" placeholder="Cth: S.Pd">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="text-primary font-weight-bold">NUPTK (User Login) <span class="text-danger">*</span></label>
                            <input type="number" name="nuptk" class="form-control" value="{{ old('nuptk', $guru->nuptk) }}" required>
                            <small class="text-muted">Digunakan sebagai username login.</small>
                        </div>

                        <div class="form-group mb-3">
                            <label>NIP</label>
                            <input type="number" name="nip" class="form-control" value="{{ old('nip', $guru->nip) }}">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Jenis Kelamin <span class="text-danger">*</span></label>
                                <select name="jenis_kelamin" class="form-control" required>
                                    <option value="L" {{ old('jenis_kelamin', $guru->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin', $guru->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Status Kepegawaian <span class="text-danger">*</span></label>
                                <select name="status_kepegawaian" class="form-control" required>
                                    <option value="GTY" {{ old('status_kepegawaian', $guru->status_kepegawaian) == 'GTY' ? 'selected' : '' }}>GTY</option>
                                    <option value="PTY" {{ old('status_kepegawaian', $guru->status_kepegawaian) == 'PTY' ? 'selected' : '' }}>PTY</option>
                                    <option value="GTTY" {{ old('status_kepegawaian', $guru->status_kepegawaian) == 'GTTY' ? 'selected' : '' }}>GTTY</option>
                                    <option value="PTTY" {{ old('status_kepegawaian', $guru->status_kepegawaian) == 'PTTY' ? 'selected' : '' }}>PTTY</option>
                                    <option value="HONORER" {{ old('status_kepegawaian', $guru->status_kepegawaian) == 'HONORER' ? 'selected' : '' }}>Honorer</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label>Tugas Utama/Tambahan</label>
                            <input type="text" name="tugas_tambahan" class="form-control" value="{{ old('tugas_tambahan', $guru->tugas_tambahan) }}" placeholder="Cth: Wali Kelas, Kepala Lab">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>No. HP</label>
                                <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $guru->no_hp) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $guru->email) }}">
                            </div>
                        </div>
                    </div>

                    {{-- === KOLOM KANAN (Biodata, Pendidikan & Foto) === --}}
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3 font-weight-bold"><i class="fas fa-user-clock"></i> Detail Pribadi & Karir</h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Tempat Lahir <span class="text-danger">*</span></label>
                                <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $guru->tempat_lahir) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', $guru->tanggal_lahir) }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Pendidikan Terakhir <span class="text-danger">*</span></label>
                                <select name="pendidikan_terakhir" class="form-control" required>
                                    <option value="S1" {{ old('pendidikan_terakhir', $guru->pendidikan_terakhir) == 'S1' ? 'selected' : '' }}>S1</option>
                                    <option value="S2" {{ old('pendidikan_terakhir', $guru->pendidikan_terakhir) == 'S2' ? 'selected' : '' }}>S2</option>
                                    <option value="S3" {{ old('pendidikan_terakhir', $guru->pendidikan_terakhir) == 'S3' ? 'selected' : '' }}>S3</option>
                                    <option value="D3" {{ old('pendidikan_terakhir', $guru->pendidikan_terakhir) == 'D3' ? 'selected' : '' }}>D3</option>
                                    <option value="SMA" {{ old('pendidikan_terakhir', $guru->pendidikan_terakhir) == 'SMA' ? 'selected' : '' }}>SMA</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Tahun Lulus</label>
                                <input type="number" name="tahun_lulus" class="form-control" value="{{ old('tahun_lulus', $guru->tahun_lulus) }}">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label>TMT Sekolah</label>
                            <input type="date" name="tmt_sekolah" class="form-control" value="{{ old('tmt_sekolah', $guru->tmt_sekolah) }}">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Masa Kerja (SD)</label>
                                <input type="text" name="masa_kerja_sd" class="form-control" value="{{ old('masa_kerja_sd', $guru->masa_kerja_sd) }}" placeholder="Cth: 2 Tahun">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Masa Kerja (Total)</label>
                                <input type="text" name="masa_kerja_total" class="form-control" value="{{ old('masa_kerja_total', $guru->masa_kerja_total) }}" placeholder="Cth: 5 Tahun">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label>Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control" rows="2">{{ old('alamat', $guru->alamat) }}</textarea>
                        </div>

                        <div class="form-group mb-3 p-3 bg-light rounded border">
                            <label class="font-weight-bold">Foto Profil</label>
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    {{-- Menggunakan Accessor foto_url yang sudah kita buat di Model --}}
                                    <img src="{{ $guru->foto_url ?? asset('img/no-image.png') }}" alt="Foto Guru" width="80" height="80" class="img-thumbnail rounded-circle">
                                </div>
                                <div class="col">
                                    <input type="file" name="foto" class="form-control-file">
                                    <small class="text-muted d-block mt-1">Biarkan kosong jika tidak ingin mengganti foto.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('guru.index') }}" class="btn btn-secondary mr-2">Batal</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection