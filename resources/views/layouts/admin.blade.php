<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - LMS Bawamai</title> <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* --- RESET & VARIABLES --- */
        :root {
            --primary-bg: #4b95d2d6;
            --card-bg: #ffffff;
            --text-main: #1f2937;
            --text-muted: #070707ff;
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