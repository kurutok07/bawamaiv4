<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guru Dashboard - LMS Bawamai</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    {{-- TAMBAHAN: Bootstrap 5 (Wajib untuk Modal & Navbar) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* --- RESET CONFLICT --- */
        a { text-decoration: none; }

        /* --- VARIABLES (GREEN THEME) --- */
        :root {
            --primary-bg: #e8f5e9; /* Hijau Mint Lembut */
            --card-bg: #ffffff;
            --text-main: #1b5e20;  /* Hijau Tua */
            --text-body: #374151;  /* Abu Gelap */
            --green-accent: #2e7d32;
            --green-dark: #1b5e20;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
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

        /* --- HEADER & NAVBAR --- */
        .main-header {
            background-color: #ffffff;
            box-shadow: var(--shadow-sm);
            padding: 1rem 0;
            position: sticky; top: 0; z-index: 50;
            border-bottom: 4px solid var(--green-accent);
        }

        .container {
            max-width: 1200px; margin: 0 auto; padding: 0 1.5rem;
        }

        .header-content {
            display: flex; justify-content: space-between; align-items: center;
        }

        .logo {
            display: flex; align-items: center; gap: 12px;
            font-weight: 800; font-size: 1.35rem; color: var(--green-dark);
            letter-spacing: -0.5px;
        }
        .logo img { height: 48px; width: auto; }

        /* NAV MENU (Desktop) */
        .nav-menu {
            display: flex; align-items: center; gap: 20px;
        }

        .nav-link-custom {
            color: #555; font-weight: 700; font-size: 0.95rem;
            display: flex; align-items: center; gap: 8px;
            padding: 8px 16px; border-radius: 20px; transition: all 0.2s;
        }
        .nav-link-custom:hover { background-color: #c8e6c9; color: var(--green-dark); }

        .btn-logout {
            color: #ef4444 !important;
            border: 1px solid transparent;
        }
        .btn-logout:hover { 
            background-color: #fee2e2; 
            color: #dc2626 !important; 
            border-color: #ef4444;
        }

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
        .hamburger-line {
            width: 25px; height: 3px; background-color: var(--green-dark); border-radius: 5px;
        }

        /* --- MOBILE RESPONSIVE --- */
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
            .header-content { justify-content: space-between; }
        }

        /* --- CONTENT --- */
        main { flex: 1; padding: 3.5rem 0; }
        
        .page-header { text-align: center; margin-bottom: 3.5rem; }
        
        .page-title { 
            font-size: 2.25rem; font-weight: 900; 
            color: var(--green-dark); margin-bottom: 0.5rem; 
            letter-spacing: -0.5px;
        }
        
        .page-subtitle { color: #555; font-size: 1.15rem; font-weight: 500; }

        /* --- GRID --- */
        .guru-grid-wrapper {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem; max-width: 1000px; margin: 0 auto;
        }

        /* --- CARDS (High Contrast) --- */
        .guru-card {
            background-color: var(--card-bg);
            border-radius: 1.25rem; padding: 2.5rem 2rem;
            text-align: center; text-decoration: none;
            box-shadow: var(--shadow-card);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            height: 100%; position: relative;
            border-top: 5px solid transparent; /* Border Top Warna Warni */
        }
        .guru-card:hover { transform: translateY(-8px); box-shadow: var(--shadow-hover); }

        .icon-wrapper {
            width: 80px; height: 80px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1.5rem; transition: transform 0.4s ease;
            color: white; font-size: 2rem;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        }
        .guru-card:hover .icon-wrapper { transform: scale(1.15) rotate(5deg); }

        .guru-card h3 {
            font-size: 1.25rem; font-weight: 800; color: var(--text-body); margin-bottom: 0.5rem;
        }
        .guru-card p {
            font-size: 0.95rem; color: #666; line-height: 1.5;
        }

        /* --- CARD SPECIFIC COLORS --- */
        
        /* 1. E-Rapot (Orange) */
        .card-rapor { border-top-color: #fb8c00; }
        .card-rapor .icon-wrapper { background: linear-gradient(135deg, #ffa726, #ef6c00); }

        /* 2. LMS (Blue) */
        .card-lms { border-top-color: #1e88e5; }
        .card-lms .icon-wrapper { background: linear-gradient(135deg, #42a5f5, #1565c0); }

        /* 3. Analytics (Teal) - Biar beda dari ijo biasa */
        .card-analytics { border-top-color: #00897b; }
        .card-analytics .icon-wrapper { background: linear-gradient(135deg, #26a69a, #00695c); }

        /* --- FOOTER --- */
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
                <img src="{{ asset('assets/1.png') }}" alt="Logo SD Bawamai" onerror="this.style.display='none'"> 
                <span>Ruang Guru Bawamai</span>
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
                            <i class="fas fa-user text-secondary" style="font-size: 1rem;"></i>
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
                <h1 class="page-title">Menu Utama Guru</h1>
                <p class="page-subtitle">Selamat datang di panel pengelolaan kelas dan pembelajaran.</p>
            </div>
            
            <div class="guru-grid-wrapper">
                
                {{-- 1. E-RAPOR --}}
                <a href="{{ route('guru.raport.index') }}" class="guru-card card-rapor">
                    <div class="icon-wrapper"><i class="fas fa-book-open"></i></div>
                    <h3>e-Rapot</h3>
                    <p>Input dan upload berkas raport siswa per semester.</p>
                </a>

                {{-- 2. MANAJEMEN LMS --}}
                <a href="{{ route('lms-items.index') }}" class="guru-card card-lms">
                    <div class="icon-wrapper"><i class="fas fa-chalkboard-teacher"></i></div>
                    <h3>Manajemen LMS</h3>
                    <p>Upload materi, kelola folder kelas, dan tugas.</p>
                </a>

                {{-- 3. ANALYTICS SISWA --}}
                <a href="{{ route('admin.analytics') }}" class="guru-card card-analytics">
                    <div class="icon-wrapper"><i class="fas fa-chart-pie"></i></div>
                    <h3>Statistik Belajar</h3>
                    <p>Lihat keaktifan siswa membuka materi Anda.</p>
                </a>
                
            </div>
        </div>
    </main>

    <footer class="main-footer">
        <div class="container">
            <div class="footer-copyright">
                <p>&copy; {{ date('Y') }} SD Bawamai Pontianak - Teacher Dashboard</p>
            </div>
        </div>
    </footer>

    {{-- MODAL PENGATURAN (SETTINGS) --}}
    <div class="modal fade" id="settingsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header text-white" style="background-color: var(--green-dark);">
                    <h5 class="modal-title"><i class="fas fa-cog me-2"></i> Pengaturan Akun</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
            <div class="modal-body">
    <ul class="nav nav-tabs nav-justified mb-4" id="settingTab" role="tablist">
        {{-- Tab Foto --}}
        <li class="nav-item">
            <button class="nav-link active fw-bold" id="foto-tab" data-bs-toggle="tab" data-bs-target="#foto" type="button" role="tab">
                <i class="fas fa-camera me-1"></i> Foto
            </button>
        </li>
        
        {{-- Tab Portofolio (Hanya Muncul Jika Guru) --}}
        @if(Auth::user()->role == 'guru')
        <li class="nav-item">
            <button class="nav-link fw-bold" id="portofolio-tab" data-bs-toggle="tab" data-bs-target="#portofolio" type="button" role="tab">
                <i class="fas fa-file-pdf me-1"></i> Portofolio
            </button>
        </li>
        @endif

        {{-- Tab Password --}}
        <li class="nav-item">
            <button class="nav-link fw-bold" id="pass-tab" data-bs-toggle="tab" data-bs-target="#pass" type="button" role="tab">
                <i class="fas fa-key me-1"></i> Password
            </button>
        </li>
    </ul>

    <div class="tab-content">
        
        <div class="tab-pane fade show active" id="foto" role="tabpanel">
            <form action="{{ route('profile.updateFoto') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="text-center mb-4">
                    {{-- Wadah Preview --}}
                    <div class="position-relative d-inline-block">
                        @if(Auth::user()->foto_profil)
                            <img src="{{ asset(Auth::user()->foto_profil) }}" id="previewImg" 
                                 class="rounded-circle border shadow-sm" 
                                 style="width: 120px; height: 120px; object-fit: cover;">
                        @else
                            {{-- Placeholder jika belum ada foto --}}
                            <div id="placeholderIcon" class="rounded-circle bg-light d-flex align-items-center justify-content-center border shadow-sm" 
                                 style="width: 120px; height: 120px;">
                                <i class="fas fa-user fa-3x text-secondary"></i>
                            </div>
                            {{-- Image hidden untuk preview nanti --}}
                            <img src="#" id="previewImg" class="rounded-circle border shadow-sm d-none" 
                                 style="width: 120px; height: 120px; object-fit: cover;">
                        @endif
                        
                        {{-- Ikon Edit Kecil --}}
                        <div class="position-absolute bottom-0 end-0 bg-white rounded-circle p-1 shadow border" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-pen text-primary small"></i>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Upload Foto Baru (Max 2MB)</label>
                    <input type="file" name="foto" class="form-control" accept="image/*" onchange="previewFile(this)">
                </div>
                <button type="submit" class="btn btn-primary w-100 rounded-pill">
                    <i class="fas fa-save me-1"></i> Simpan Perubahan Foto
                </button>
            </form>
        </div>

        @if(Auth::user()->role == 'guru')
        <div class="tab-pane fade" id="portofolio" role="tabpanel">
            <form action="{{ route('profile.updatePortofolio') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="text-center mb-4 py-3 bg-light rounded border border-dashed">
                    <i class="fas fa-file-pdf text-danger fa-3x mb-2"></i>
                    <h6 class="fw-bold text-dark">Portofolio Guru</h6>
                    <small class="text-muted">Format PDF, Maksimal 5MB</small>
                </div>

                {{-- Info File Saat Ini --}}
                @php $guru = \App\Models\Guru::where('user_id', Auth::id())->first(); @endphp
                @if($guru && $guru->portofolio)
                    <div class="alert alert-success d-flex align-items-center justify-content-between p-2 mb-3">
                        <small class="fw-bold"><i class="fas fa-check-circle me-1"></i> File Tersedia</small>
                        <a href="{{ asset($guru->portofolio) }}" target="_blank" class="btn btn-sm btn-light text-success border fw-bold">
                            <i class="fas fa-eye me-1"></i> Lihat
                        </a>
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label small fw-bold">Pilih File PDF</label>
                    <input type="file" name="portofolio" class="form-control" accept="application/pdf" required>
                </div>

                <button type="submit" class="btn btn-success w-100 rounded-pill">
                    <i class="fas fa-cloud-upload-alt me-1"></i> Upload Portofolio
                </button>
            </form>
        </div>
        @endif

        <div class="tab-pane fade" id="pass" role="tabpanel">
            <form action="{{ route('profile.updatePassword') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="small fw-bold">Password Lama</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-lock"></i></span>
                        <input type="password" name="current_password" class="form-control" required placeholder="******">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="small fw-bold">Password Baru</label>
                    <input type="password" name="password" class="form-control" required placeholder="Minimal 6 karakter">
                </div>
                <div class="mb-3">
                    <label class="small fw-bold">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="form-control" required placeholder="Ulangi password baru">
                </div>
                <button type="submit" class="btn btn-warning w-100 rounded-pill text-white fw-bold">
                    <i class="fas fa-check me-1"></i> Ganti Password
                </button>
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
    <script>
    function previewFile(input) {
        var file = input.files[0];
        if(file){
            var reader = new FileReader();
            reader.onload = function(){
                var previewImg = document.getElementById('previewImg');
                var placeholder = document.getElementById('placeholderIcon');

                if(previewImg) {
                    previewImg.src = reader.result;
                    previewImg.classList.remove('d-none'); // Munculkan gambar
                }
                if(placeholder) {
                    placeholder.classList.add('d-none');   // Sembunyikan ikon orang
                }
            }
            reader.readAsDataURL(file);
        }
    }
</script>
</body>
</html>