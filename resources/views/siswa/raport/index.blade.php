@extends('layouts.admin') 

@section('content')
<div class="container py-4">
    <h3 class="mb-4 font-weight-bold">Arsip Raport Saya</h3>
    <div class="col-md-6 mb-3">
                <a href="{{ route('landing') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
    @if($riwayatKelas->isEmpty())
        <div class="alert alert-info">Belum ada riwayat kelas.</div>
    @else
    
        <div class="row">
            
            @foreach($riwayatKelas as $kelas)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">{{ $kelas->nama_kelas }}</h5>
                            <small>Tahun Ajaran: {{ $listTahun[$kelas->pivot->tahun_ajaran_id] ?? '-' }}</small>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">
                                Wali Kelas: {{ $kelas->waliKelas->nama_lengkap ?? 'Belum ditentukan' }}
                            </p>
                            <hr>
                            
                            {{-- LOGIC TOMBOL BARU --}}
                            <div class="d-grid gap-2">
                                
                                {{-- CEK FILE GANJIL --}}
                                @php
                                    $urlGanjil = $fileMap[$kelas->id]['ganjil'] ?? null;
                                @endphp
                                <button type="button" 
                                        onclick="checkRaport('{{ $urlGanjil }}', 'Semester Ganjil')" 
                                        class="btn btn-outline-primary w-100">
                                    <i class="fas fa-file-alt"></i> Raport Semester Ganjil
                                </button>

                                {{-- CEK FILE GENAP --}}
                                @php
                                    $urlGenap = $fileMap[$kelas->id]['genap'] ?? null;
                                @endphp
                                <button type="button" 
                                        onclick="checkRaport('{{ $urlGenap }}', 'Semester Genap')"
                                        class="btn btn-outline-success w-100">
                                    <i class="fas fa-file-alt"></i> Raport Semester Genap
                                </button>

                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<div class="modal fade" id="pdfModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-width: 90%; height: 90%;">
        <div class="modal-content h-100">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfModalTitle">Preview Raport</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <iframe id="pdfIframe" src="" width="100%" height="100%" style="border:none;"></iframe>
            </div>
            <div class="modal-footer">
                <a href="#" id="downloadBtn" class="btn btn-primary" download target="_blank">
                    <i class="fas fa-download"></i> Download PDF
                </a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-circle"></i> Maaf</h5>
                
                {{-- PERHATIKAN: Ganti 'data-bs-dismiss' jadi 'data-dismiss' --}}
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body text-center py-4">
                <h4>Raport Belum Rilis</h4>
                <p class="text-muted mb-0">
                    Raport untuk <span id="errorSemester" class="fw-bold"></span> belum diunggah.
                </p>
            </div>

            <div class="modal-footer">
                {{-- PERHATIKAN: Ganti 'data-bs-dismiss' jadi 'data-dismiss' --}}
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Mengerti</button>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT JAVASCRIPT --}}
<script>
    function checkRaport(url, semesterName) {
        // Jika URL kosong (null/undefined dari PHP), berarti file belum ada
        if (!url) {
            // Tampilkan Modal Error
            document.getElementById('errorSemester').innerText = semesterName;
            var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            errorModal.show();
        } else {
            // Jika URL ada, Tampilkan Modal Preview
            document.getElementById('pdfModalTitle').innerText = 'Preview Raport - ' + semesterName;
            document.getElementById('pdfIframe').src = url;
            document.getElementById('downloadBtn').href = url;
            
            var pdfModal = new bootstrap.Modal(document.getElementById('pdfModal'));
            pdfModal.show();
        }
    }
</script>
@endsection