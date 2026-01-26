@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Data Guru</h6>
        </div>
        <div class="card-body">
            {{-- Tambahkan enctype untuk handle upload file --}}
            <form action="{{ route('guru.update', $guru->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    {{-- Kolom Kiri --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>NIP</label>
                            <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror" value="{{ old('nip', $guru->nip) }}">
                            @error('nip') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap', $guru->nama_lengkap) }}">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Gelar Depan</label>
                                <input type="text" name="gelar_depan" class="form-control" value="{{ old('gelar_depan', $guru->gelar_depan) }}" placeholder="Contoh: Drs.">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Gelar Belakang</label>
                                <input type="text" name="gelar_belakang" class="form-control" value="{{ old('gelar_belakang', $guru->gelar_belakang) }}" placeholder="Contoh: S.Pd">
                            </div>
                        </div>
                    </div>

                    {{-- Kolom Kanan --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-control">
                                <option value="L" {{ old('jenis_kelamin', $guru->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin', $guru->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>No. HP</label>
                            <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $guru->no_hp) }}">
                        </div>

                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $guru->email) }}">
                        </div>

                        <div class="mb-3">
                            <label>Foto Profil</label>
                            <input type="file" name="foto" class="form-control mb-2">
                            
                            {{-- Tampilkan foto lama jika ada --}}
                            @if($guru->foto)
                                <div class="text-muted small">Foto Saat Ini:</div>
                                <img src="{{ asset('storage/' . $guru->foto) }}" alt="Foto Guru" width="100" class="img-thumbnail mt-1">
                            @endif
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update Data</button>
                <a href="{{ route('guru.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection