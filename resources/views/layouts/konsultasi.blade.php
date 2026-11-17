<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Bonamu</title>

    {{-- Bootstrap 5 CSS & Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    {{-- Google Fonts: Plus Jakarta Sans & JetBrains Mono --}}
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    {{-- Tailwind CSS CDN untuk modul Konsultasi --}}
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        "primary": "#005954",
                        "primary-hover": "#004743",
                        "primary-container": "#005954",
                        "on-primary": "#ffffff",
                        "secondary": "#005954",
                        "secondary-container": "#d1f4ed",
                        "secondary-fixed": "#bbf0e1",
                        "surface-variant": "#e2e8f0",
                        "tertiary-fixed": "#d4f4ea",
                        "surface-bright": "#f8fafc",
                        "on-surface": "#1e293b",
                        "on-surface-variant": "#475569",
                        "surface-container-lowest": "#ffffff",
                        "surface-container-low": "#f8fafc",
                        "surface-container": "#f1f5f9",
                        "surface-container-highest": "#e2e8f0",
                        "outline": "#64748b",
                        "outline-variant": "#cbd5e1",
                        "error": "#dc2626",
                        "error-container": "#fee2e2",
                        "on-error-container": "#991b1b"
                    },
                    borderRadius: { DEFAULT: "0.5rem", lg: "0.75rem", xl: "1rem", "2xl": "1.25rem", full: "9999px" },
                    spacing: { "space-md": "1rem", "margin-desktop": "2.5rem", "space-sm": "0.5rem", "space-xs": "0.25rem" },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        "headline-lg": ['"Plus Jakarta Sans"', 'sans-serif'],
                        "headline-md": ['"Plus Jakarta Sans"', 'sans-serif'],
                        "headline-sm": ['"Plus Jakarta Sans"', 'sans-serif'],
                        "label-uppercase": ['"Plus Jakarta Sans"', 'sans-serif'],
                        "body-default": ['"Plus Jakarta Sans"', 'sans-serif'],
                        "body-semibold": ['"Plus Jakarta Sans"', 'sans-serif'],
                        "body-sm": ['"Plus Jakarta Sans"', 'sans-serif'],
                        "badge-status": ['"Plus Jakarta Sans"', 'sans-serif']
                    },
                    fontSize: {
                        "headline-lg": ["28px", { lineHeight: "36px", letterSpacing: "-0.02em", fontWeight: "700" }],
                        "headline-md": ["22px", { lineHeight: "28px", letterSpacing: "-0.01em", fontWeight: "600" }],
                        "headline-sm": ["18px", { lineHeight: "24px", fontWeight: "600" }],
                        "label-uppercase": ["11px", { lineHeight: "14px", letterSpacing: "0.08em", fontWeight: "700" }],
                        "body-default": ["14px", { lineHeight: "20px", fontWeight: "400" }],
                        "body-semibold": ["14px", { lineHeight: "20px", fontWeight: "600" }],
                        "body-sm": ["12px", { lineHeight: "18px", fontWeight: "400" }],
                        "badge-status": ["11px", { lineHeight: "14px", fontWeight: "600" }]
                    }
                }
            }
        }
    </script>
    <style>
        :root {
            --app-primary: #005954;
            --app-primary-hover: #004743;
            --app-primary-light: #d1f4ed;
            --app-bg: #f5f8fa;
            --card-bg: #ffffff;
            --text-color: #2d3748;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--app-bg);
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            vertical-align: middle;
            line-height: 1;
        }
        .font-mono-code { font-family: 'JetBrains Mono', monospace; }

        /* Header Style Mirip layouts/app.blade.php */
        .app-header {
            background: #ffffff;
            border-bottom: 1px solid #e9ecef;
            padding: 0.75rem 0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }

        .brand-logo-icon {
            width: 36px;
            height: 36px;
            background-color: var(--app-primary);
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.1rem;
        }

        .nav-pill-item {
            color: #4a5568;
            font-size: 0.85rem;
            font-weight: 500;
            padding: 0.45rem 0.85rem;
            border-radius: 50rem;
            text-decoration: none;
            transition: all 0.2s ease;
            white-space: nowrap !important;
            display: inline-flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
            gap: 0.45rem !important;
            line-height: 1.2 !important;
            word-break: keep-all !important;
        }

        .nav-pill-item span {
            white-space: nowrap !important;
            display: inline !important;
        }

        .nav-pill-item i {
            font-size: 1rem;
            line-height: 1;
            display: inline-block;
            vertical-align: middle;
        }

        .nav-pill-item:hover {
            color: var(--app-primary);
            background-color: #f1f5f9;
        }

        .nav-pill-item.active {
            background-color: var(--app-primary-light);
            color: var(--app-primary);
            font-weight: 600;
        }

        .user-badge {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 50rem;
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .admin-pill {
            background-color: #d1f4ed;
            color: #005954;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.15rem 0.5rem;
            border-radius: 50rem;
            letter-spacing: 0.05em;
        }

        .btn-keluar {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 50rem;
            padding: 0.3rem 1rem;
            font-size: 0.85rem;
            color: #4a5568;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-keluar:hover {
            background: #f8fafc;
            color: #e53e3e;
            border-color: #fecaca;
        }

        .app-footer {
            margin-top: auto;
            padding: 1.5rem 0;
            font-size: 0.8rem;
            color: #94a3b8;
            border-top: 1px solid #e9ecef;
            background: #ffffff;
        }

        body.dark-mode {
            --app-bg: #121820;
            --card-bg: #1a222d;
            --text-color: #e2e8f0;
            background-color: var(--app-bg) !important;
            color: var(--text-color) !important;
        }

        body.dark-mode .app-header,
        body.dark-mode .app-footer {
            background-color: var(--card-bg) !important;
            border-color: #2d3748 !important;
            color: var(--text-color) !important;
        }
    </style>
    @stack('styles')
</head>
<body class="antialiased flex flex-col min-h-screen">

{{-- Header terpadu sesuai standar Bonamu --}}
<header class="app-header">
    <div class="container-xl d-flex align-items-center justify-content-between">
        {{-- Logo Brand --}}
        <a href="{{ url('/') }}" class="d-flex align-items-center gap-3 text-decoration-none flex-shrink-0">
            <div class="brand-logo-icon">
                <i class="bi bi-hospital"></i>
            </div>
            <div>
                <div class="fw-bold text-dark lh-sm" style="font-size: 1.15rem; letter-spacing: -0.02em;">Bonamu</div>
                <div class="text-muted" style="font-size: 0.75rem;">Manajemen antrean &amp; informasi layanan</div>
            </div>
        </a>

        {{-- Navigasi Menu Dinamis Sesuai Role --}}
        <nav class="d-none d-md-flex align-items-center gap-1 gap-lg-2 flex-nowrap">
            @auth
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="nav-pill-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-fill"></i> <span>Dashboard</span>
                    </a>
                    <a href="{{ route('admin.antrean.index') }}" class="nav-pill-item {{ request()->routeIs('admin.antrean.*') ? 'active' : '' }}">
                        <i class="bi bi-ticket-detailed"></i> <span>Antrean Pasien</span>
                    </a>
                    <a href="{{ route('admin.poli.index') }}" class="nav-pill-item {{ request()->routeIs('admin.poli.*') ? 'active' : '' }}">
                        <i class="bi bi-diagram-3"></i> <span>Data Poli</span>
                    </a>
                    <a href="{{ route('admin.jadwal.index') }}" class="nav-pill-item {{ request()->routeIs('admin.jadwal.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-week"></i> <span>Jadwal Dokter</span>
                    </a>
                    <a href="{{ route('admin.layanan.index') }}" class="nav-pill-item {{ request()->routeIs('admin.layanan.*') ? 'active' : '' }}">
                        <i class="bi bi-tag"></i> <span>Layanan &amp; Tarif</span>
                    </a>
                    <a href="{{ route('admin.akun.index') }}" class="nav-pill-item {{ request()->routeIs('admin.akun.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> <span>Akun Staf</span>
                    </a>
                @elseif(auth()->user()->role === 'pasien')
                    <a href="{{ route('pasien.dashboard') }}" class="nav-pill-item {{ request()->routeIs('pasien.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-fill"></i> <span>Dashboard</span>
                    </a>
                    <a href="{{ route('antrean.create') }}" class="nav-pill-item {{ request()->routeIs('antrean.create') ? 'active' : '' }}">
                        <i class="bi bi-ticket-perforated"></i> <span>Ambil Antrean</span>
                    </a>
                    <a href="{{ route('antrean.index') }}" class="nav-pill-item {{ request()->routeIs('antrean.index') || request()->routeIs('antrean.show') ? 'active' : '' }}">
                        <i class="bi bi-card-checklist"></i> <span>Antrean Saya</span>
                    </a>
                    <a href="{{ route('pasien.profil.edit') }}" class="nav-pill-item {{ request()->routeIs('pasien.profil.*') ? 'active' : '' }}">
                        <i class="bi bi-person-lines-fill"></i> <span>Profil Pasien</span>
                    </a>
                    <a href="{{ route('konsultasi.create') }}" class="nav-pill-item active">
                        <i class="bi bi-chat-dots"></i> <span>Konsultasi</span>
                    </a>
                @elseif(auth()->user()->role === 'dokter')
                    <a href="{{ route('dokter.konsultasi.index') }}" class="nav-pill-item active">
                        <i class="bi bi-clipboard2-pulse"></i> <span>Konsultasi Masuk</span>
                    </a>
                @endif
            @else
                <a href="{{ url('/') }}" class="nav-pill-item {{ request()->is('/') ? 'active' : '' }}">Portal Testing</a>
                <a href="{{ route('login') }}" class="nav-pill-item {{ request()->routeIs('login') ? 'active' : '' }}">Login</a>
                <a href="{{ route('register') }}" class="nav-pill-item {{ request()->routeIs('register') ? 'active' : '' }}">Register Pasien</a>
            @endauth
        </nav>

        {{-- Profil & Action --}}
        <div class="d-flex align-items-center gap-2 flex-shrink-0">
            {{-- Quick Role Switcher untuk Testing --}}
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle rounded-pill px-2 px-md-3" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Ganti Role Cepat">
                    <i class="bi bi-arrow-repeat me-1 text-primary"></i> <span class="d-none d-sm-inline">Switch Role</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="font-size: 0.85rem; min-width: 240px;">
                    <li><h6 class="dropdown-header text-uppercase fw-bold text-primary">1-Click Test Login</h6></li>
                    <li>
                        <a class="dropdown-item py-2 {{ auth()->check() && auth()->user()->role === 'admin' ? 'active bg-light text-dark fw-bold' : '' }}" href="{{ route('dev.login-as', 'admin') }}">
                            👨‍💼 <strong>Admin</strong> <small class="text-muted d-block">admin@test.com</small>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2 {{ auth()->check() && auth()->user()->role === 'dokter' ? 'active bg-light text-dark fw-bold' : '' }}" href="{{ route('dev.login-as', 'dokter') }}">
                            👨‍⚕️ <strong>Dokter (dr. Andi)</strong> <small class="text-muted d-block">dokter.andi@test.com</small>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2 {{ auth()->check() && auth()->user()->role === 'pasien' ? 'active bg-light text-dark fw-bold' : '' }}" href="{{ route('dev.login-as', 'pasien') }}">
                            🧑‍🦽 <strong>Pasien (Budi)</strong> <small class="text-muted d-block">pasien@test.com</small>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item py-2 text-primary" href="{{ url('/') }}">
                            <i class="bi bi-grid-fill me-1"></i> <strong>Kembali ke Portal Testing</strong>
                        </a>
                    </li>
                </ul>
            </div>

            <button type="button" id="btnDarkMode" class="btn btn-sm btn-light rounded-circle text-muted" title="Mode gelap/terang">
                <i class="bi bi-moon"></i>
            </button>

            @auth
                <div class="user-badge">
                    <i class="bi bi-person-circle text-secondary"></i>
                    <span class="d-none d-sm-inline text-dark">{{ auth()->user()->name }}</span>
                    <span class="admin-pill">{{ strtoupper(auth()->user()->role) }}</span>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn-keluar" title="Keluar dari akun">
                        <i class="bi bi-box-arrow-right me-1"></i> Keluar
                    </button>
                </form>
            @endauth
        </div>
    </div>
</header>

<main class="w-full max-w-[1140px] mx-auto px-4 py-8 flex-grow">
    @yield('content')
</main>

{{-- Footer sesuai mockup --}}
<footer class="app-footer">
    <div class="container-xl d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
        <div>
            Sistem Manajemen Antrean Bonamu — pengelolaan antrean &amp; informasi layanan, bukan layanan medis.
        </div>
        <div>
            Papan antrean publik: <a href="javascript:void(0)" onclick="tampilkanInfoModul('Papan Antrean Publik', 'PBI-17')" class="text-decoration-underline text-muted">lihat papan</a>
        </div>
    </div>
</footer>

{{-- Bootstrap 5 JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const btnDarkMode = document.getElementById('btnDarkMode');
    if (btnDarkMode) {
        btnDarkMode.addEventListener('click', function () {
            document.body.classList.toggle('dark-mode');
            const icon = this.querySelector('i');
            if (document.body.classList.contains('dark-mode')) {
                icon.classList.remove('bi-moon');
                icon.classList.add('bi-sun');
            } else {
                icon.classList.remove('bi-sun');
                icon.classList.add('bi-moon');
            }
        });
    }

    function tampilkanInfoModul(namaModul, kodePbi) {
        alert('Fitur ' + namaModul + ' (' + kodePbi + ') adalah tanggung jawab modul lain.');
    }
</script>

@stack('scripts')
</body>
</html>
