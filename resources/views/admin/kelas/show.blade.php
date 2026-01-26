@extends('layouts.admin')
@section('title', 'Detail Kelas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 text-gray-800 mb-0">Kelas: {{ $kelas->nama_kelas }}</h1>
        <p class="text-muted">
            Wali Kelas: {{ $kelas->waliKelas->nama_lengkap ?? '-' }} | 
            <span class="badge bg-warning text-dark">
        Tahun Ajaran: {{ $activeTa->tahun }}
    </span>
        </p>
    </div>
    <a href="{{ route('kelas.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Siswa ({{ $kelas->siswas->count() }})</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable">
                        <thead class="bg-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                    {{-- Perhatikan: Disini pakai $siswas (variable dari controller), BUKAN $kelas->siswas --}}
                        @forelse($siswas as $index => $siswa)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $siswa->nis }}</td>
                            <td>{{ $siswa->nama_lengkap }}</td>
                            <td class="text-center">
                                {{-- Route Delete butuh 2 parameter: ID Kelas dan ID Siswa --}}
                                <form action="{{ route('kelas.removeSiswa', ['id_kelas' => $kelas->id, 'id_siswa' => $siswa->id]) }}" 
                                    method="POST" onsubmit="return confirm('Keluarkan siswa?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="fas fa-times"></i></button>
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
        </div>
    </div>

    <div class="col-md-4">
        
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">Tambah Siswa Manual</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('kelas.addSiswa', $kelas->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="small fw-bold">Pilih Siswa (Yang belum punya kelas)</label>
                        <select name="siswa_id" class="form-control select2" required>
                            <option value="">-- Cari Nama Siswa --</option>
                            @foreach($siswaNonKelas as $snk)
                                <option value="{{ $snk->id }}">{{ $snk->nis }} - {{ $snk->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-plus-circle"></i> Masukkan ke Kelas
                    </button>
                </form>
            </div>
        </div>

        <div class="card shadow mb-4 border-left-info">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">Import via Excel</h6>
            </div>
            <div class="card-body">
                <p class="small text-muted mb-2">Upload file Excel (.xlsx) yang berisi kolom <strong>nis</strong>.</p>
                <form action="{{ route('kelas.importSiswa', $kelas->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <input type="file" name="file" class="form-control form-control-sm" required>
                    </div>
                    <button type="submit" class="btn btn-info text-white btn-sm w-100">
                        <i class="fas fa-file-upload"></i> Upload & Masukkan
                    </button>
                </form>
                <div class="mt-2 text-center">
                    <a href="{{asset('templates/template_data_kelas.xlsx')}}" download class="small text-decoration-underline">Contoh Template Excel</a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection