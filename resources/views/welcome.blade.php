<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LMS SD Bawamai Pontianak</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"> 
    
    <link rel="icon" type="image/png" href="{{ asset('assets/1.png') }}">

    <style>
        /* --- STYLE TAMBAHAN UNTUK DINAMIS --- */

        /* 1. Warna-warni Otomatis untuk Card (Looping 6 Warna) */
        .fitur-card:nth-child(6n+1) .fitur-card-icon-bg { background-color: #e4f5e9; } /* Hijau Muda */
        .fitur-card:nth-child(6n+2) .fitur-card-icon-bg { background-color: #fff9c4; } /* Kuning */
        .fitur-card:nth-child(6n+3) .fitur-card-icon-bg { background-color: #e3f2fd; } /* Biru Muda */
        .fitur-card:nth-child(6n+4) .fitur-card-icon-bg { background-color: #ffe0b2; } /* Orange */
        .fitur-card:nth-child(6n+5) .fitur-card-icon-bg { background-color: #f3e5f5; } /* Ungu */
        .fitur-card:nth-child(6n+6) .fitur-card-icon-bg { background-color: #ffebee; } /* Merah Muda */

        /* 2. Style Tombol Login Custom */
        .login-btn {
            background-color: #0d47a1;
            color: white !important;
            padding: 8px 25px !important;
            border-radius: 25px;
            box-shadow: 0 4px 6px rgba(13, 71, 161, 0.2);
            transition: all 0.3s ease;
        }
        .login-btn:hover {
            background-color: #1565c0;
            transform: translateY(-2px);
            box-shadow: 0 6px 10px rgba(13, 71, 161, 0.3);
        }
        .login-btn::after { display: none; } /* Hapus garis bawah hover default */

        /* 3. Style SVG Default (Jika tidak ada gambar) */
        .svg-icon {
            width: 60px;
            height: 60px;
            fill: #0d47a1;
            opacity: 0.7;
        }

        /* 4. Perbaikan Navbar untuk User Login */
        .user-menu-item {
            font-weight: 800 !important;
            color: #0d47a1 !important;
        }
    </style>
</head>
<body class="dashboard-page">

    <div class="nav-overlay"></div>

    <header class="main-header">
        <div class="container header-flex">
          <div class="logo">
            <img src="{{ asset('assets/1.png') }}" alt="Logo SD Bawamai" class="logo-img">
            <span class="logo-text">SD Bawamai Pontianak</span>
          </div>
      
          <button class="nav-toggle" aria-label="Buka menu">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
          </button>
      
          <nav class="main-nav">
            <ul class="main-menu">
                <li><a href="{{ url('/') }}">Beranda</a></li>

                @auth
                    {{-- LOGIC BARU: Cek Role --}}
                    @if(Auth::user()->role === 'siswa')
                        {{-- Jika SISWA, arahkan ke Landing Page (Beranda) --}}
                        <li>
                            <a href="{{ route('landing') }}" class="user-menu-item"></a>
                        </li>
                    @else
                        {{-- Jika ADMIN/GURU, arahkan ke Dashboard --}}
                        <li>
                            <a href="{{ route('dashboard') }}" class="user-menu-item">Dashboard Saya</a>
                        </li>
                    @endif

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

    <main class="container" id="fitur">
        <h2 class="section-title">LEARNING MANAGEMENT SYSTEM (LMS)</h2>
        
        <div class="fitur-wrapper">
            <a href="{{ route('akses.raport') }}" class="fitur-card show" id="fitur-raport">
                <div class="fitur-card-icon-bg">
                    {{-- Ikon Gambar --}}
                    <img src="{{ asset('assets/dashboard/e-raport/seo-report.png') }}" 
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='block';" 
                        alt="E-Raport">
                    
                    {{-- Fallback Ikon SVG jika gambar gagal load --}}
                    <svg class="svg-icon" style="display:none;" viewBox="0 0 24 24">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 14H7v-2h5v2zm5-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                    </svg>
                </div>
                <h4>E-RAPORT</h4>
            </a>
            @forelse($menus as $menu)
                <a href="{{ route('lms.show', $menu->slug) }}" class="fitur-card show">
                    <div class="fitur-card-icon-bg">
                        @if($menu->cover_image && file_exists(public_path($menu->cover_image)))
                            <img src="{{ asset($menu->cover_image) }}" alt="{{ $menu->title }}">
                        @else
                            <svg class="svg-icon" viewBox="0 0 24 24">
                                <path d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z"/>
                            </svg>
                        @endif
                    </div>
                    <h4>{{ $menu->title }}</h4>
                </a>
            @empty
                <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #777;">
                    <i class="fas fa-box-open fa-3x" style="margin-bottom: 15px; opacity: 0.5;"></i>
                    <p>Menu LMS sedang disiapkan oleh Admin.</p>
                </div>
            @endforelse


                        
        </div>
    </main>

    <footer class="main-footer">
        <div class="container footer-content">
            <div class="footer-social">
                <h3>
                    <img src="{{ asset('assets/1.png') }}" alt="Logo Sekolah" class="footer-logo"> 
                    Media Sosial SD Bawamai Pontianak
                </h3>
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

    <script src="{{ asset('script.js') }}"></script> 
</body>
</html>