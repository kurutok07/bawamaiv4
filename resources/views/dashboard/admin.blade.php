<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - LMS Bawamai</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* --- RESET & VARIABLES --- */
        :root {
            --primary-bg: #4b95d2d6;
            --card-bg: #ffffff;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background-color: var(--primary-bg);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* --- HEADER --- */
        .main-header {
            background-color: #ffffff;
            box-shadow: var(--shadow-sm);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
            font-size: 1.25rem;
            color: #111827;
            text-decoration: none;
        }

        .logo img {
            height: 45px;
            width: auto;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-name {
            font-size: 0.95rem;
            color: var(--text-muted);
            background: #f9fafb;
            padding: 8px 16px;
            border-radius: 20px;
            border: 1px solid #e5e7eb;
        }

        .btn-logout {
            background: none;
            border: none;
            color: #ef4444;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: color 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
        }

        .btn-logout:hover {
            color: #dc2626;
        }

        /* --- MAIN CONTENT --- */
        main {
            flex: 1;
            padding: 3rem 0;
        }

        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 800;
            color: #111827;
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            color: var(--text-muted);
            font-size: 1.1rem;
        }

        /* --- GRID SYSTEM --- */
        .admin-grid-wrapper {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
        }

        /* --- CARDS --- */
        .admin-card {
            background-color: var(--card-bg);
            border-radius: 1rem;
            padding: 2rem;
            text-align: center;
            text-decoration: none;
            box-shadow: var(--shadow-md);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(0,0,0,0.02);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
        }

        .admin-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            border-color: rgba(0,0,0,0.05);
        }

        .icon-wrapper {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
            transition: transform 0.3s ease;
        }

        .admin-card:hover .icon-wrapper {
            transform: scale(1.1) rotate(5deg);
        }

        .admin-card svg {
            width: 35px;
            height: 35px;
            color: white;
        }

        .admin-card h3 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #374151;
            margin: 0;
        }

        /* --- WARNA WARNI OTOMATIS (Tanpa ubah HTML) --- */
        /* Card 1 - Biru */
        .admin-card:nth-child(1) .icon-wrapper { background: linear-gradient(135deg, #3b82f6, #2563eb); box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3); }
        /* Card 2 - Hijau */
        .admin-card:nth-child(2) .icon-wrapper { background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 4px 10px rgba(5, 150, 105, 0.3); }
        /* Card 3 - Ungu */
        .admin-card:nth-child(3) .icon-wrapper { background: linear-gradient(135deg, #8b5cf6, #7c3aed); box-shadow: 0 4px 10px rgba(124, 58, 237, 0.3); }
        /* Card 4 - Orange */
        .admin-card:nth-child(4) .icon-wrapper { background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 4px 10px rgba(217, 119, 6, 0.3); }
        /* Card 5 - Pink */
        .admin-card:nth-child(5) .icon-wrapper { background: linear-gradient(135deg, #ec4899, #db2777); box-shadow: 0 4px 10px rgba(219, 39, 119, 0.3); }
        /* Card 6 - Cyan */
        .admin-card:nth-child(6) .icon-wrapper { background: linear-gradient(135deg, #06b6d4, #0891b2); box-shadow: 0 4px 10px rgba(8, 145, 178, 0.3); }
        /* Card 7 - Merah */
        .admin-card:nth-child(7) .icon-wrapper { background: linear-gradient(135deg, #ef4444, #dc2626); box-shadow: 0 4px 10px rgba(220, 38, 38, 0.3); }
        /* Card 8 - Abu/Gelap */
        .admin-card:nth-child(8) .icon-wrapper { background: linear-gradient(135deg, #6b7280, #4b5563); box-shadow: 0 4px 10px rgba(75, 85, 99, 0.3); }

        /* --- FOOTER --- */
        .main-footer {
            background: white;
            padding: 1.5rem 0;
            text-align: center;
            border-top: 1px solid #f3f4f6;
            margin-top: auto;
        }

        .footer-copyright {
            color: #9ca3af;
            font-size: 0.85rem;
            font-weight: 600;
        }

        /* --- RESPONSIVE --- */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 1rem;
            }
            .admin-grid-wrapper {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 480px) {
            .admin-grid-wrapper {
                grid-template-columns: 1fr;
            }
        }
        .btn { padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer; border: none; text-decoration: none; display: inline-block; font-size: 0.9rem;}
        .btn-primary { background: #3b82f6; color: white; }
        .btn-primary:hover { background: #2563eb; }
        .btn-danger { background: #ef4444; color: white; }
        .btn-sm { padding: 4px 10px; font-size: 0.8rem; }
        
        .table-container { background: white; border-radius: 12px; box-shadow: var(--shadow-sm); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f9fafb; text-align: left; padding: 12px 20px; font-size: 0.85rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e5e7eb; }
        td { padding: 16px 20px; border-bottom: 1px solid #f3f4f6; color: #374151; font-size: 0.95rem; }
        tr:last-child td { border-bottom: none; }
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-secondary { background: #f3f4f6; color: #374151; }
        
        .form-group { margin-bottom: 15px; }
        .form-label { display: block; margin-bottom: 5px; color: #374151; font-weight: 600; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; }
    </style>
</head>
<body>

    <header class="main-header">
        <div class="container header-content">
            <a href="#" class="logo">
                <img src="{{ asset('assets/1.png') }}" alt="Logo SD Bawamai"> 
                <span>SD Bawamai Pontianak</span>
            </a>

            <nav class="user-menu">
                <span class="user-name">Halo, <strong>{{ Auth::user()->name }}</strong></span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout">
                        Logout <i class="fa fa-sign-out-alt"></i>
                    </button>
                </form>
            </nav>
        </div>
    </header> 

    <main>
        <div class="container">
            <div class="page-header">
                <h1 class="page-title">Menu Utama Admin</h1>
                <p class="page-subtitle">Kelola sistem sekolah dan pembelajaran dari sini.</p>
            </div>
            
            <div class="admin-grid-wrapper">
                
                {{-- 1. TAHUN AJARAN --}}
                <a href="{{ route('tahun-ajaran.index') }}" class="admin-card">
                    <div class="icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                    </div>
                    <h3>Tahun Ajaran</h3>
                </a>

                {{-- 2. DATA GURU --}}
                <a href="{{ route('guru.index') }}" class="admin-card">
                    <div class="icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </div>
                    <h3>Data Guru</h3>
                </a>

                {{-- 3. DATA SISWA --}}
                <a href="{{ route('siswa.index') }}" class="admin-card">
                    <div class="icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.499 5.216 50.59 50.59 0 00-2.658.813m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
                        </svg>
                    </div>
                    <h3>Data Siswa</h3>
                </a>

                {{-- 4. MANAJEMEN KELAS --}}
                <a href="{{ route('kelas.index') }}" class="admin-card">
                    <div class="icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" />
                        </svg>
                    </div>
                    <h3>Manajemen Kelas</h3>
                </a>

                {{-- 5. MANAJEMEN LMS (lms-items.index karena lms.show untuk user) --}}
                <a href="{{ route('lms-items.index') }}" class="admin-card">
                    <div class="icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                    <h3>LMS</h3>
                </a>

                {{-- 6. E-RAPORT (Belum ada route, biarkan #) --}}
                <a href="{{ route('admin.raport-categories.index') }}" class="admin-card">
                    <div class="icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </div>
                    <h3>e-Rapot</h3>
                </a>

                {{-- 7. ANALYTICS --}}
                <a href="{{ route('admin.analytics') }}" class="admin-card">
                    <div class="icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" />
                        </svg>
                    </div>
                    <h3>Analytics</h3>
                </a>

                {{-- 8. PENGATURAN (Belum ada route, biarkan #) --}}
                <a href="{{ route('landing') }}" class="admin-card">
                    <div class="icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 001.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 010 3.46" />
                        </svg>
                    </div>
                    <h3>Smart Learning</h3>
                </a>
                
            </div>
        </div>
    </main>

    <footer class="main-footer">
        <div class="container">
            <div class="footer-copyright">
                <p>&copy; {{ date('Y') }} SD Bawamai Pontianak - Learning Management System</p>
            </div>
        </div>
    </footer>
    
</body>
</html>