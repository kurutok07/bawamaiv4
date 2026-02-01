@extends('layouts.admin')

@section('content')
<style>
    /* UX Refinements */
    .card-title-custom { font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; font-size: 0.85rem; }
    .badge-soft { padding: 5px 10px; border-radius: 50px; font-weight: 600; font-size: 0.7rem; }
    .badge-video { background-color: #fee2e2; color: #991b1b; }
    .badge-pdf { background-color: #dbeafe; color: #1e40af; }
    .badge-link { background-color: #f3f4f6; color: #374151; }
    
    /* Timeline Modern */
    .timeline-wrapper { position: relative; padding-left: 20px; border-left: 2px solid #e5e7eb; margin-left: 10px; }
    .timeline-card { 
        position: relative; 
        margin-bottom: 20px; 
        background: #fff; 
        border: 1px solid #f3f4f6; 
        border-radius: 10px; 
        padding: 15px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        transition: transform 0.2s;
    }
    .timeline-card:hover { transform: translateX(5px); border-color: #d1d5db; }
    .timeline-dot {
        position: absolute; left: -26px; top: 15px;
        width: 12px; height: 12px; border-radius: 50%;
        background: #3bf667; border: 2px solid white; box-shadow: 0 0 0 2px #dbeafe;
    }
    .folder-badge { font-size: 0.7rem; color: #6b7280; background: #f9fafb; padding: 2px 8px; border-radius: 4px; border: 1px solid #e5e7eb; display: inline-block; margin-bottom: 5px; }
</style>

<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            @if(Auth::user()->role == 'guru')
                <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Analytics Kelas Anda</h1>
                <p class="mb-0 text-muted">Pantau interaksi siswa terhadap materi pembelajaran Anda.</p>
            @else
                <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Analytics Global</h1>
                <p class="mb-0 text-muted">Statistik aktivitas LMS seluruh sekolah.</p>
            @endif
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-white shadow-sm border rounded-pill px-3 font-weight-bold text-primary hover-scale">
            <i class="fas fa-arrow-left mr-2"></i> Dashboard
        </a>
    </div>

    {{-- 1. SUMMARY CARDS --}}
    <div class="row">
        {{-- Total Views --}}
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #4e73df !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Interaksi</div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">{{ number_format($totalViews) }}</div>
                            <small class="text-muted">Kali materi dibuka</small>
                        </div>
                        <div class="col-auto">
                            <div class="icon-circle bg-primary text-white p-3 rounded-circle">
                                <i class="fas fa-eye fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Active Students --}}
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #1cc88a !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Siswa Terlibat</div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">{{ number_format($activeStudents) }}</div>
                            <small class="text-muted">Siswa unik mengakses</small>
                        </div>
                        <div class="col-auto">
                            <div class="icon-circle bg-success text-white p-3 rounded-circle">
                                <i class="fas fa-users fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Material --}}
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #f6c23e !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Materi Terpopuler</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800 text-truncate" style="max-width: 200px;">
                                {{ $topMaterials->first() ? $topMaterials->first()->lmsItem->title ?? '-' : 'Belum ada data' }}
                            </div>
                            <small class="text-muted">
                                {{ $topMaterials->first() ? $topMaterials->first()->total . ' views' : '-' }}
                            </small>
                        </div>
                        <div class="col-auto">
                            <div class="icon-circle bg-warning text-white p-3 rounded-circle">
                                <i class="fas fa-trophy fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. CHART SECTION --}}
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-area mr-2"></i>Tren Aktivitas (7 Hari)</h6>
        </div>
        <div class="card-body">
            <div class="chart-area" style="height: 320px;">
                <canvas id="myAreaChart"></canvas>
            </div>
        </div>
    </div>

    {{-- 3. SPLIT SECTION --}}
    <div class="row">
        
        {{-- KIRI: Live Activity Log --}}
        <div class="col-xl-6 col-lg-12">
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header py-3 bg-white border-bottom">
                    <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-history mr-2 text-muted"></i>Aktivitas Terbaru</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light text-muted">
                                <tr>
                                    <th class="pl-4 border-0">Siswa</th>
                                    <th class="border-0">Materi</th>
                                    <th class="border-0 text-right pr-4">Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentLogs as $log)
                                <tr>
                                    <td class="pl-4">
                                        <div class="font-weight-bold text-dark">{{ $log->user->name ?? 'Deleted' }}</div>
                                        <div class="small text-muted">
                                            @if(str_contains($log->user_agent, 'Mobile') || str_contains($log->user_agent, 'Android'))
                                                <i class="fas fa-mobile-alt mr-1"></i> HP
                                            @else
                                                <i class="fas fa-laptop mr-1"></i> PC
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="text-dark small font-weight-bold">
                                                {{ Str::limit($log->lmsItem->title ?? 'Item dihapus', 30) }}
                                            </span>
                                            <div class="mt-1">
                                                @if($log->lmsItem && $log->lmsItem->type == 'video') 
                                                    <span class="badge-soft badge-video"><i class="fas fa-play mr-1"></i> Video</span>
                                                @elseif($log->lmsItem && $log->lmsItem->type == 'file') 
                                                    <span class="badge-soft badge-pdf"><i class="fas fa-file-pdf mr-1"></i> PDF</span>
                                                @else 
                                                    <span class="badge-soft badge-link">Link</span> 
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right pr-4 text-muted small">
                                        {{ $log->created_at->diffForHumans() }}
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center py-5 text-muted">Belum ada aktivitas tercatat.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- KANAN: Student Profiling (Search) --}}
        <div class="col-xl-6 col-lg-12">
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-search mr-2 text-muted"></i>Cek Profil Siswa</h6>
                </div>
                
                <div class="p-3 bg-white border-bottom">
                    <div class="form-row">
                        <div class="col-md-4 mb-2">
                            <select id="kelasFilter" class="form-control custom-select">
                                <option value="">Filter Kelas</option>
                                @foreach($availableClasses as $kelas)
                                    <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8 mb-2">
                            <div class="input-group">
                                <input type="text" id="studentSearch" class="form-control bg-light border-0" placeholder="Ketik nama siswa..." aria-label="Search">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="button"><i class="fas fa-search"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0" id="studentListContainer" style="min-height: 300px;">
                    <div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DETAIL SISWA (FIXED) --}}
<div class="modal fade" id="studentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-body p-0">
                <div class="row no-gutters">
                    
                    {{-- KOLOM KIRI (PROFIL SISWA) --}}
                    <div class="col-md-4 bg-success text-white text-center py-5 px-3" style="border-top-left-radius: 15px; border-bottom-left-radius: 15px;">
                        
                        {{-- Foto Profil Area --}}
                        <div class="mb-3 d-flex justify-content-center align-items-center" style="height: 110px;">
                            <div id="profileContainer" class="mb-3 d-flex justify-content-center align-items-center" style="height: 110px;">
                                {{-- Default Loading --}}
                                <div class="spinner-border text-light" role="status"></div>
                            </div>                        </div>

                        <h5 class="font-weight-bold mb-1" id="modalStudentName">Nama Siswa</h5>
                        <p class="mb-3 text-white-50" id="modalNisn">NISN: -</p>
                        
                        <div class="text-left mt-4 px-3 small">
                            <div class="mb-2 border-bottom border-white-50 pb-2">
                                <i class="fas fa-chalkboard-teacher mr-2"></i> <span id="modalKelas">-</span>
                            </div>
                            <div class="mb-2 border-bottom border-white-50 pb-2">
                                <i class="fas fa-user-friends mr-2"></i> Wali: <span id="modalWali">-</span>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-venus-mars mr-2"></i> <span id="modalGender">-</span>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 mx-2">
                            <h3 class="font-weight-bold mb-0" id="statTotalAkses">0</h3>
                            <small class="text-white-50">Total Materi Diakses</small>
                        </div>
                    </div>

                    {{-- KOLOM KANAN (RIWAYAT AKSES) --}}
                    <div class="col-md-8 bg-white p-0" style="border-top-right-radius: 15px; border-bottom-right-radius: 15px;">
                        <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light" style="border-top-right-radius: 15px;">
                            <h6 class="m-0 font-weight-bold text-gray-800"><i class="fas fa-history mr-2 text-muted"></i> Riwayat Belajar</h6>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="scroll-area p-3" style="max-height: 500px; overflow-y: auto;">
                            <div id="studentHistoryContent">
                                {{-- Timeline diisi JS --}}
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // --- 1. CHART SETUP ---
    var ctx = document.getElementById("myAreaChart");
    var myLineChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: {!! json_encode($chartLabels) !!},
        datasets: [{
          label: "Akses Materi",
          lineTension: 0.4, // Kurva halus
          backgroundColor: "rgba(78, 115, 223, 0.05)",
          borderColor: "#4e73df",
          pointRadius: 4,
          pointBackgroundColor: "#4e73df",
          pointBorderColor: "#fff",
          pointHoverRadius: 6,
          pointHoverBackgroundColor: "#4e73df",
          data: {!! json_encode($chartValues) !!},
        }],
      },
      options: {
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false } },
            y: { grid: { borderDash: [4], color: "#e3e6f0" }, beginAtZero: true }
        },
        interaction: { intersect: false, mode: 'index' },
      }
    });

    // --- 2. AJAX STUDENT LIST ---
    $(document).ready(function() {
        function fetchStudents(page = 1, query = '', kelasId = '') {
            $.ajax({
                url: "{{ route('admin.analytics.students') }}",
                data: { page: page, search: query, kelas_id: kelasId },
                success: function(data) {
                    $('#studentListContainer').html(data);
                },
                error: function() {
                    $('#studentListContainer').html('<div class="text-danger p-4 text-center">Gagal memuat data.</div>');
                }
            });
        }

        // Init load
        fetchStudents();

        let timeout = null;
        $('#studentSearch').on('keyup', function() {
            clearTimeout(timeout);
            let q = $(this).val();
            let k = $('#kelasFilter').val();
            timeout = setTimeout(function() { fetchStudents(1, q, k); }, 400);
        });

        $('#kelasFilter').on('change', function() {
            fetchStudents(1, $('#studentSearch').val(), $(this).val());
        });

        $(document).on('click', '.custom-pagination .page-link', function(e) {
            e.preventDefault();
            let url = $(this).attr('href');
            if(url) {
                let p = url.split('page=')[1];
                fetchStudents(p, $('#studentSearch').val(), $('#kelasFilter').val());
            }
        });
    });

    // --- 3. MODAL DETAIL SISWA (FIXED LOGIC) ---
    function showStudentDetail(id) {
        // --- STEP 1: RESET TAMPILAN (PENTING) ---
        $('#profileContainer').html('<div class="spinner-border text-white" role="status"></div>');
        $('#studentModal').modal('show');
        $('#modalFotoSiswa').hide().attr('src', '');
        $('#modalAvatarSiswa').show().text('...');
        
        $('#studentModal').modal('show');
        $('#modalStudentName').text('Loading...');
        $('#studentHistoryContent').html('<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>');
        
        // --- STEP 2: AJAX CALL ---
        $.ajax({
            url: '/admin/analytics/student-detail/' + id,
            success: function(res) {
                // ISI PROFIL TEKS
                
                $('#modalStudentName').text(res.siswa.nama_lengkap);
                $('#modalNisn').text('NISN: ' + res.siswa.nisn);
                $('#modalKelas').text(res.stats.kelas);
                $('#modalWali').text(res.stats.wali);
                $('#modalGender').text(res.siswa.jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan');
                $('#statTotalAkses').text(res.stats.total_akses);

                // 2. LOGIC FOTO PROFIL (Sistem Ganti HTML)
                let profileHtml = '';
                
                if (res.siswa.foto) {
                    // KONDISI A: Ada Foto -> Pasang Tag IMG
                    profileHtml = `<img src="/${res.siswa.foto}" class="rounded-circle shadow border border-white" style="width: 100px; height: 100px; object-fit: cover;">`;
                } else {
                    // KONDISI B: Tidak Ada -> Pasang Tag DIV Huruf
                    let inisial = res.siswa.nama_lengkap.charAt(0).toUpperCase();
                    profileHtml = `<div class="rounded-circle shadow border border-white d-flex align-items-center justify-content-center bg-white text-primary font-weight-bold" style="width: 100px; height: 100px; font-size: 2.5rem;">${inisial}</div>`;
                }

                // Masukkan HTML yang dipilih ke dalam wadah
                $('#profileContainer').html(profileHtml);

                // ISI RIWAYAT (TIMELINE)
                let html = '<div class="timeline-wrapper pl-3" style="border-left: 2px solid #e3e6f0; margin-left: 10px;">';
                
                if(res.logs.length === 0) {
                    html = '<div class="text-center py-5 text-muted"><img src="https://img.icons8.com/ios/50/cbd5e0/empty-box.png" class="mb-2"><br>Belum ada aktivitas belajar.</div>';
                } else {
                    res.logs.forEach(function(log) {
                        let badgeClass = 'badge-secondary';
                        let icon = 'fa-link';
                        if(log.item_type == 'video') { badgeClass = 'badge-danger'; icon = 'fa-play'; }
                        if(log.item_type == 'file')  { badgeClass = 'badge-primary'; icon = 'fa-file-pdf'; }

                        html += `
                            <div class="position-relative mb-4">
                                <div class="position-absolute bg-white border border-primary rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 12px; height: 12px; left: -22px; top: 5px;"></div>
                                
                                <div class="card border-0 shadow-sm mb-0 bg-light">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <small class="text-primary font-weight-bold"><i class="far fa-folder-open mr-1"></i> ${log.folder}</small>
                                            <small class="text-muted">${log.time}</small>
                                        </div>
                                        <h6 class="font-weight-bold text-dark mb-2" style="font-size: 0.95rem;">${log.item_title}</h6>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge ${badgeClass} font-weight-normal px-2 py-1"><i class="fas ${icon} mr-1"></i> ${log.item_type}</span>
                                            <small class="text-muted" title="Device"><i class="fas fa-mobile-alt mr-1"></i> ${log.device}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                }
                html += '</div>';
                $('#studentHistoryContent').html(html);
            },
            error: function() {
                $('#modalStudentName').text('Gagal Memuat Data');
            }
        });
    }
    </script>
@endsection