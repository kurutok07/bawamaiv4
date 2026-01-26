<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Sistem - SD Bawamai</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); /* Gradient Biru Laravel/Bootstrap */
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .card-header-custom {
            background-color: white;
            padding-top: 2rem;
            text-align: center;
            border-bottom: none;
        }

        .logo-img {
            width: 80px;
            height: auto;
            margin-bottom: 10px;
        }

        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #dee2e6;
            background-color: #f8f9fa;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #0d6efd;
            background-color: #fff;
        }

        .btn-login {
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            font-size: 16px;
            background-color: #0d6efd;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background-color: #0b5ed7;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
        }

        .input-group-text {
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-right: none;
            color: #6c757d;
        }

        .input-with-icon {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            border-left: none;
        }
        
        .input-with-icon:focus {
            border-left: 1px solid #0d6efd; 
        }

        .login-title {
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }
        
        .login-subtitle {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <div class="card login-card p-3">
        <div class="card-header-custom">
            <img src="{{ asset('assets/1.png') }}" alt="Logo Sekolah" class="logo-img">
            <h4 class="login-title">Selamat Datang</h4>
            <p class="login-subtitle">Silakan masuk ke akun Anda</p>
        </div>
        
        <div class="card-body">
            @error('username')
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <div>Username atau Password salah!</div>
                </div>
            @enderror

            <form action="/login" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="form-label fw-bold small text-secondary">USERNAME</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" 
                               name="username" 
                               class="form-control input-with-icon" 
                               placeholder="Masukkan username" 
                               value="{{ old('username') }}" 
                               required 
                               autofocus>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-secondary">PASSWORD</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" 
                               name="password" 
                               class="form-control input-with-icon" 
                               placeholder="Masukkan password" 
                               required>
                    </div>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-login text-white">
                        MASUK SEKARANG <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>

                <div class="text-center mt-4">
                    <small class="text-muted">&copy; {{ date('Y') }} SD Bawamai Pontianak</small>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>