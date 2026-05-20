<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .hero-card {
            background: white;
            padding: 3rem;
            border-radius: 2rem;
            box-shadow: 0 20px 50px rgba(0,0,0,0.05);
            max-width: 500px;
            width: 100%;
            text-align: center;
        }
        .logo-icon {
            width: 64px;
            height: 64px;
            background-color: #4f46e5;
            color: white;
            border-radius: 1.25rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 1.5rem;
        }
        h1 {
            font-weight: 800;
            letter-spacing: -0.025em;
            margin-bottom: 1rem;
        }
        p {
            color: #64748b;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .btn-primary {
            background-color: #4f46e5;
            border-color: #4f46e5;
            padding: 0.75rem 2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background-color: #4338ca;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.2);
        }
    </style>
</head>
<body>
    <div class="hero-card">
        <div class="logo-icon">
            <i class="fas fa-wallet"></i>
        </div>
        <h1>{{ config('app.name') }}</h1>
        <p>Kelola keuangan pribadi Anda dengan mudah, modern, dan informatif. Pantau arus kas, anggaran, dan target tabungan dalam satu tempat.</p>
        
        @auth
            <a href="{{ route('dashboard') }}" class="btn btn-primary w-100 mb-3">Ke Dashboard</a>
        @else
            <a href="{{ route('login') }}" class="btn btn-primary w-100 mb-3">Masuk ke Akun</a>
            <div class="small text-muted">
                Belum punya akun? <span class="text-primary fw-medium">Hubungi Admin</span>
            </div>
        @endauth
    </div>
</body>
</html>
