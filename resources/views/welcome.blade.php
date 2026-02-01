<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LMS SD Bawamai Pontianak</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    {{-- BOOTSTRAP 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"> 
    <link rel="icon" type="image/png" href="{{ asset('assets/1.png') }}">

    <style>
        /* --- BASIC RESET & THEME --- */
        a { text-decoration: none; }
        :root {
            --primary-color: #198754;
            --primary-hover: #157347;
            --accent-color: #f6c23e;
        }

        /* --- NAVBAR FIX (MOBILE) --- */
        .main-nav {
            /* Default hidden on mobile handled by media query usually, 
               but here we ensure class 'active' works */
            transition: transform 0.3s ease-in-out;
        }
        
        /* Mobile Menu Styling (Override dashboard.css if needed) */
        @media (max-width: 768px) {
            .main-nav {
                position: fixed;
                top: 70px; /* Tinggi header */
                left: 0;
                width: 100%;
                background: white;
                box-shadow: 0 10px 15px rgba(0,0,0,0.1);
                padding: 20px;
                transform: translateY(-150%); /* Hidden by default */
                z-index: 99;
            }
            .main-nav.active {
                transform: translateY(0); /* Show */
            }
            .main-menu {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
        }

        /* --- CARD STYLING --- */
        .fitur-wrapper {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); 
            gap: 25px;
            margin-bottom: 50px;
        }
        .fitur-card {
            background: white;
            border-radius: 20px;
            padding: 35px 20px; 
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            height: 100%;
            border: 1px solid rgba(0,0,0,0.02);
            position: relative;
            cursor: pointer;
        }
        .fitur-card:hover { transform: translateY(-8px); box-shadow: 0 10px 25px rgba(0,0,0,0.15); }

        .fitur-card-icon-bg {
            width: 90px; height: 90px; border-radius: 12px; 
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 15px; transition: transform 0.3s;
            overflow: hidden; border: 3px solid white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }
        .fitur-card:hover .fitur-card-icon-bg { transform: scale(1.05) rotate(3deg); }
        .fitur-card img { width: 100%; height: 100%; object-fit: cover; }
        .fitur-card svg { width: 45px; height: 45px; object-fit: contain; }
        .fitur-card h4 {
            color: #333; font-size: 1.1rem; font-weight: 800;
            margin: 0; line-height: 1.3; text-transform: uppercase; margin-bottom: 8px;
        }

        /* --- COLORS --- */
        .guru-badge {
            font-size: 0.7rem; color: #6c757d; background-color: #f8f9fa;
            padding: 4px 10px; border-radius: 12px; border: 1px solid #e9ecef;
            display: inline-flex; align-items: center; gap: 4px; margin-top: auto;
        }
        .section-header h2 {
            font-size: 1.3rem; font-weight: 800; color: var(--primary-color);
            margin: 0; border-left: 5px solid var(--accent-color); padding-left: 15px;
        }
        
        .fitur-card:nth-child(6n+1) .fitur-card-icon-bg { background-color: #e4f5e9; } 
        .fitur-card:nth-child(6n+2) .fitur-card-icon-bg { background-color: #fff9c4; } 
        .fitur-card:nth-child(6n+3) .fitur-card-icon-bg { background-color: #e3f2fd; } 
        .fitur-card:nth-child(6n+4) .fitur-card-icon-bg { background-color: #ffe0b2; } 
        .fitur-card:nth-child(6n+5) .fitur-card-icon-bg { background-color: #f3e5f5; } 
        .fitur-card:nth-child(6n+6) .fitur-card-icon-bg { background-color: #ffebee; } 
        
        .card-jadwal .fitur-card-icon-bg { background-color: #ffe4e6 !important; }
        .card-jadwal svg { fill: #dc3545 !important; }

        .login-btn {
            background-color: var(--primary-color); color: white !important;
            padding: 8px 25px !important; border-radius: 25px;
            box-shadow: 0 4px 6px rgba(25, 135, 84, 0.2); transition: all 0.3s ease;
        }
        .login-btn:hover { background-color: var(--primary-hover); transform: translateY(-2px); }
        .user-menu-item { font-weight: 800 !important; color: var(--primary-color) !important; }
        .svg-icon { fill: var(--primary-color); opacity: 0.8; }

        /* AVATAR NAVBAR */
        .nav-avatar {
            width: 35px; height: 35px; border-radius: 50%; object-fit: cover;
            border: 2px solid var(--primary-color);
        }
    </style>
</head>
<body class="dashboard-page">

    {{-- HEADER --}}
    <header class="main-header">
        <div class="container header-flex">
          <div class="logo">
            <img src="{{ asset('assets/1.png') }}" alt="Logo SD Bawamai" class="logo-img">
            <span class="logo-text">SD Bawamai Pontianak</span>
          </div>
      
          {{-- HAMBURGER BUTTON (ID ditambahkan untuk JS) --}}
          <button class="nav-toggle" id="navToggleBtn" aria-label="Buka menu">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
          </button>
      
          <nav class="main-nav" id="mainNav">
            <ul class="main-menu">

                @auth
                    {{-- MENU SETTING & DASHBOARD --}}
                    
                    <li>
                        <a href="#" class="user-menu-item" data-bs-toggle="modal" data-bs-target="#settingsModal">
                            <i class="fas fa-cog me-1"></i> Pengaturan
                        </a>
                    </li>

                    @if(Auth::user()->role === 'siswa')
                        <li><a href="{{ route('landing') }}" class="user-menu-item"></a></li>
                    @else
                        <li><a href="{{ route('dashboard') }}" class="user-menu-item">Dashboard</a></li>
                    @endif

                    <li>
                        {{-- Avatar display jika ada --}}
                        @if(Auth::user()->foto_profil)
                            {{-- Jika ada foto di database Siswa/Guru --}}
                            <img src="{{ asset(Auth::user()->foto_profil) }}" class="nav-avatar" alt="Profil">
                        @else
                            {{-- Jika kosong, tampilkan Placeholder --}}
                            <div class="nav-avatar bg-light d-flex align-items-center justify-content-center border">
                                <i class="fas fa-user text-secondary"></i>
                            </div>
                        @endif    
                    </li>

                    <li>                    
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="color: #d32f2f;">
                            Keluar <i class="fas fa-sign-out-alt"></i>
                        </a>
                    
                    </li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                @else
                    <li><a href="{{ route('login') }}" class="login-btn">Masuk / Login</a></li>
                @endauth
            </ul>
          </nav>
        </div>
    </header>
            
    <section class="hero-section">
        <div class="container hero-content">
            <div class="hero-text">
                <span>SELAMAT DATANG DI</span>
                <h1>LMS SD BAWAMAI PONTIANAK</h1>
            </div>
            <div class="hero-logo-wrapper">
                <img src="{{ asset('assets/1.png') }}" alt="Logo Besar" class="hero-logo">
            </div>
        </div>
    </section>

    <main class="container" id="fitur" style="padding-top: 20px;">
        
        {{-- ALERT MESSAGES --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- LOGIC DATA --}}
        @php
            use App\Models\TahunAjaran;
            $lmsUmum = $menus->whereNull('kelas_id');
            $lmsKelas = collect([]); // Default empty
            
            $isSiswa = Auth::check() && Auth::user()->role == 'siswa';
            $kelasSiswa = null;
            $jadwalPdf = null;

            if ($isSiswa && Auth::user()->siswa) {
                $taAktif = TahunAjaran::where('is_active', 1)->first();
                if ($taAktif) {
                    $kelasSiswa = Auth::user()->siswa->riwayatKelas()
                                    ->wherePivot('tahun_ajaran_id', $taAktif->id)
                                    ->first();
                    if ($kelasSiswa) {
                        $jadwalPdf = $kelasSiswa->file_jadwal;
                        $lmsKelas = $menus->where('kelas_id', $kelasSiswa->id);
                    }
                }
            }
        @endphp

        {{-- SECTION 1: LMS UMUM --}}
        <div class="section-header">
            <h2>Learning Management System (LMS)</h2>
        </div>
        
        <div class="fitur-wrapper">
            <a href="{{ route('akses.raport') }}" class="fitur-card show" id="fitur-raport">
                <div class="fitur-card-icon-bg">
                    <img src="{{ asset('assets/dashboard/e-raport/seo-report.png') }}" 
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='block';" alt="E-Raport">
                    <svg class="svg-icon" style="display:none;" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 14H7v-2h5v2zm5-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                </div>
                <h4>E-RAPORT</h4>
                <span class="guru-badge">Akademik</span>
            </a>

            @forelse($lmsUmum as $menu)
                <a href="{{ route('lms.show', $menu->slug) }}" class="fitur-card show">
                    <div class="fitur-card-icon-bg">
                        @if($menu->cover_image && file_exists(public_path($menu->cover_image)))
                            <img src="{{ asset($menu->cover_image) }}" alt="{{ $menu->title }}">
                        @else
                            <svg class="svg-icon" viewBox="0 0 24 24"><path d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z"/></svg>
                        @endif
                    </div>
                    <h4>{{ $menu->title }}</h4>
                    <span class="guru-badge">Umum</span>
                </a>
            @empty
                @if($lmsKelas->isEmpty())
                    <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #777;">
                        <i class="fas fa-box-open fa-3x" style="margin-bottom: 15px; opacity: 0.5;"></i>
                        <p>Menu LMS sedang disiapkan oleh Admin.</p>
                    </div>
                @endif
            @endforelse
        </div>

                {{-- SECTION 2: KELAS KAMU --}}
        @if($isSiswa)
            <div class="section-header">
                <h2>KELAS KAMU @if($kelasSiswa) ({{ $kelasSiswa->nama_kelas }}) @endif</h2>
            </div>

            <div class="fitur-wrapper">
                {{-- CARD JADWAL --}}
                @if($jadwalPdf)
                    <div class="fitur-card card-jadwal show" onclick="showJadwalModal('{{ asset($jadwalPdf) }}')">
                        <div class="fitur-card-icon-bg">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path fill-rule="evenodd" d="M6.75 2.25A.75.75 0 017.5 3v1.5h9V3A.75.75 0 0118 3v1.5h.75a3 3 0 013 3v11.25a3 3 0 01-3 3H5.25a3 3 0 01-3-3V7.5a3 3 0 013-3H6V3a.75.75 0 01.75-.75zm13.5 9a1.5 1.5 0 00-1.5-1.5H5.25a1.5 1.5 0 00-1.5 1.5v7.5a1.5 1.5 0 001.5 1.5h13.5a1.5 1.5 0 001.5-1.5v-7.5z" clip-rule="evenodd" />
                                <path d="M9 13.5a.75.75 0 01.75-.75h4.5a.75.75 0 010 1.5h-4.5A.75.75 0 019 13.5zm0 3a.75.75 0 01.75-.75h4.5a.75.75 0 010 1.5h-4.5a.75.75 0 01-.75-.75z" />
                            </svg>
                        </div>
                        <h4>JADWAL PELAJARAN</h4>
                        <span class="guru-badge" style="background-color: #ffe4e6; color: #dc3545; border-color: #f8d7da;">
                            <i class="fas fa-eye me-1"></i> Klik untuk Lihat
                        </span>
                    </div>
                @else
                    <div class="fitur-card" style="opacity: 0.6; cursor: default;">
                        <div class="fitur-card-icon-bg" style="background: #eee;">
                            <i class="fas fa-calendar-times" style="font-size: 30px; color: #999;"></i>
                        </div>
                        <h4 style="color: #999;">JADWAL BELUM ADA</h4>
                    </div>
                @endif

                {{-- LMS KHUSUS --}}
                @forelse($lmsKelas as $item)
                    <a href="{{ route('lms.show', $item->slug) }}" class="fitur-card show">
                        <div class="fitur-card-icon-bg">
                            @if($item->cover_image && file_exists(public_path($item->cover_image)))
                                <img src="{{ asset($item->cover_image) }}" alt="{{ $item->title }}">
                            @else
                                <svg class="svg-icon" viewBox="0 0 24 24"><path d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z"/></svg>
                            @endif
                        </div>
                        <h4>{{ $item->title }}</h4>
                        <div class="guru-badge">
                            <i class="fas fa-chalkboard-teacher me-1"></i>
                            {{ $item->guru->nama_lengkap ?? 'Admin' }}
                        </div>
                    </a>
                @empty
                @endforelse
            </div>
        @endif


    </main>

    <footer class="main-footer">
        <div class="container footer-content">
            <div class="footer-social">
                <h3><img src="{{ asset('assets/1.png') }}" alt="Logo" class="footer-logo"> Media Sosial SD Bawamai</h3>
                <div class="social-icons">
                    <a href="#" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.youtube.com/channel/UCSogJssy9q1QItvfHS19ptA/videos" target="_blank"><i class="fab fa-youtube"></i></a>
                    <a href="#" target="_blank"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="footer-copyright">
                <p>Copyright @ 2025 SD Bawamai Pontianak All Right Reserved</p>
            </div>
        </div>
    </footer>

    {{-- MODAL PREVIEW JADWAL --}}
    <div class="modal fade" id="jadwalModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px; border: none; overflow: hidden;">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-calendar-alt me-2"></i> Jadwal Pelajaran</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0" style="height: 80vh;">
                    <iframe id="pdfFrame" src="" width="100%" height="100%" style="border: none;"></iframe>
                </div>
                <div class="modal-footer bg-light justify-content-between">
                    <small class="text-muted">Jika preview tidak muncul, download file.</small>
                    <div>
                        <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Tutup</button>
                        <a href="#" id="downloadBtn" class="btn btn-primary rounded-pill" download><i class="fas fa-download me-1"></i> Download PDF</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL PENGATURAN (SETTINGS) --}}
    <div class="modal fade" id="settingsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-cog me-2"></i> Pengaturan Akun</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs nav-justified mb-3" id="settingTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="foto-tab" data-bs-toggle="tab" href="#foto" role="tab">Foto Profil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pass-tab" data-bs-toggle="tab" href="#pass" role="tab">Ganti Password</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="settingTabContent">
                        {{-- TAB FOTO --}}
                        <div class="tab-pane fade show active" id="foto" role="tabpanel">
                            <form action="{{ route('profile.updateFoto') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="text-center mb-3">
                                    {{-- Cek apakah user login DAN punya foto profil --}}
                                    @if(Auth::user() && Auth::user()->foto_profil)
                                        <img src="{{ asset(Auth::user()->foto_profil) }}" id="previewImg" class="rounded-circle mb-2 border" style="width: 100px; height: 100px; object-fit: cover;">
                                    @else
                                        {{-- Tampilkan Placeholder jika user belum login ATAU tidak punya foto --}}
                                        <div id="previewPlaceholder" class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto border mb-2" style="width: 100px; height: 100px;">
                                            <i class="fas fa-user fa-3x text-secondary"></i>
                                        </div>
                                        {{-- Image hidden untuk preview JS --}}
                                        <img src="#" id="previewImg" class="rounded-circle mb-2 border d-none" style="width: 100px; height: 100px; object-fit: cover;">
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Upload Foto Baru (Max 2MB)</label>
                                    <input type="file" name="foto" class="form-control" accept="image/*" onchange="previewFile(this)">
                                </div>
                                <button type="submit" class="btn btn-success w-100 rounded-pill">Simpan Foto</button>
                            </form>
                        </div>
                        {{-- TAB PASSWORD --}}
                        <div class="tab-pane fade" id="pass" role="tabpanel">
                            <form action="{{ route('profile.updatePassword') }}" method="POST">
                                @csrf
                                <div class="mb-2">
                                    <label class="small fw-bold">Password Lama</label>
                                    <input type="password" name="current_password" class="form-control" required>
                                </div>
                                <div class="mb-2">
                                    <label class="small fw-bold">Password Baru</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="small fw-bold">Konfirmasi Password Baru</label>
                                    <input type="password" name="password_confirmation" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-warning w-100 rounded-pill">Ganti Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Modal Jadwal
        function showJadwalModal(url) {
            document.getElementById('pdfFrame').src = url;
            document.getElementById('downloadBtn').href = url;
            var myModal = new bootstrap.Modal(document.getElementById('jadwalModal'));
            myModal.show();
        }

        // Preview Image Upload
        function previewFile(input) {
            var file = input.files[0];
            if(file){
                var reader = new FileReader();
                reader.onload = function(){
                    var previewImg = document.getElementById('previewImg');
                    var placeholder = document.getElementById('previewPlaceholder');
                    previewImg.src = reader.result;
                    previewImg.classList.remove('d-none');
                    if(placeholder) placeholder.classList.add('d-none');
                }
                reader.readAsDataURL(file);
            }
        }

        // Fix Hamburger Menu
        document.getElementById('navToggleBtn').addEventListener('click', function() {
            document.getElementById('mainNav').classList.toggle('active');
        });
    </script>
</body>
</html>