<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guru Dashboard - LMS Bawamai</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* --- RESET & VARIABLES (Sama seperti Admin) --- */
        :root {
            --primary-bg: #4b95d2d6; /* Biru Background */
            --card-bg: #ffffff;
            --text-main: #1f2937;
            --text-muted: #000000ff;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

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
        .logo img { height: 45px; width: auto; }

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
        .btn-logout:hover { color: #dc2626; }

        /* --- MAIN CONTENT --- */
        main { flex: 1; padding: 3rem 0; }

        .page-header { text-align: center; margin-bottom: 3rem; }
        .page-title { font-size: 2rem; font-weight: 800; color: #111827; margin-bottom: 0.5rem; }
        .page-subtitle { color: var(--text-muted); font-size: 1.1rem; }

        /* --- GRID SYSTEM --- */
        .guru-grid-wrapper {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); /* Lebih lebar dikit karena menunya sedikit */
            gap: 2rem;
            max-width: 1000px; /* Batasi lebar agar kartu tidak terlalu melar */
            margin: 0 auto;
        }

        /* --- CARDS --- */
        .guru-card {
            background-color: var(--card-bg);
            border-radius: 1rem;
            padding: 2.5rem 2rem;
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

        .guru-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
        }

        .icon-wrapper {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease;
        }

        .guru-card:hover .icon-wrapper { transform: scale(1.1) rotate(5deg); }
        .guru-card svg, .guru-card i { font-size: 35px; color: white; }

        .guru-card h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .guru-card p {
            font-size: 0.9rem;
            color: #6b7280;
            line-height: 1.4;
        }

        /* --- WARNA KARTU --- */
        /* E-Rapor (Hijau Emerald) */
        .card-rapor .icon-wrapper { background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 4px 10px rgba(5, 150, 105, 0.3); }
        
        /* Smart Learning (Biru Utama) */
        .card-lms .icon-wrapper { background: linear-gradient(135deg, #3b82f6, #2563eb); box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3); }
        
        /* Analytics (Ungu) */
        .card-analytics .icon-wrapper { background: linear-gradient(135deg, #8b5cf6, #7c3aed); box-shadow: 0 4px 10px rgba(124, 58, 237, 0.3); }

        /* --- FOOTER --- */
        .main-footer {
            background: white;
            padding: 1.5rem 0;
            text-align: center;
            border-top: 1px solid #f3f4f6;
            margin-top: auto;
        }
        .footer-copyright { color: #9ca3af; font-size: 0.85rem; font-weight: 600; }

        /* --- RESPONSIVE --- */
        @media (max-width: 768px) {
            .header-content { flex-direction: column; gap: 1rem; }
            .guru-grid-wrapper { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <header class="main-header">
        <div class="container header-content">
            <a href="#" class="logo">
                {{-- Pastikan file asset logo ada --}}
                <img src="{{ asset('assets/1.png') }}" alt="Logo SD Bawamai" onerror="this.style.display='none'"> 
                <span>Ruang Guru Bawamai</span>
            </a>

            <nav class="user-menu">
                <span class="user-name">Halo, <strong>{{ Auth::user()->name }}</strong> (Guru)</span>
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
                <h1 class="page-title">Menu Utama Guru</h1>
                <p class="page-subtitle">Selamat datang di panel pengelolaan kelas dan pembelajaran.</p>
            </div>
            
            <div class="guru-grid-wrapper">
                
                {{-- 1. E-RAPOR (Akan kita buat fiturnya setelah ini) --}}
                {{-- Menggunakan route sementara '#' nanti kita ganti ke 'raport-guru.index' --}}
                <a href="{{ route('guru.raport.index') }}" class="guru-card card-rapor">
                        <div class="icon-wrapper">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <h3>e-Rapot</h3>
                    <p>Input dan upload berkas raport siswa per semester.</p>
                </a>

                {{-- 2. SMART LEARNING (Link ke Landing Page / LMS) --}}
                <a href="{{ route('landing') }}" class="guru-card card-lms">
                    <div class="icon-wrapper">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h3>Smart Learning</h3>
                    <p>Akses materi pembelajaran dan modul LMS.</p>
                </a>

                {{-- 3. ANALYTICS (Bypass ke Analytics Admin) --}}
                {{-- Pastikan route 'admin.analytics' bisa diakses oleh role guru di web.php --}}
                <a href="{{ route('admin.analytics') }}" class="guru-card card-analytics">
                    <div class="icon-wrapper">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Analytics</h3>
                    <p>Pantau aktivitas belajar dan statistik akses siswa.</p>
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
</body>
</html>