<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $item->title }} - LMS SD Bawamai</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"> 
    <link rel="icon" type="image/png" href="{{ asset('assets/1.png') }}">

    <style>
        /* Style Khusus Halaman Konten */
        .content-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            padding: 30px;
            margin-top: -50px; /* Overlap ke Hero Section sedikit */
            position: relative;
            z-index: 10;
        }

        .video-wrapper {
            position: relative;
            padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
            height: 0;
            overflow: hidden;
            border-radius: 15px;
            background: #000;
        }
        .video-wrapper iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        .pdf-wrapper {
            width: 100%;
            height: 800px;
            border-radius: 15px;
            border: 2px solid #eee;
        }

        .back-btn {
            color: white; /* Karena di hero section biru */
            margin-bottom: 10px;
            display: inline-block;
            font-weight: bold;
            text-decoration: none;
        }
        /* Style Navigasi Next/Prev */
        .nav-btn {
            display: flex;
            align-items: center;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 10px;
            transition: all 0.3s ease;
            max-width: 45%;
        }

        .nav-text {
            display: flex;
            flex-direction: column;
            margin: 0 10px;
        }

        .nav-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #888;
            font-weight: 700;
        }

        .nav-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #333;
        }

        .prev-btn {
            background-color: #f8f9fa;
            border: 1px solid #eee;
            color: #555;
        }
        .prev-btn:hover {
            background-color: #e2e6ea;
            transform: translateX(-5px); /* Geser kiri dikit pas hover */
        }

        .next-btn {
            background-color: #0d47a1;
            color: white !important; /* Force white text */
            text-align: right;
            justify-content: flex-end;
        }
        .next-btn .nav-label { color: #bbdefb; } /* Label warna biru muda */
        .next-btn .nav-title { color: white; }
        
        .next-btn:hover {
            background-color: #1565c0;
            transform: translateX(5px); /* Geser kanan dikit pas hover */
            box-shadow: 0 4px 10px rgba(13, 71, 161, 0.3);
        }

        .finish-btn {
            background-color: #10b981; /* Hijau */
            color: white !important;
            text-align: right;
        }
        .finish-btn .nav-label { color: #d1fae5; }
        .finish-btn .nav-title { color: white; }
        .finish-btn:hover { background-color: #059669; }

        /* Responsive HP */
        @media (max-width: 500px) {
            .nav-label { display: none; } /* Hilangkan label 'Selanjutnya' di HP biar muat */
            .nav-title { font-size: 0.8rem; }
            .nav-btn { padding: 10px; }
        }
    </style>
</head>
<body class="dashboard-page">

    {{-- Navbar bisa dicopy dari welcome.blade.php atau dibuat component --}}
    
    <section class="hero-section" style="height: 170px; align-items: flex-start; padding-top: 40px;">
        <div class="container">
            <h1 style="color: #0d47a1; font-size: 2rem; margin-top: 10px;">{{ $item->title }}</h1>
        </div>
    </section>

    <main class="container">
        <div class="content-container">
            <button 
                onclick="window.location.href='{{ route('lms.show', $item->parent->slug) }}'" 
                class="btn back-btn"
                style="
                    position: relative; 
                    z-index: 999; /* Agar berada di atas konten putih */
                    background: rgba(110, 110, 110, 1); /* Efek kaca transparan */
                    color: white; 
                    border: 1px solid white;
                    border-radius: 30px;
                    padding: 8px 20px;
                    font-weight: bold;
                    cursor: pointer;
                ">
                <i class="fas fa-arrow-left"></i> Keluar dari materi
            </button>
            
            {{-- LOGIC TAMPILAN KONTEN --}}
            
            @if($item->type == 'video')
                <div class="video-wrapper">
                    {{-- Pastikan link database berupa embed URL (bukan watch URL) --}}
                    <iframe src="{{ $item->content }}" title="YouTube video player" allowfullscreen></iframe>
                </div>
            
            @elseif($item->type == 'file')
                {{-- Tampilkan PDF --}}
                <embed src="{{ asset($item->content) }}" type="application/pdf" class="pdf-wrapper" />
                
                <div style="margin-top: 20px; text-align: right;">
                    <a href="{{ asset($item->content) }}" target="_blank" class="login-btn" style="background: #0d47a1; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none;">
                        <i class="fas fa-download"></i> Download File
                    </a>
                </div>

            @elseif($item->type == 'link')
                 <div style="text-align: center; padding: 50px;">
                    <h3>Materi ini adalah Link Eksternal</h3>
                    <p>Silakan klik tombol di bawah untuk membuka.</p>
                    <br>
                    <a href="{{ $item->content }}" target="_blank" class="login-btn" style="padding: 15px 30px; background: #0d47a1; color: white; border-radius: 30px; text-decoration: none;">
                        Buka Tautan <i class="fas fa-external-link-alt"></i>
                    </a>
                 </div>
            @endif
        <hr style="margin-top: 30px; margin-bottom: 20px; border-top: 1px solid #eee;">

            <div class="navigation-wrapper" style="display: flex; justify-content: space-between; align-items: center;">
                
                {{-- TOMBOL SEBELUMNYA (PREVIOUS) --}}
                @if($prevItem)
                    <a href="{{ route('lms.show', $prevItem->slug) }}" class="nav-btn prev-btn">
                        <i class="fas fa-chevron-left"></i> 
                        <div class="nav-text">
                            <span class="nav-label">Sebelumnya</span>
                            <span class="nav-title">{{ Str::limit($prevItem->title, 20) }}</span>
                        </div>
                    </a>
                @else
                    {{-- Spacer kosong agar tombol Next tetap di kanan jika tidak ada Previous --}}
                    <div></div> 
                @endif

                {{-- TOMBOL SELANJUTNYA (NEXT) --}}
                @if($nextItem)
                    <a href="{{ route('lms.show', $nextItem->slug) }}" class="nav-btn next-btn">
                        <div class="nav-text">
                            <span class="nav-label">Selanjutnya</span>
                            <span class="nav-title">{{ Str::limit($nextItem->title, 20) }}</span>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                @else
                    {{-- Jika sudah materi terakhir, tampilkan tombol kembali ke menu --}}
                    <a href="{{ route('lms.show', $item->parent->slug) }}" class="nav-btn finish-btn">
                        <div class="nav-text">
                            <span class="nav-label">Selesai</span>
                            <span class="nav-title">Kembali ke Menu</span>
                        </div>
                        <i class="fas fa-check"></i>
                    </a>
                @endif
            </div>
            {{-- SELESAI BAGIAN TOMBOL NAVIGASI --}}

        </div> {{-- Tutup .content-container --}}
        </div>
        
    </main>
    <br><br>

    {{-- Footer --}}
</body>
</html>