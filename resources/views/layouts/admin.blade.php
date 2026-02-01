    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title', 'Admin Dashboard') - LMS Bawamai</title> <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
        /* --- RESET & VARIABLES (GREEN THEME - HIGH CONTRAST) --- */
        :root {
            --primary-bg: #e8f5e9; /* Hijau Mint Lembut untuk Background Halaman */
            --card-bg: #ffffff;
            --text-main: #1b5e20;  /* Hijau Tua untuk Teks Utama */
            --text-body: #374151;  /* Abu Gelap untuk Teks Biasa */
            --green-accent: #2e7d32; /* Hijau Daun untuk Aksen */
            --green-dark: #1b5e20;
            --shadow-soft: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            --shadow-card: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
            --shadow-hover: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background-color: var(--primary-bg);
            color: var(--text-body);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* --- HEADER --- */
        .main-header {
            background-color: #ffffff;
            box-shadow: var(--shadow-soft);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 50;
            border-bottom: 4px solid var(--green-accent); /* Border Bawah Hijau Tegas */
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
            font-size: 1.35rem;
            color: var(--green-dark);
            text-decoration: none;
            letter-spacing: -0.5px;
        }

        .logo img {
            height: 48px;
            width: auto;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-name {
            font-size: 0.95rem;
            color: var(--green-dark);
            background: #c8e6c9; /* Hijau Pastel Lebih Pekat */
            padding: 8px 18px;
            border-radius: 30px;
            font-weight: 700;
            border: 1px solid #a5d6a7;
        }

        .btn-logout {
            background: white;
            border: 1px solid #ef4444;
            color: #ef4444;
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
        }

        .btn-logout:hover {
            background: #ef4444;
            color: white;
        }

        /* --- MAIN CONTENT --- */
        main {
            flex: 1;
            padding: 3.5rem 0;
        }

        .page-header {
            text-align: center;
            margin-bottom: 3.5rem;
        }

        .page-title {
            font-size: 2.25rem;
            font-weight: 900;
            color: var(--green-dark);
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }

        .page-subtitle {
            color: #555;
            font-size: 1.15rem;
            font-weight: 500;
        }

        /* --- GRID SYSTEM --- */
        .admin-grid-wrapper {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 2rem;
        }

        /* --- CARDS (High Contrast Style) --- */
        .admin-card {
            background-color: var(--card-bg);
            border-radius: 1.25rem; /* Sudut lebih bulat */
            padding: 2.5rem 1.5rem;
            text-align: center;
            text-decoration: none;
            box-shadow: var(--shadow-card); /* Shadow lebih dalam */
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            border-top: 5px solid transparent; /* Persiapan untuk warna warni */
        }

        .admin-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-hover);
        }

        /* Icon Wrapper */
        .icon-wrapper {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            transition: transform 0.4s ease;
            color: white;
            font-size: 1.75rem;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); /* Shadow pada icon */
        }

        .admin-card:hover .icon-wrapper {
            transform: scale(1.15) rotate(5deg);
        }

        .admin-card h3 {
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--text-body);
            margin: 0;
            letter-spacing: -0.3px;
        }

        /* --- WARNA WARNI CARD (Border Top & Icon) --- */
        
        /* 1. Tahun Ajaran (Teal) */
        .admin-card:nth-child(1) { border-top-color: #00897b; }
        .admin-card:nth-child(1) .icon-wrapper { background: linear-gradient(135deg, #26a69a, #00695c); }
        
        /* 2. Data Guru (Green) */
        .admin-card:nth-child(2) { border-top-color: #43a047; }
        .admin-card:nth-child(2) .icon-wrapper { background: linear-gradient(135deg, #66bb6a, #2e7d32); }
        
        /* 3. Data Siswa (Light Green) */
        .admin-card:nth-child(3) { border-top-color: #7cb342; }
        .admin-card:nth-child(3) .icon-wrapper { background: linear-gradient(135deg, #9ccc65, #558b2f); }
        
        /* 4. Manajemen Kelas (Lime) */
        .admin-card:nth-child(4) { border-top-color: #c0ca33; }
        .admin-card:nth-child(4) .icon-wrapper { background: linear-gradient(135deg, #d4e157, #9e9d24); }
        
        /* 5. LMS (Blue) */
        .admin-card:nth-child(5) { border-top-color: #1e88e5; }
        .admin-card:nth-child(5) .icon-wrapper { background: linear-gradient(135deg, #42a5f5, #1565c0); }
        
        /* 6. E-Rapot (Orange) */
        .admin-card:nth-child(6) { border-top-color: #fb8c00; }
        .admin-card:nth-child(6) .icon-wrapper { background: linear-gradient(135deg, #ffa726, #ef6c00); }
        
        /* 7. Analytics (Purple) */
        .admin-card:nth-child(7) { border-top-color: #8e24aa; }
        .admin-card:nth-child(7) .icon-wrapper { background: linear-gradient(135deg, #ab47bc, #7b1fa2); }
        
        /* 8. Smart Learning (Cyan) */
        .admin-card:nth-child(8) { border-top-color: #00acc1; }
        .admin-card:nth-child(8) .icon-wrapper { background: linear-gradient(135deg, #26c6da, #00838f); }
        
        /* 9. Kinerja Guru (Red) */
        .admin-card:nth-child(9) { border-top-color: #e53935; }
        .admin-card:nth-child(9) .icon-wrapper { background: linear-gradient(135deg, #ef5350, #c62828); }
        
        /* 10. Akun Yayasan (Gold/Brown) */
        .admin-card:nth-child(10) { border-top-color: #d4af37; }
        .admin-card:nth-child(10) .icon-wrapper { background: linear-gradient(135deg, #fbc02d, #8d6e63); }


        /* --- FOOTER --- */
        .main-footer {
            background: white;
            padding: 2rem 0;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            margin-top: auto;
        }

        .footer-copyright {
            color: #888;
            font-size: 0.9rem;
            font-weight: 600;
        }

        /* --- RESPONSIVE --- */
        @media (max-width: 768px) {
            .header-content { flex-direction: column; gap: 1rem; }
            .admin-grid-wrapper { grid-template-columns: repeat(2, 1fr); gap: 1rem; }
            .admin-card { padding: 1.5rem; }
            .icon-wrapper { width: 60px; height: 60px; font-size: 1.25rem; margin-bottom: 1rem; }
        }
        @media (max-width: 480px) {
            .admin-grid-wrapper { grid-template-columns: 1fr; }
        }
    </style>

    </head>
    <body>
        <header class="main-header">
            <div class="container header-content">
                <a href="{{ route('dashboard') }}" class="logo">
                    <img src="{{ asset('assets/1.png') }}" alt="Logo"> 
                    <span>SD Bawamai</span>
                </a>
                <nav class="user-menu">
                    <span class="user-name">{{ Auth::user()->name }}</span>
                </nav>
            </div>
        </header> 

        <main>
            <div class="container">
                @yield('content')
            </div>
        </main>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>

    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        
        @yield('scripts')
        </body>
    </html>