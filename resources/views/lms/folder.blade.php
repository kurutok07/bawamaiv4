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
        /* Menggunakan logic warna yang sama dengan Dashboard */
        .fitur-card:nth-child(6n+1) .fitur-card-icon-bg { background-color: #e4f5e9; }
        .fitur-card:nth-child(6n+2) .fitur-card-icon-bg { background-color: #fff9c4; }
        .fitur-card:nth-child(6n+3) .fitur-card-icon-bg { background-color: #e3f2fd; }
        .fitur-card:nth-child(6n+4) .fitur-card-icon-bg { background-color: #ffe0b2; }
        .fitur-card:nth-child(6n+5) .fitur-card-icon-bg { background-color: #f3e5f5; }
        .fitur-card:nth-child(6n+6) .fitur-card-icon-bg { background-color: #ffebee; }

        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            color: #0d47a1;
            font-weight: 700;
            text-decoration: none;
        }
        .back-btn:hover { text-decoration: underline; }
    </style>
</head>
<body class="dashboard-page">

    @include('components.navbar') {{-- Nanti kita buat component navbar biar tidak copy-paste terus --}}

    <section class="hero-section" style="height: 300px;">
        <div class="container hero-content">
            <div class="hero-text">
                <span>KATEGORI</span>
                <h1>{{ $item->title }}</h1>
            </div>
            <div class="hero-logo-wrapper">
                 @if($item->cover_image)
                    <img src="{{ asset($item->cover_image) }}" alt="{{ $item->title }}" class="hero-logo">
                @endif
            </div>
        </div>
    </section>

    <main class="container" id="fitur">
        <br>
        {{-- Tombol Kembali logic --}}
        <a href="{{ $item->parent_id ? route('lms.show', $item->parent->slug) : route('landing') }}" class="back-btn">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>

        <h2 class="section-title">DAFTAR MATERI</h2>
        
        <div class="fitur-wrapper">
            @forelse($children as $child)
                <a href="{{ route('lms.show', $child->slug) }}" class="fitur-card show">
                    <div class="fitur-card-icon-bg">
                        {{-- Icon Logic: Folder vs File --}}
                        @if($child->cover_image)
                            <img src="{{ asset($child->cover_image) }}" alt="{{ $child->title }}">
                        @elseif($child->type == 'folder')
                            <i class="fas fa-folder fa-3x" style="color: #FFB74D;"></i>
                        @elseif($child->type == 'video')
                            <i class="fas fa-play-circle fa-3x" style="color: #E53935;"></i>
                        @else
                            <i class="fas fa-file-pdf fa-3x" style="color: #1E88E5;"></i>
                        @endif
                    </div>
                    <h4>{{ $child->title }}</h4>
                </a>
            @empty
                <div style="grid-column: 1/-1; text-align: center; padding: 50px; color: #777;">
                    <i class="fas fa-folder-open fa-3x" style="margin-bottom: 15px; opacity: 0.5;"></i>
                    <p>Belum ada materi di dalam folder ini.</p>
                </div>
            @endforelse
        </div>
    </main>

    {{-- Footer sama seperti welcome --}}
    <script src="{{ asset('script.js') }}"></script> 
</body>
</html>