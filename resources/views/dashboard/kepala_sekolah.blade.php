<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kepala Sekolah - SD Bawamai</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* --- RESET & VARIABLES (GREEN THEME) --- */
        :root {
            --primary-bg: #e8f5e9; /* Hijau Mint Lembut */
            --card-bg: #ffffff;
            --text-main: #1b5e20;  /* Hijau Tua */
            --text-body: #374151;  /* Abu Gelap */
            --green-accent: #2e7d32;
            --green-dark: #1b5e20;
            --shadow-card: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
            --shadow-hover: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        body {
            font-family: 'Nunito', sans-serif;
            background-color: var(--primary-bg);
            color: var(--text-body);
            min-height: 100vh;
            display: flex; flex-direction: column;
        }

        a { text-decoration: none; }

        /* --- HEADER & NAVBAR --- */
        .main-header {
            background-color: #ffffff;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            padding: 1rem 0;
            position: sticky; top: 0; z-index: 50;
            border-bottom: 4px solid var(--green-accent);
        }

        .container { max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; }

        .header-content { display: flex; justify-content: space-between; align-items: center; }

        .logo {
            display: flex; align-items: center; gap: 12px;
            font-weight: 800; font-size: 1.35rem; color: var(--green-dark);
            letter-spacing: -0.5px;
        }
        .logo img { height: 48px; width: auto; }

        /* NAV MENU */
        .nav-menu { display: flex; align-items: center; gap: 20px; }

        .nav-link-custom {
            color: #555; font-weight: 700; font-size: 0.95rem;
            display: flex; align-items: center; gap: 8px;
            padding: 8px 16px; border-radius: 20px; transition: all 0.2s;
        }
        .nav-link-custom:hover { background-color: #c8e6c9; color: var(--green-dark); }

        .btn-logout { color: #ef4444 !important; border: 1px solid transparent; }
        .btn-logout:hover { background-color: #fee2e2; color: #dc2626 !important; border-color: #ef4444; }

        /* AVATAR */
        .nav-avatar {
            width: 35px; height: 35px; border-radius: 50%; object-fit: cover;
            border: 2px solid #a5d6a7;
        }

        /* HAMBURGER (Mobile) */
        .nav-toggle {
            display: none; background: none; border: none; cursor: pointer;
            flex-direction: column; gap: 5px;
        }
        .hamburger-line { width: 25px; height: 3px; background-color: var(--green-dark); border-radius: 5px; }

        /* MOBILE RESPONSIVE */
        @media (max-width: 768px) {
            .nav-toggle { display: flex; }
            .nav-menu {
                position: fixed; top: 80px; left: 0; width: 100%;
                background: white; flex-direction: column;
                padding: 20px; gap: 15px;
                box-shadow: 0 10px 15px rgba(0,0,0,0.1);
                transform: translateY(-150%); transition: transform 0.3s ease;
                align-items: flex-start;
            }
            .nav-menu.active { transform: translateY(0); }
        }

        /* --- CONTENT --- */
        main { flex: 1; padding: 3.5rem 0; }
        .page-header { text-align: center; margin-bottom: 3.5rem; }
        .page-title { 
            font-size: 2.25rem; font-weight: 900; 
            color: var(--green-dark); margin-bottom: 0.5rem; letter-spacing: -0.5px;
        }
        .page-subtitle { color: #555; font-size: 1.15rem; font-weight: 500; }

        /* --- GRID --- */
        .grid-wrapper {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem; justify-content: center;
        }

        /* --- CARDS (High Contrast) --- */
        .menu-card {
            background-color: var(--card-bg);
            border-radius: 1.25rem; padding: 2.5rem 2rem;
            text-align: center; text-decoration: none;
            box-shadow: var(--shadow-card);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            display: flex; flex-direction: column; align-items: center;
            border-top: 5px solid transparent; /* Border Top Warna Warni */
        }
        .menu-card:hover { transform: translateY(-8px); box-shadow: var(--shadow-hover); }

        .icon-wrapper {
            width: 80px; height: 80px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1.5rem; transition: transform 0.3s ease;
            color: white; font-size: 2rem;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        }
        .menu-card:hover .icon-wrapper { transform: scale(1.15) rotate(5deg); }

        .menu-card h3 { font-size: 1.25rem; font-weight: 800; color: var(--text-main); margin: 0; }
        .menu-card p { font-size: 0.95rem; color: #666; margin-top: 5px; line-height: 1.5; }

        /* COLORS FOR CARDS */
        .card-analytics { border-top-color: #4caf50; }
        .card-analytics .icon-wrapper { background: linear-gradient(135deg, #1b5e20, #4caf50); }
        
        .card-teacher { border-top-color: #ff9800; }
        .card-teacher .icon-wrapper { background: linear-gradient(135deg, #e65100, #ff9800); }
        
        .card-smart { border-top-color: #2196f3; }
        .card-smart .icon-wrapper { background: linear-gradient(135deg, #0d47a1, #2196f3); }

        /* FOOTER */
        .main-footer {
            background: white; padding: 2rem 0; text-align: center;
            border-top: 1px solid #e5e7eb; margin-top: auto;
        }
        .footer-copyright { color: #888; font-size: 0.9rem; font-weight: 600; }
    </style>
</head>
<body>

    <header class="main-header">
        <div class="container header-content">
            <a href="#" class="logo">
                <img src="{{ asset('assets/1.png') }}" alt="Logo SD Bawamai"> 
                <span>Portal Yayasan</span>
            </a>

            {{-- HAMBURGER BTN --}}
            <button class="nav-toggle" id="navToggleBtn">
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
            </button>

            <nav class="nav-menu" id="navMenu">
                {{-- Nama User & Avatar --}}
                <a href="#" class="nav-link-custom" data-bs-toggle="modal" data-bs-target="#settingsModal">
                    @if(Auth::user()->foto_profil)
                        <img src="{{ asset(Auth::user()->foto_profil) }}" class="nav-avatar" alt="Profil">
                    @else
                        <div class="nav-avatar bg-light d-flex align-items-center justify-content-center border">
                            <i class="fas fa-user-tie text-secondary" style="font-size: 1rem;"></i>
                        </div>
                    @endif
                    <span>{{ Auth::user()->name }}</span>
                </a>

                {{-- Logout --}}
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-link-custom btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Keluar
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </nav>
        </div>
    </header> 

    <main>
        <div class="container">
            {{-- ALERT MESSAGES --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
                    <ul class="mb-0 ps-3">@foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="page-header">
                <h1 class="page-title">Dashboard Monitoring</h1>
                <p class="page-subtitle">Pantau perkembangan siswa dan kinerja guru secara real-time.</p>
            </div>
            
            <div class="grid-wrapper">
                
                {{-- 1. ANALYTICS SISWA --}}
                <a href="{{ route('admin.analytics') }}" class="menu-card card-analytics">
                    <div class="icon-wrapper">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Analytics Siswa</h3>
                    <p>Statistik akses materi & aktivitas siswa</p>
                </a>

                {{-- 2. KINERJA GURU --}}
                <a href="{{ route('admin.analytics.teachers') }}" class="menu-card card-teacher">
                    <div class="icon-wrapper">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h3>Kinerja Guru</h3>
                    <p>Monitor keaktifan & upload materi guru</p>
                </a>

                {{-- 3. SMART LEARNING (LANDING) --}}
                <a href="{{ route('landing') }}" class="menu-card card-smart">
                    <div class="icon-wrapper">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                    <h3>Smart Learning</h3>
                    <p>Akses fitur pembelajaran (Smart Quran, dll)</p>
                </a>

            </div>
        </div>
    </main>

    <footer class="main-footer">
        <div class="container">
            <div class="footer-copyright">
                <p>&copy; {{ date('Y') }} SD Bawamai Pontianak - Portal Yayasan</p>
            </div>
        </div>
    </footer>

    {{-- MODAL PENGATURAN (SETTINGS) --}}
    <div class="modal fade" id="settingsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header text-white" style="background-color: var(--green-dark);">
                    <h5 class="modal-title"><i class="fas fa-user-cog me-2"></i> Pengaturan Akun</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs nav-justified mb-3" id="settingTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active text-success" id="foto-tab" data-bs-toggle="tab" href="#foto" role="tab">Foto Profil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-secondary" id="pass-tab" data-bs-toggle="tab" href="#pass" role="tab">Ganti Password</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        {{-- TAB FOTO --}}
                        <div class="tab-pane fade show active" id="foto" role="tabpanel">
                            <form action="{{ route('profile.updateFoto') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="text-center mb-3">
                                    @if(Auth::user()->foto_profil)
                                        <img src="{{ asset(Auth::user()->foto_profil) }}" id="previewImg" class="rounded-circle mb-2 border shadow-sm" style="width: 100px; height: 100px; object-fit: cover;">
                                    @else
                                        {{-- Placeholder --}}
                                        <div id="previewPlaceholder" class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto border mb-2" style="width: 100px; height: 100px;">
                                            <i class="fas fa-user-tie fa-3x text-secondary"></i>
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
                                <button type="submit" class="btn btn-warning w-100 rounded-pill text-white">Ganti Password</button>
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
        // Hamburger Toggle
        document.getElementById('navToggleBtn').addEventListener('click', function() {
            document.getElementById('navMenu').classList.toggle('active');
        });

        // Preview Image
        function previewFile(input) {
            var file = input.files[0];
            if(file){
                var reader = new FileReader();
                reader.onload = function(){
                    var previewImg = document.getElementById('previewImg');
                    var placeholder = document.getElementById('previewPlaceholder');
                    if(previewImg) {
                        previewImg.src = reader.result;
                        previewImg.classList.remove('d-none');
                    }
                    if(placeholder) placeholder.classList.add('d-none');
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>