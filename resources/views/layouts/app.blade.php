<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #198754;
            --primary-hover: #157347;
            --primary-soft: #eaf6ef;
            --success: #198754;
            --danger: #dc3545;
            --warning: #b7791f;
            --info: #0d6efd;
            --ink: #111827;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --border: #e5e7eb;
            --background: #f8fafc;
            --panel: #ffffff;
            --sidebar-width: 260px;
            --radius: 12px;
            --shadow-subtle: 0 1px 2px rgba(17, 24, 39, 0.05);
            --shadow-soft: 0 8px 24px rgba(17, 24, 39, 0.08);
            --bs-primary: var(--primary);
            --bs-primary-rgb: 25, 135, 84;
            --bs-success: var(--success);
            --bs-success-rgb: 25, 135, 84;
            --bs-danger: var(--danger);
            --bs-danger-rgb: 220, 53, 69;
        }

        * {
            letter-spacing: 0;
        }

        body {
            min-height: 100vh;
            margin: 0;
            color: var(--text-main);
            background: var(--background);
            font-family: 'Inter', sans-serif;
        }

        .sidebar {
            width: var(--sidebar-width);
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 1050;
            display: flex;
            flex-direction: column;
            background: var(--panel);
            border-right: 1px solid var(--border);
            transition: transform 0.24s ease;
        }

        .sidebar-brand {
            padding: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 1rem;
            font-weight: 800;
            color: var(--ink);
            border-bottom: 1px solid var(--border);
        }

        .sidebar-brand div {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-brand i.fa-wallet {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            background: var(--primary-soft);
        }

        .sidebar-menu {
            padding: 0.85rem;
            flex: 1;
        }

        .nav-link {
            padding: 0.72rem 0.85rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 0.72rem;
            border-radius: 10px;
            margin-bottom: 0.18rem;
            font-weight: 600;
            transition: background 0.16s ease, color 0.16s ease;
        }

        .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 0.95rem;
        }

        .nav-link:hover {
            color: var(--ink);
            background: #f3f4f6;
        }

        .nav-link.active {
            color: var(--primary);
            background: var(--primary-soft);
        }

        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid var(--border);
            background: #fbfbfc;
        }

        .avatar {
            background: var(--primary) !important;
        }

        .mobile-navbar {
            display: none;
            position: sticky;
            top: 0;
            z-index: 1040;
            padding: 0.85rem 1rem;
            color: var(--ink);
            background: var(--panel);
            border-bottom: 1px solid var(--border);
            justify-content: space-between;
            align-items: center;
        }

        .main-content {
            min-height: 100vh;
            margin-left: var(--sidebar-width);
            padding: 1.5rem;
            width: calc(100% - var(--sidebar-width));
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(17, 24, 39, 0.32);
            z-index: 1045;
        }

        .header-section {
            margin-bottom: 1.25rem;
            padding-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 1rem;
            border-bottom: 1px solid var(--border);
        }

        .header-title h1 {
            margin: 0;
            color: var(--ink);
            font-size: clamp(1.35rem, 2vw, 1.85rem);
            font-weight: 800;
        }

        .header-title p {
            margin: 0.28rem 0 0;
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .card {
            border: 1px solid var(--border) !important;
            border-radius: var(--radius);
            margin-bottom: 1.25rem;
            overflow: hidden;
            background: var(--panel) !important;
            box-shadow: var(--shadow-subtle) !important;
        }

        .card:hover {
            box-shadow: var(--shadow-soft) !important;
        }

        .card-header {
            background: var(--panel) !important;
            border-bottom: 1px solid var(--border) !important;
            padding: 1rem 1.25rem;
            font-weight: 700;
        }

        .btn {
            font-weight: 600;
            border-radius: 10px;
        }

        .btn-primary {
            color: #fff;
            background: var(--primary);
            border-color: var(--primary);
            padding: 0.55rem 1rem;
            box-shadow: none;
        }

        .btn-primary:hover {
            color: #fff;
            background: var(--primary-hover);
            border-color: var(--primary-hover);
        }

        .btn-light {
            background: #f9fafb;
            border-color: var(--border);
            color: var(--text-main);
        }

        .form-control,
        .form-select {
            min-height: 40px;
            border-color: #d1d5db;
            border-radius: 10px;
            color: var(--text-main);
            background-color: #ffffff;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.12);
        }

        .form-label {
            color: var(--text-muted);
            font-weight: 700;
            letter-spacing: 0.03em;
        }

        .table {
            color: var(--text-main);
        }

        .table > :not(caption) > * > * {
            padding-top: 0.9rem;
            padding-bottom: 0.9rem;
            border-bottom-color: var(--border);
        }

        .table thead th {
            color: var(--text-muted);
            font-size: 0.74rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            background: #f9fafb !important;
        }

        .table-hover > tbody > tr:hover > * {
            background-color: #f9fafb;
        }

        .badge {
            font-weight: 700;
        }

        .progress {
            background-color: #eef2f7 !important;
        }

        .modal-content {
            border: 1px solid var(--border) !important;
            border-radius: 14px;
            background: #ffffff;
            box-shadow: var(--shadow-soft) !important;
        }

        .alert {
            border-radius: 12px;
            box-shadow: none;
        }

        .bg-light {
            background-color: #f9fafb !important;
        }

        .bg-primary {
            background: var(--primary) !important;
        }

        .text-primary {
            color: var(--primary) !important;
        }

        .text-success {
            color: var(--success) !important;
        }

        .text-danger {
            color: var(--danger) !important;
        }

        .text-warning {
            color: var(--warning) !important;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .sidebar-overlay.show {
                display: block;
            }

            .mobile-navbar {
                display: flex;
            }

            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 1rem;
            }

            .header-section {
                flex-direction: column;
                align-items: flex-start;
            }

            .header-action,
            .header-action form {
                width: 100%;
            }

            .header-action {
                flex-wrap: wrap;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="mobile-navbar">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-wallet text-primary"></i>
            <span class="fw-bold">MyFinance</span>
        </div>
        <button class="btn p-0 text-primary" id="sidebarToggle" type="button" aria-label="Buka menu">
            <i class="fas fa-bars fa-lg"></i>
        </button>
    </div>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="sidebar" id="mainSidebar">
        <div class="sidebar-brand">
            <div>
                <i class="fas fa-wallet"></i>
                <span>MyFinance</span>
            </div>
            <button class="btn p-0 text-muted d-lg-none" id="sidebarClose" type="button" aria-label="Tutup menu">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <nav class="sidebar-menu">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('transactions.index') }}" class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                <i class="fas fa-exchange-alt"></i>
                <span>Transaksi</span>
            </a>
            <a href="{{ route('analytics') }}" class="nav-link {{ request()->routeIs('analytics') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i>
                <span>Analisa</span>
            </a>
            <a href="{{ route('accounts.index') }}" class="nav-link {{ request()->routeIs('accounts.*') ? 'active' : '' }}">
                <i class="fas fa-university"></i>
                <span>Akun Saya</span>
            </a>
            <a href="{{ route('budgets.index') }}" class="nav-link {{ request()->routeIs('budgets.*') ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i>
                <span>Anggaran</span>
            </a>
            <a href="{{ route('goals.index') }}" class="nav-link {{ request()->routeIs('goals.*') ? 'active' : '' }}">
                <i class="fas fa-bullseye"></i>
                <span>Target</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="avatar text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; font-size: 0.82rem;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="user-info overflow-hidden">
                    <div class="text-truncate" style="font-size: 0.9rem; font-weight: 700;">{{ auth()->user()->name }}</div>
                    <div class="text-muted text-truncate" style="font-size: 0.75rem;">{{ auth()->user()->email }}</div>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <main class="main-content">
        @if(session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('mainSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const toggle = document.getElementById('sidebarToggle');
            const close = document.getElementById('sidebarClose');

            function toggleSidebar() {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            }

            if (toggle) toggle.addEventListener('click', toggleSidebar);
            if (close) close.addEventListener('click', toggleSidebar);
            if (overlay) overlay.addEventListener('click', toggleSidebar);
        });
    </script>
    @yield('scripts')
</body>
</html>
