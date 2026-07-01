<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kepala Sekolah - SD Bawamai</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* --- VARIABLES --- */
        :root {
            --primary-bg: #e8f5e9;    /* Hijau Mint Lembut */
            --card-bg: #ffffff;
            --text-main: #1b5e20;     /* Hijau Tua */
            --text-body: #374151;     /* Abu Gelap */
            --green-accent: #2e7d32;
            --green-dark: #1b5e20;
            --shadow-card: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-hover: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        body {
            font-family: 'Nunito', sans-serif;
            background-color: var(--primary-bg);
            color: var(--text-body);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        a { text-decoration: none; }

        /* --- HEADER --- */
        .main-header {
            background-color: #ffffff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 0.8rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 3px solid var(--green-accent);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 1.25rem;
            color: var(--green-dark);
        }
        .logo img { height: 40px; width: auto; }

        /* --- NAVIGATION --- */
        .nav-link-custom {
            color: #555;
            font-weight: 700;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 50px;
            transition: all 0.2s;
        }
        .nav-link-custom:hover {
            background-color: #f1f8e9;
            color: var(--green-dark);
        }

        .nav-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #a5d6a7;
        }

        .btn-logout {
            color: #dc2626 !important;
        }
        .btn-logout:hover {
            background-color: #fee2e2;
        }

        /* --- CONTENT AREA --- */
        main { flex: 1; padding: 3rem 0; }

        .page-title {
            font-size: 2rem;
            font-weight: 800;
            color: var(--green-dark);
            margin-bottom: 0.5rem;
        }
        .page-subtitle {
            color: #666;
            font-size: 1.1rem;
        }

        /* --- CARDS --- */
        .menu-card {
            background-color: var(--card-bg);
            border-radius: 1rem;
            padding: 2rem 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-card);
            border-top: 5px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .icon-circle {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
            font-size: 1.75rem;
            color: white;
            transition: transform 0.3s ease;
        }

        .menu-card:hover .icon-circle {
            transform: scale(1.1);
        }

        .menu-card h3 {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 0.5rem;
        }

        .menu-card p {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0;
            line-height: 1.4;
        }

        /* CARD COLORS */
        /* Analytics (Hijau) */
        .card-analytics { border-top-color: #4caf50; }
        .card-analytics .icon-circle { background: linear-gradient(135deg, #2e7d32, #66bb6a); }

        /* Guru (Orange) */
        .card-teacher { border-top-color: #ff9800; }
        .card-teacher .icon-circle { background: linear-gradient(135deg, #ef6c00, #ffa726); }

        /* Siswa (Biru) */
        .card-student { border-top-color: #1e88e5; }
        .card-student .icon-circle { background: linear-gradient(135deg, #1565c0, #42a5f5); }

        /* Smart Learning (Ungu) */
        .card-smart { border-top-color: #8e24aa; }
        .card-smart .icon-circle { background: linear-gradient(135deg, #6a1b9a, #ab47bc); }

        /* --- FOOTER --- */
        .main-footer {
            background-color: #fff;
            padding: 1.5rem 0;
            border-top: 1px solid #e0e0e0;
            text-align: center;
            font-size: 0.9rem;
            color: #888;
        }
    </style>
</head>
<body>

    <header class="main-header">
        <div class="container d-flex justify-content-between align-items-center">
            
            <a href="#" class="logo text-decoration-none">
                <img src="{{ asset('assets/1.png') }}" alt="Logo">
                <span>Portal Yayasan</span>
            </a>

            <div class="d-flex align-items-center gap-3">
                
                <div class="dropdown">
                    <a href="#" class="nav-link-custom text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                        @if(Auth::user()->foto_profil)
                            <img src="{{ asset(Auth::user()->foto_profil) }}" class="nav-avatar" alt="User">
                        @else
                            <div class="nav-avatar bg-light d-flex align-items-center justify-content-center">
                                <i class="fas fa-user text-secondary" style="font-size: 0.8rem;"></i>
                            </div>
                        @endif
                        <span class="d-none d-md-inline ms-1">{{ Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li>
                            <a class="dropdown-item py-2" href="#" data-bs-toggle="modal" data-bs-target="#settingsModal">
                                <i class="fas fa-cog me-2 text-secondary"></i> Pengaturan
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i> Keluar
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </header>

    <main>
        <div class="container">
            
            <div class="text-center mb-5">
                <h1 class="page-title">Dashboard Monitoring</h1>
                <p class="page-subtitle">Pantau perkembangan siswa dan kinerja guru secara real-time.</p>
            </div>
            


            <div class="row g-4 justify-content-center">
                
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="{{ route('admin.analytics') }}" class="menu-card card-analytics text-decoration-none">
                        <div class="icon-circle">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3>Analytics Siswa</h3>
                        <p>Statistik akses & aktivitas</p>
                    </a>
                </div>
                


                <div class="col-6 col-md-4 col-lg-3">
                    <a href="{{ route('admin.analytics.teachers') }}" class="menu-card card-teacher text-decoration-none">
                        <div class="icon-circle">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <h3>Kinerja Guru</h3>
                        <p>Monitor keaktifan guru</p>
                    </a>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <a href="{{ route('siswa.index') }}" class="menu-card card-student text-decoration-none">
                        <div class="icon-circle">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h3>Data Siswa</h3>
                        <p>Database seluruh siswa</p>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('guru.index') }}" class="menu-card card-student text-decoration-none">
                    <div class="icon-circle">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h3>Data Guru</h3>
                    <p>Database seluruh guru</p>

                </a>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <a href="{{ route('landing') }}" class="menu-card card-smart text-decoration-none">
                        <div class="icon-circle">
                            <i class="fas fa-laptop-code"></i>
                        </div>
                        <h3>Smart Learning</h3>
                        <p>Akses fitur pembelajaran</p>
                    </a>
                </div>

            </div>
        </div>
    </main>

    <footer class="main-footer">
        <div class="container">
            <p class="mb-0">&copy; {{ date('Y') }} SD Bawamai Pontianak - Portal Yayasan</p>
        </div>
    </footer>

    <div class="modal fade" id="settingsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white" style="background-color: var(--green-dark);">
                    <h5 class="modal-title fs-6 fw-bold"><i class="fas fa-user-cog me-2"></i> Pengaturan Akun</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    
                    <ul class="nav nav-pills nav-justified mb-4" id="settingTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill btn-sm fw-bold" id="foto-tab" data-bs-toggle="tab" data-bs-target="#foto" type="button" role="tab">Foto Profil</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill btn-sm fw-bold" id="pass-tab" data-bs-toggle="tab" data-bs-target="#pass" type="button" role="tab">Password</button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="foto" role="tabpanel">
                            <form action="{{ route('profile.updateFoto') }}" method="POST" enctype="multipart/form-data" class="text-center">
                                @csrf
                                <div class="mb-3">
                                    @if(Auth::user()->foto_profil)
                                        <img src="{{ asset(Auth::user()->foto_profil) }}" id="previewImg" class="rounded-circle shadow-sm border" style="width: 100px; height: 100px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto border" style="width: 100px; height: 100px;">
                                            <i class="fas fa-user text-secondary fa-3x"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <input type="file" name="foto" class="form-control form-control-sm" accept="image/*">
                                </div>
                                <button type="submit" class="btn btn-success btn-sm w-100 rounded-pill fw-bold">Simpan Foto</button>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="pass" role="tabpanel">
                            <form action="{{ route('profile.updatePassword') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Password Lama</label>
                                    <input type="password" name="current_password" class="form-control form-control-sm" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Password Baru</label>
                                    <input type="password" name="password" class="form-control form-control-sm" required>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-muted">Konfirmasi Password</label>
                                    <input type="password" name="password_confirmation" class="form-control form-control-sm" required>
                                </div>
                                <button type="submit" class="btn btn-warning btn-sm w-100 rounded-pill text-white fw-bold">Ganti Password</button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>