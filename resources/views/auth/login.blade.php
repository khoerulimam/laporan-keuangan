<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            color-scheme: light;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            --primary: #198754;
            --primary-hover: #157347;
            --primary-soft: #eaf6ef;
            --ink: #111827;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --border: #e5e7eb;
            --background: #f8fafc;
        }

        * {
            box-sizing: border-box;
            letter-spacing: 0;
        }

        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            padding: 24px;
            color: var(--text-main);
            background: var(--background);
        }

        .auth-card {
            width: min(420px, 100%);
            padding: 32px;
            border: 1px solid var(--border);
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 8px 24px rgba(17, 24, 39, 0.06);
        }

        .brand {
            margin-bottom: 28px;
        }

        .logo-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
            color: var(--ink);
            font-weight: 800;
        }

        .logo {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            background: var(--primary-soft);
            font-weight: 800;
        }

        h1 {
            margin: 0;
            color: var(--ink);
            font-size: 1.55rem;
            font-weight: 800;
        }

        .subtitle {
            margin: 8px 0 0;
            color: var(--text-muted);
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 16px;
            display: grid;
            gap: 7px;
        }

        label {
            color: var(--text-muted);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }

        input {
            width: 100%;
            min-height: 44px;
            padding: 11px 13px;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            background: #ffffff;
            color: var(--text-main);
            font: inherit;
            outline: 0;
            transition: border-color 0.16s, box-shadow 0.16s;
        }

        input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(25, 135, 84, 0.12);
        }

        input::placeholder {
            color: #9ca3af;
        }

        .remember-group {
            display: flex;
            align-items: center;
            gap: 9px;
            margin: 2px 0 22px;
        }

        input[type="checkbox"] {
            width: 17px;
            min-height: 17px;
            accent-color: var(--primary);
            cursor: pointer;
        }

        .remember-label {
            color: var(--text-muted);
            font-size: 14px;
            text-transform: none;
            letter-spacing: 0;
            cursor: pointer;
        }

        .btn {
            width: 100%;
            min-height: 44px;
            border: 0;
            border-radius: 10px;
            color: #ffffff;
            background: var(--primary);
            font: inherit;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.16s;
        }

        .btn:hover {
            background: var(--primary-hover);
        }

        .errors,
        .status,
        .demo-hint {
            border-radius: 10px;
            padding: 12px 13px;
            margin-bottom: 16px;
            font-size: 14px;
            line-height: 1.5;
        }

        .errors {
            color: #991b1b;
            background: #fef2f2;
            border: 1px solid #fecaca;
        }

        .status {
            color: #166534;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
        }

        code {
            color: var(--ink);
            background: #eef2f7;
            padding: 2px 6px;
            border-radius: 6px;
        }

        @media (max-width: 480px) {
            body {
                padding: 16px;
            }

            .auth-card {
                padding: 24px;
            }
        }
    </style>
</head>
<body>
    <main class="auth-card">
        <div class="brand">
            <div class="logo-row">
                <div class="logo">LK</div>
                <span>MyFinance</span>
            </div>
            <h1>Masuk</h1>
            <p class="subtitle">Lanjutkan ke dashboard keuangan Anda.</p>
        </div>

        @if (session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="errors">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="masukkan@email.com" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="password" required>
            </div>

            <div class="remember-group">
                <input type="checkbox" id="remember" name="remember" value="1">
                <label for="remember" class="remember-label">Ingat saya</label>
            </div>

            <button type="submit" class="btn">Masuk</button>
        </form>

    </main>
</body>
</html>
