@extends('layouts.admin')

@section('title', 'Detail Guru')

@section('content')

{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800 fw-bold">Detail Profil Guru</h1>
        <p class="text-muted small mb-0">Informasi lengkap data kepegawaian dan portofolio.</p>
    </div>
    <a href="{{ route('guru.index') }}" class="btn btn-secondary btn-sm shadow-sm">
        <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
    </a>
</div>

<div class="row">
    
    {{-- KOLOM KIRI: Foto, Kontak Utama & Portofolio --}}
    <div class="col-lg-4 mb-4">
        
        {{-- Card Profil --}}
        <div class="card shadow-sm border-0 mb-4 text-center overflow-hidden">
            <div class="card-header bg-primary text-white py-4" style="background: linear-gradient(45deg, #0b9422, #0b9422);">
<br>
            </div>
            <div class="card-body p-4 position-block">
                {{-- Foto --}}
                @php
                    $imgSrc = $guru->foto ? asset('storage/' . $guru->foto) : asset('img/no-image.png');
                @endphp
                <img src="{{ $imgSrc }}" class="rounded-circle shadow bg-white p-1" 
                     style="width: 150px; height: 150px; object-fit: cover; margin-top: -80px; border: 4px solid white;"
                     onerror="this.src='https://placehold.co/150x150?text=No+Img';">
                
                <h4 class="fw-bold mt-3 text-dark mb-1">
                    {{ $guru->gelar_depan }} {{ $guru->nama_lengkap }} {{ $guru->gelar_belakang }}
                </h4>
                
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3">
                    {{ $guru->status_kepegawaian }}
                </span>

                <div class="mb-3">
                    {{-- No HP / WhatsApp --}}
                    <div class="mb-2">
                        <i class="fab fa-whatsapp text-success me-2"></i>
                        @if($guru->no_hp)
                            <span class="fw-bold text-dark">{{ $guru->no_hp }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>

                    {{-- Email --}}
                    <div>
                        <i class="fas fa-envelope text-secondary me-2"></i>
                        @if($guru->email)
                            <span class="text-dark">{{ $guru->email }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>
                <hr>

                {{-- Action Edit --}}
                <a href="{{ route('guru.edit', $guru->id) }}" class="btn btn-warning w-100 fw-bold text-white">
                    <i class="fas fa-pen-to-square me-1"></i> Edit Data Guru
                </a>
            </div>
        </div>

        {{-- Card Portofolio --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom fw-bold py-3">
                <i class="fas fa-file-pdf text-danger me-2"></i> Dokumen Portofolio
            </div>
            <div class="card-body text-center py-4">
                @if($guru->portofolio && \Illuminate\Support\Facades\File::exists(public_path($guru->portofolio)))
                    <i class="fas fa-file-pdf fa-4x text-danger mb-3"></i>
                    <h6 class="fw-bold text-dark">Portofolio Tersedia</h6>
                    <p class="text-muted small">Diunggah pada folder server.</p>
                    
                    <a href="{{ asset($guru->portofolio) }}" target="_blank" class="btn btn-danger w-100 rounded-pill shadow-sm">
                        <i class="fas fa-download me-1"></i> Lihat / Download PDF
                    </a>
                @else
                    <div class="py-3 opacity-50">
                        <i class="fas fa-folder-open fa-3x text-secondary mb-3"></i>
                        <h6 class="text-muted">Belum ada Portofolio</h6>
                        <small class="d-block text-muted">Guru belum mengupload dokumen portofolio di profil mereka.</small>
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- KOLOM KANAN: Detail Biodata --}}
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Pribadi & Kepegawaian</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <tbody>
                            {{-- Identitas --}}
                            <tr>
                                <th width="35%" class="text-muted ps-4">NIY / Username</th>
                                <td class="fw-bold text-dark">{{ $guru->niy }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted ps-4">NUPTK</th>
                                <td>{{ $guru->nuptk ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted ps-4">Jenis Kelamin</th>
                                <td>{{ $guru->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted ps-4">Tempat, Tanggal Lahir</th>
                                <td>
                                    {{ $guru->tempat_lahir }}, 
                                    {{ \Carbon\Carbon::parse($guru->tanggal_lahir)->translatedFormat('d F Y') }}
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted ps-4">Alamat Domisili</th>
                                <td>{{ $guru->alamat ?? '-' }}</td>
                            </tr>
                            
                            {{-- Divider --}}
                            <tr class="table-light"><td colspan="2"></td></tr>

                            {{-- Pendidikan --}}
                            <tr>
                                <th class="text-muted ps-4">Pendidikan Terakhir</th>
                                <td>
                                    <span class="badge bg-info text-dark">{{ $guru->pendidikan_terakhir }}</span>
                                    <span class="text-muted ms-1">({{ $guru->tahun_lulus ?? 'Thn -' }})</span>
                                </td>
                            </tr>
                            
                            {{-- Karir --}}
                            <tr>
                                <th class="text-muted ps-4">Status Kepegawaian</th>
                                <td class="fw-bold">{{ $guru->status_kepegawaian }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted ps-4">Tugas Tambahan</th>
                                <td>{{ $guru->tugas_tambahan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted ps-4">TMT Sekolah</th>
                                <td>
                                    @if($guru->tmt_sekolah)
                                        {{ \Carbon\Carbon::parse($guru->tmt_sekolah)->translatedFormat('d F Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted ps-4">Masa Kerja (SD)</th>
                                <td>{{ $guru->masa_kerja_sd ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted ps-4">Masa Kerja (Total)</th>
                                <td>{{ $guru->masa_kerja_total ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Section Data Login (Read Only) --}}
        <div class="card shadow-sm border-0 border-start border-4 border-warning">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="fw-bold text-dark mb-1">Akun Login Guru</h6>
                    <p class="text-muted small mb-0">Username dan email yang terhubung dengan sistem.</p>
                </div>
                <div class="text-end">
                    <div class="badge bg-light text-dark border mb-1">Username: {{ $guru->user->username ?? $guru->niy }}</div>
                    <div class="d-block text-muted small">{{ $guru->user->email ?? '-' }}</div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection