@extends('layouts.admin')

@section('content')
<style>
    .card-title-custom { font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; font-size: 0.85rem; }
    .table-modern th { font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; color: #6b7280; }
    .table-modern td { vertical-align: middle; font-size: 0.9rem; }
    .badge-video { background-color: #fee2e2; color: #b91c1c; padding: 5px 10px; border-radius: 6px; font-weight: 700; font-size: 0.75rem; }
    .badge-pdf { background-color: #dbeafe; color: #1e40af; padding: 5px 10px; border-radius: 6px; font-weight: 700; font-size: 0.75rem; }
    .badge-link { background-color: #f3f4f6; color: #374151; padding: 5px 10px; border-radius: 6px; font-weight: 700; font-size: 0.75rem; }
    .timeline-item { padding: 15px; border-left: 3px solid #e5e7eb; position: relative; margin-left: 10px; }
    .timeline-item::before { content: ''; position: absolute; left: -9px; top: 20px; width: 15px; height: 15px; border-radius: 50%; background: #3b82f6; border: 3px solid white; }
</style>

<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Analytics Dashboard</h1>
            <p class="mb-0 text-muted">Pantau aktivitas belajar siswa secara realtime.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-secondary shadow-sm rounded-pill px-3">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-2"></i> Kembali ke LMS
        </a>
    </div>

    {{-- 1. ROW KARTU STATISTIK --}}
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-0 shadow h-100 py-2" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); color: white;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1" style="opacity: 0.8;">Total Akses</div>
                            <div class="h3 mb-0 font-weight-bold">{{ number_format($totalViews) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-mouse-pointer fa-2x text-white-50"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-0 shadow h-100 py-2" style="background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%); color: white;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1" style="opacity: 0.8;">Siswa Aktif</div>
                            <div class="h3 mb-0 font-weight-bold">{{ number_format($activeStudents) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-user-graduate fa-2x text-white-50"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-0 shadow h-100 py-2" style="background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%); color: white;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1" style="opacity: 0.8;">Materi Favorit</div>
                            <div class="h5 mb-0 font-weight-bold">
                                {{ $topMaterials->first() ? Str::limit($topMaterials->first()->lmsItem->title ?? '-', 15) : '-' }}
                            </div>
                        </div>
                        <div class="col-auto"><i class="fas fa-star fa-2x text-white-50"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. ROW CHART --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary">Tren Aktivitas Siswa (7 Hari Terakhir)</h6>
        </div>
        <div class="card-body">
            <div class="chart-area" style="height: 300px;">
                <canvas id="myAreaChart"></canvas>
            </div>
        </div>
    </div>

    {{-- 3. ROW SPLIT --}}
    <div class="row">
        {{-- KIRI: Riwayat Terbaru --}}
        <div class="col-xl-6 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white d-flex align-items-center">
                    <i class="fas fa-clock text-primary mr-2"></i>
                    <h6 class="m-0 font-weight-bold text-primary">10 Riwayat Akses Terbaru (Siswa)</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-modern table-striped mb-0">
                            <thead>
                                <tr><th class="pl-4">Siswa</th><th>Materi</th><th>Waktu</th></tr>
                            </thead>
                            <tbody>
                                @forelse($recentLogs as $log)
                                <tr>
                                    <td class="pl-4 font-weight-bold text-dark">{{ $log->user->name ?? 'User Terhapus' }}</td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="text-dark font-weight-bold" style="font-size: 0.85rem;">
                                                {{ Str::limit($log->lmsItem->title ?? 'Item Terhapus', 25) }}
                                            </span>
                                            <div class="mt-1">
                                                @if($log->lmsItem && $log->lmsItem->type == 'video') <span class="badge-video"><i class="fas fa-play mr-1"></i> Video</span>
                                                @elseif($log->lmsItem && $log->lmsItem->type == 'file') <span class="badge-pdf"><i class="fas fa-file-pdf mr-1"></i> PDF</span>
                                                @else <span class="badge-link">Link</span> @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted small">{{ $log->created_at->diffForHumans() }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center py-4 text-muted">Belum ada aktivitas siswa.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- KANAN: Student Profiling --}}
        <div class="col-xl-6 col-lg-12">
            <div class="card shadow mb-4 border-left-info">
                <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
                    <div><i class="fas fa-id-card text-info mr-2"></i><h6 class="m-0 font-weight-bold text-info">Student Profiling</h6></div>
                </div>
                <div class="p-3 bg-light border-bottom">
                    <div class="input-group">
                        <input type="text" id="studentSearch" class="form-control border-0 small" placeholder="Cari nama siswa..." aria-label="Search">
                        <div class="input-group-append"><button class="btn btn-info" type="button"><i class="fas fa-search fa-sm"></i></button></div>
                    </div>
                </div>
                <div class="card-body p-0" id="studentListContainer">
                    <div class="text-center py-5"><div class="spinner-border text-info" role="status"><span class="sr-only">Loading...</span></div></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DETAIL SISWA --}}
<div class="modal fade" id="studentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title font-weight-bold" id="modalStudentName">Detail Aktivitas</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body bg-light">
                <h6 class="font-weight-bold text-gray-800 mb-3">Riwayat Materi yang Diakses:</h6>
                <div id="studentHistoryContent" style="max-height: 400px; overflow-y: auto;"></div>
            </div>
            <div class="modal-footer bg-white">
                <button type="button" class="btn btn-secondary rounded-pill" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- PENTING: PAKE SECTION SCRIPTS --}}
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // 1. Chart
    var ctx = document.getElementById("myAreaChart");
    var myLineChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: {!! json_encode($chartLabels) !!},
        datasets: [{
          label: "Jumlah Akses",
          lineTension: 0.4,
          backgroundColor: "rgba(78, 115, 223, 0.05)",
          borderColor: "rgba(78, 115, 223, 1)",
          pointRadius: 4,
          pointBackgroundColor: "#fff",
          pointBorderColor: "rgba(78, 115, 223, 1)",
          pointHoverRadius: 5,
          data: {!! json_encode($chartValues) !!},
        }],
      },
      options: {
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { x: { grid: { display: false } }, y: { grid: { borderDash: [2] }, beginAtZero: true } }
      }
    });

    // 2. AJAX Profiling
    $(document).ready(function() {
        console.log("Analytics Script Loaded!");

        function fetchStudents(page = 1, query = '') {
            $.ajax({
                url: "{{ route('admin.analytics.students') }}",
                data: { page: page, search: query },
                success: function(data) {
                    $('#studentListContainer').html(data);
                },
                error: function(xhr) {
                    console.log(xhr);
                    $('#studentListContainer').html('<div class="text-danger p-4 text-center">Gagal memuat data.</div>');
                }
            });
        }

        fetchStudents(); // Load awal

        let timeout = null;
        $('#studentSearch').on('keyup', function() {
            clearTimeout(timeout);
            let query = $(this).val();
            timeout = setTimeout(function() { fetchStudents(1, query); }, 500);
        });

        $(document).on('click', '.custom-pagination .page-link', function(e) {
            e.preventDefault();
            let url = $(this).attr('href');
            if(url) {
                let page = url.split('page=')[1];
                let query = $('#studentSearch').val();
                fetchStudents(page, query);
            }
        });
    });

    // 3. Modal Detail
    function showStudentDetail(id) {
        $('#studentHistoryContent').html('<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>');
        $('#modalStudentName').text('Memuat Data...');
        $('#studentModal').modal('show');

        $.ajax({
            url: '/admin/analytics/student-history/' + id,
            success: function(response) {
                $('#modalStudentName').text('Aktivitas: ' + response.student_name);
                let html = '';
                if(response.history.length === 0) {
                    html = '<div class="alert alert-warning text-center">Belum ada riwayat akses materi.</div>';
                } else {
                    response.history.forEach(function(log) {
                        let date = new Date(log.created_at).toLocaleString('id-ID', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
                        let itemTitle = log.lms_item ? log.lms_item.title : 'Item Terhapus';
                        let badge = '';
                        if(log.lms_item && log.lms_item.type === 'video') badge = '<span class="badge badge-danger">Video</span>';
                        else if(log.lms_item && log.lms_item.type === 'file') badge = '<span class="badge badge-primary">PDF</span>';
                        else badge = '<span class="badge badge-secondary">Link</span>';

                        html += `<div class="timeline-item bg-white shadow-sm rounded mb-3">
                                    <div class="d-flex justify-content-between">
                                        <span class="font-weight-bold text-dark">${itemTitle}</span>
                                        <span class="small text-muted">${date}</span>
                                    </div>
                                    <div class="mt-1">${badge}</div>
                                </div>`;
                    });
                }
                $('#studentHistoryContent').html(html);
            }
        });
    }
</script>
@endsection