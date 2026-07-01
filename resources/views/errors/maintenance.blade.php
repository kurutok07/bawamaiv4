<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sedang Dalam Perbaikan</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f3f4f6;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #374151;
            text-align: center;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            max-width: 500px;
            width: 90%;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #111827;
        }
        p {
            font-size: 16px;
            color: #6b7280;
            line-height: 1.5;
            margin-bottom: 20px;
        }
        .brand {
            font-weight: bold;
            color: #3b82f6; /* Warna Primary Biru */
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        /* Animasi Gear Berputar */
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .gear-spin {
            transform-origin: center;
            animation: spin 4s linear infinite;
        }
    </style>
</head>
<body>

    <div class="container">
        <svg width="250" height="200" viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-bottom: 20px;">
            <circle cx="200" cy="150" r="120" fill="#EBF5FF"/>
            
            <rect x="120" y="80" width="160" height="140" rx="8" fill="#FFFFFF" stroke="#3B82F6" stroke-width="4"/>
            <line x1="140" y1="110" x2="260" y2="110" stroke="#E5E7EB" stroke-width="4" stroke-linecap="round"/>
            <line x1="140" y1="140" x2="260" y2="140" stroke="#E5E7EB" stroke-width="4" stroke-linecap="round"/>
            <line x1="140" y1="170" x2="260" y2="170" stroke="#E5E7EB" stroke-width="4" stroke-linecap="round"/>
            
            <circle cx="250" cy="95" r="4" fill="#10B981"/>
            <circle cx="235" cy="95" r="4" fill="#E5E7EB"/>
            
            <path d="M260 240 C260 240 230 200 200 200 C170 200 140 240 140 240" stroke="#1F2937" stroke-width="4" stroke-linecap="round" fill="#374151"/>
            <circle cx="200" cy="170" r="30" fill="#FFD166" stroke="#1F2937" stroke-width="3"/>
            <path d="M170 170 C170 140 230 140 230 170" fill="#F59E0B" stroke="#1F2937" stroke-width="3"/>
            
            <g class="gear-spin" style="transform-box: fill-box;">
                <path d="M330 80 L335 70 L345 70 L350 80 L360 82 L368 75 L375 80 L370 90 L372 100 L382 105 L380 115 L370 118 L365 128 L355 135 L345 130 L335 125 L325 115 L330 105 Z" fill="#9CA3AF" opacity="0.8"/>
                <circle cx="350" cy="100" r="8" fill="white"/>
            </g>
            
            <g class="gear-spin" style="animation-direction: reverse; transform-box: fill-box; animation-duration: 6s;">
                <path d="M60 180 L65 170 L75 170 L80 180 L90 182 L98 175 L105 180 L100 190 L102 200 L112 205 L110 215 L100 218 L95 228 L85 235 L75 230 L65 225 L55 215 L60 205 Z" fill="#60A5FA" opacity="0.8"/>
                <circle cx="80" cy="200" r="8" fill="white"/>
            </g>
        </svg>

        <h1>Maaf, Website Sedang Dalam Perbaikan</h1>
        <p>
            Kami sedang melakukan pemeliharaan sistem untuk meningkatkan performa dan layanan. 
            Silakan kembali lagi beberapa saat lagi.
        </p>
        <div class="brand">&mdash; Tim IT Sekolah &mdash;</div>
    </div>

</body>
</html>