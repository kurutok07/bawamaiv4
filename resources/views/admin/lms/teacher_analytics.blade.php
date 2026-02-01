@extends('layouts.admin')
@section('title', 'Kinerja Guru')

@section('content')
<style>
    .activity-timeline { position: relative; padding-left: 30px; border-left: 2px solid #e3e6f0; }
    .activity-item { position: relative; margin-bottom: 25px; }
    .activity-dot { 
        position: absolute; left: -36px; top: 0; width: 14px; height: 14px; 
        border-radius: 50%; border: 2px solid white; box-shadow: 0 0 0 2px #eaecf4; 
    }
    .bg-upload { background-color: #4e73df; }
    .bg-login { background-color: #1cc88a; }
    
    /* Card List Style */
    .guru-list-item { transition: all 0.2s; border-left: 4px solid transparent; cursor: pointer; }
    .guru-list-item:hover { background-color: #f8f9fc; border-left: 4px solid #4e73df; transform: translateX(5px); }
    
    /* Avatar Kecil (di List) */
    .avatar-circle {
        width: 40px; height: 40px; background: #e3e6f0; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: bold; color: #5a5c69; font-size: 1.1rem; overflow: hidden;
    }
    .avatar-circle img { width: 100%; height: 100%; object-fit: cover; }

    /* Avatar Besar (di Modal - Class CSS untuk Inisial) */
    .avatar-circle-lg {
        width: 100px; height: 100px; background: #4e73df; color: white; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 2.5rem; margin: 0 auto; 
        box-shadow: 0 5px 15px rgba(78, 115, 223, 0.3); border: 3px solid rgba(255,255,255,0.3);
    }
    
    /* Modal Styles */
    .nav-tabs .nav-link { border: none; color: #6e707e; font-weight: 600; }
    .nav-tabs .nav-link.active { color: #4edf58; border-bottom: 2px solid #4e73df; background: none; }
    .scroll-area { max-height: 400px; overflow-y: auto; padding-right: 5px; }
</style>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Monitoring Kinerja Guru</h1>
            <p class="mb-0 text-muted">Pantau keaktifan login dan kontribusi materi pembelajaran.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-secondary shadow-sm rounded-pill px-3">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-2"></i> Dashboard
        </a>
    </div>

    <div class="row">
        
        {{-- KOLOM KIRI --}}
        <div class="col-xl-8 col-lg-7">
            {{-- 1. Grafik Upload --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-bar mr-2"></i>Top 5 Guru Paling Aktif Upload</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar" style="height: 300px;">
                        <canvas id="guruUploadChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- 2. Recent Activity Feed --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-stream mr-2"></i>Aktivitas Terkini (Live Feed)</h6>
                </div>
                <div class="card-body">
                    <div class="activity-timeline mt-2">
                        @forelse($recentActivities as $activity)
                            <div class="activity-item">
                                @if($activity->type == 'upload')
                                    <div class="activity-dot bg-upload"></div>
                                    <p class="mb-1 text-gray-800">
                                        <strong>{{ $activity->nama_lengkap }}</strong> mengupload materi baru:
                                        <span class="text-primary font-italic">"{{ Str::limit($activity->item_title, 40) }}"</span>
                                    </p>
                                @else
                                    <div class="activity-dot bg-login"></div>
                                    <p class="mb-1 text-gray-800">
                                        <strong>{{ $activity->nama_lengkap }}</strong> masuk ke dalam sistem (Login).
                                    </p>
                                @endif
                                <small class="text-muted"><i class="far fa-clock mr-1"></i> {{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}</small>
                            </div>
                        @empty
                            <div class="text-center text-muted py-3">Belum ada aktivitas guru.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN (Sidebar: List Guru) --}}
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4" style="height: 100%;">
                <div class="card-header py-3 bg-white d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chalkboard-teacher mr-2"></i>Daftar Guru</h6>
                    <span class="badge badge-primary rounded-pill">{{ $gurus->count() }} Guru</span>
                </div>
                
                <div class="p-3 bg-light border-bottom">
                    <input type="text" id="searchGuru" class="form-control form-control-sm border-0" placeholder="Cari nama guru...">
                </div>

                <div class="card-body p-0" style="max-height: 700px; overflow-y: auto;">
                    <div class="list-group list-group-flush" id="guruList">
                        @foreach($gurus as $guru)
                        <div class="list-group-item guru-list-item p-3" 
                             data-name="{{ strtolower($guru->nama_lengkap) }}"
                             onclick="showTeacherDetail({{ $guru->id }})">
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle mr-3">
                                    {{-- Foto Kecil di List --}}
                                    @if($guru->foto)
                                        <img src="{{ asset($guru->foto) }}">
                                    @else
                                        {{ substr($guru->nama_lengkap, 0, 1) }}
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="font-weight-bold text-dark mb-0">{{ Str::limit($guru->nama_lengkap, 20) }}</h6>
                                    <small class="text-muted">{{ $guru->nip ?? 'NIP: -' }}</small>
                                </div>
                                <div class="text-right">
                                    <div class="badge {{ $guru->total_materi > 0 ? 'badge-success' : 'badge-secondary' }} badge-pill mb-1">
                                        {{ $guru->total_materi }} Materi
                                    </div>
                                    <div class="d-block small text-muted" style="font-size: 10px;">
                                        {{ $guru->total_login }}x Login
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DETAIL GURU --}}
<div class="modal fade" id="guruDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-body p-0">
                <div class="row no-gutters">
                    {{-- KOLOM KIRI (BIODATA) --}}
                    <div class="col-md-4 bg-success text-white text-center py-5 px-3" style="border-top-left-radius: 15px; border-bottom-left-radius: 15px;">
                        
                        {{-- WADAH FOTO PROFIL --}}
                        <div id="teacherProfileContainer" class="mb-3 d-flex justify-content-center align-items-center" style="height: 100px;">
                            {{-- Default Loading Spinner --}}
                            <div class="spinner-border text-white" role="status"></div>
                        </div>

                        <h5 class="font-weight-bold mb-1" id="modalName">Nama Guru</h5>
                        <p class="mb-3 small text-white-50" id="modalNip">NIP: 123456</p>
                        
                        <div class="text-left mt-4 px-3">
                            <div class="mb-2 small"><i class="fas fa-envelope mr-2"></i> <span id="modalEmail">-</span></div>
                            <div class="mb-2 small"><i class="fas fa-phone mr-2"></i> <span id="modalPhone">-</span></div>
                        </div>

                        <div class="row mt-4 pt-3 border-top border-white-50 mx-2">
                            <div class="col-6 border-right border-white-50">
                                <h4 class="font-weight-bold mb-0" id="statUpload">0</h4>
                                <small class="text-white-50">Upload</small>
                            </div>
                            <div class="col-6">
                                <h4 class="font-weight-bold mb-0" id="statLogin">0</h4>
                                <small class="text-white-50">Logins</small>
                            </div>
                        </div>
                    </div>

                    {{-- KOLOM KANAN (TABS RIWAYAT) --}}
                    <div class="col-md-8 bg-white p-4" style="border-top-right-radius: 15px; border-bottom-right-radius: 15px;">
                        <div class="d-flex justify-content-end">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>

                        </div>
                        
                        <h6 class="font-weight-bold text-gray-800 mb-3">Detail Aktivitas</h6>
                        
                        <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="upload-tab" data-toggle="tab" href="#upload" role="tab"><i class="fas fa-cloud-upload-alt mr-1"></i> Riwayat Upload</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="login-tab" data-toggle="tab" href="#login" role="tab"><i class="fas fa-sign-in-alt mr-1"></i> Riwayat Login</a>
                            </li>
                        </ul>

                        <div class="tab-content" id="myTabContent">
                            {{-- TAB UPLOADS --}}
                            <div class="tab-pane fade show active" id="upload" role="tabpanel">
                                <div class="scroll-area" id="uploadList">
                                    {{-- List Upload diisi JS --}}
                                </div>
                            </div>
                            
                            {{-- TAB LOGINS --}}
                            <div class="tab-pane fade" id="login" role="tabpanel">
                                <div class="scroll-area">
                                    <table class="table table-sm table-borderless table-hover">
                                        <thead class="text-muted small bg-light">
                                            <tr><th>Waktu</th><th>Device</th><th>IP Address</th></tr>
                                        </thead>
                                        <tbody id="loginList">
                                            {{-- List Login diisi JS --}}
                                        </tbody>
                                    </table>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 1. Script Search Guru
    document.getElementById('searchGuru').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let items = document.querySelectorAll('.guru-list-item');
        items.forEach(function(item) {
            let name = item.getAttribute('data-name');
            item.style.display = name.includes(filter) ? "" : "none";
        });
    });

    // 2. Chart Setup
    var ctx = document.getElementById("guruUploadChart");
    var myBarChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: {!! $topGuruNames !!},
        datasets: [{
          label: "Jumlah Materi",
          backgroundColor: "#4e73df",
          hoverBackgroundColor: "#2e59d9",
          borderColor: "#4e73df",
          data: {!! $topGuruCounts !!},
          barPercentage: 0.6,
        }],
      },
      options: {
        maintainAspectRatio: false,
        layout: { padding: { left: 10, right: 25, top: 25, bottom: 0 } },
        scales: {
          x: { grid: { display: false, drawBorder: false } },
          y: { ticks: { beginAtZero: true, maxTicksLimit: 5 }, grid: { borderDash: [2] } },
        },
        plugins: { legend: { display: false } }
      }
    });

    // --- 3. LOGIC MODAL DETAIL GURU (FIXED FOTO) ---
    function showTeacherDetail(id) {
        // RESET TAMPILAN
        $('#guruDetailModal').modal('show');
        $('#modalName').text('Loading...');
        
        // Reset Wadah Foto ke Loading Spinner
        $('#teacherProfileContainer').html('<div class="spinner-border text-white" role="status"></div>');
        
        $('#uploadList').html('<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>');
        $('#loginList').html('');

        // AJAX Fetch
        $.ajax({
            url: '/admin/analytics/teacher-detail/' + id,
            success: function(res) {
                // Populate Biodata Teks
                $('#modalName').text(res.guru.nama_lengkap);
                $('#modalNip').text(res.guru.nip ? 'NIP: ' + res.guru.nip : 'NIP: -');
                $('#modalEmail').text(res.email);
                $('#modalPhone').text(res.guru.no_telepon ? res.guru.no_telepon : '-');
                $('#statUpload').text(res.stats.total_upload);
                $('#statLogin').text(res.stats.total_login);

                // --- LOGIC FOTO PROFIL (INJECT HTML) ---
                let profileHtml = '';
                if (res.guru.foto) {
                    // Ada Foto -> Pakai tag IMG
                    profileHtml = `<img src="/${res.guru.foto}" class="rounded-circle shadow border border-white" style="width: 100px; height: 100px; object-fit: cover;">`;
                } else {
                    // Tidak Ada Foto -> Pakai tag DIV Avatar Inisial
                    let initial = res.guru.nama_lengkap.charAt(0).toUpperCase();
                    profileHtml = `<div class="avatar-circle-lg mb-3">${initial}</div>`;
                }
                $('#teacherProfileContainer').html(profileHtml);

                // Populate Upload List
                let uploadHtml = '';
                if(res.uploads.length === 0) {
                    uploadHtml = '<div class="text-center text-muted py-4"><i class="fas fa-folder-open mb-2"></i><br>Belum ada materi diupload.</div>';
                } else {
                    res.uploads.forEach(function(item) {
                        let icon = 'fa-link';
                        if(item.type == 'file') icon = 'fa-file-pdf';
                        if(item.type == 'video') icon = 'fa-play-circle';
                        
                        uploadHtml += `
                            <div class="d-flex align-items-center p-2 border-bottom">
                                <div class="mr-3 text-primary"><i class="fas ${icon} fa-lg"></i></div>
                                <div class="flex-grow-1">
                                    <div class="font-weight-bold text-dark">${item.title}</div>
                                    <small class="text-muted">Untuk: ${item.audiens}</small>
                                </div>
                                <small class="text-muted">${item.created_at}</small>
                            </div>
                        `;
                    });
                }
                $('#uploadList').html(uploadHtml);

                // Populate Login List
                let loginHtml = '';
                if(res.logins.length === 0) {
                    loginHtml = '<tr><td colspan="3" class="text-center py-3">Belum ada riwayat login.</td></tr>';
                } else {
                    res.logins.forEach(function(log) {
                        loginHtml += `
                            <tr>
                                <td><span class="font-weight-bold">${log.time}</span> <br> <small class="text-muted">${log.ago}</small></td>
                                <td>${log.device}</td>
                                <td class="text-muted small">${log.ip}</td>
                            </tr>
                        `;
                    });
                }
                $('#loginList').html(loginHtml);
            },
            error: function() {
                $('#modalName').text('Error Loading Data');
                $('#teacherProfileContainer').html('<div class="text-white">Error</div>');
            }
        });
    }
</script>
@endsection