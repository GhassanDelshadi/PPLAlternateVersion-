<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - Mengelola Data Poli') — Bonamu</title>

    {{-- Bootstrap 5 CSS & Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    {{-- Google Fonts: Plus Jakarta Sans, Playfair Display, JetBrains Mono --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --app-primary: #005954;
            --app-primary-hover: #004743;
            --app-primary-dark: #092c24;
            --app-primary-light: #d1f4ed;
            --app-primary-border: #bbf0e1;
            --app-bg: #f8fafa;
            --card-bg: #ffffff;
            --text-color: #1e293b;
            --text-muted: #64748b;
        }

        .font-display {
            font-family: 'Playfair Display', Georgia, serif;
        }

        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }

        .badge-tag {
            display: inline-block;
            background-color: var(--app-primary-light);
            color: var(--app-primary);
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 0.35rem 0.85rem;
            border-radius: 50rem;
            margin-bottom: 0.5rem;
        }

        /* Focus Ring yang tegas dan rapi */
        .form-control:focus, .form-select:focus {
            border-color: var(--app-primary) !important;
            box-shadow: 0 0 0 3px rgba(0, 89, 84, 0.15) !important;
            outline: none !important;
        }

        /* Sinkronisasi warna tema Figma untuk seluruh komponen Bootstrap */
        .btn-primary {
            background-color: var(--app-primary) !important;
            border-color: var(--app-primary) !important;
            color: #ffffff !important;
        }
        .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
            background-color: var(--app-primary-hover) !important;
            border-color: var(--app-primary-hover) !important;
            color: #ffffff !important;
        }
        .btn-outline-primary {
            color: var(--app-primary) !important;
            border-color: var(--app-primary) !important;
        }
        .btn-outline-primary:hover, .btn-outline-primary:focus, .btn-outline-primary:active {
            background-color: var(--app-primary) !important;
            border-color: var(--app-primary) !important;
            color: #ffffff !important;
        }
        .bg-primary {
            background-color: var(--app-primary) !important;
        }
        .text-primary {
            color: var(--app-primary) !important;
        }
        .border-primary {
            border-color: var(--app-primary) !important;
        }
        .badge.bg-primary {
            background-color: var(--app-primary) !important;
            color: #ffffff !important;
        }

        /* Pagination & SVG Protection */
        .page-item.active .page-link {
            background-color: var(--app-primary) !important;
            border-color: var(--app-primary) !important;
            color: #ffffff !important;
        }
        .page-link {
            color: var(--app-primary) !important;
        }
        .page-link:hover {
            color: var(--app-primary-hover) !important;
            background-color: #f1f5f9 !important;
        }
        .pagination svg,
        nav[aria-label*="Pagination"] svg,
        nav[aria-label*="pagination"] svg {
            width: 1rem !important;
            height: 1rem !important;
            max-width: 1rem !important;
            max-height: 1rem !important;
            display: inline-block !important;
            vertical-align: middle !important;
        }

        body.dark-mode {
            --app-bg: #121820;
            --card-bg: #1a222d;
            --text-color: #e2e8f0;
            background-color: var(--app-bg) !important;
            color: var(--text-color) !important;
        }

        body.dark-mode .app-header,
        body.dark-mode .app-footer,
        body.dark-mode .card-mockup {
            background-color: var(--card-bg) !important;
            border-color: #2d3748 !important;
            color: var(--text-color) !important;
        }

        body.dark-mode .text-dark {
            color: #f1f5f9 !important;
        }

        body.dark-mode .table-mockup th,
        body.dark-mode .table-mockup td {
            background-color: var(--card-bg) !important;
            border-color: #2d3748 !important;
            color: var(--text-color) !important;
        }

        body.dark-mode .form-control {
            background-color: #242e3d;
            border-color: #374151;
            color: #f1f5f9;
        }

        body.dark-mode .badge-kode {
            background-color: #242e3d;
            border-color: #374151;
            color: #cbd5e1;
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

        /* Top Header Mockup Style */
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
            background-color: #fef3c7;
            color: #92400e;
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

        /* Card & Table Mockup Style */
        .card-mockup {
            background: #ffffff;
            border: 1px solid #e8ecf1;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
            overflow: hidden;
        }

        .card-mockup-header {
            padding: 1.25rem 1.5rem 0.5rem 1.5rem;
            background: transparent;
            border-bottom: none;
        }

        .card-mockup-body {
            padding: 1.25rem 1.5rem 1.5rem 1.5rem;
        }

        .btn-app-primary {
            background-color: var(--app-primary);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 0.65rem 1.25rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-app-primary:hover {
            background-color: var(--app-primary-hover);
            color: #ffffff;
        }

        .table-mockup {
            margin-bottom: 0;
        }

        .table-mockup th {
            font-size: 0.75rem;
            font-weight: 700;
            color: #718096;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            padding: 0.85rem 1rem;
            border-bottom: 1px solid #edf2f7;
            background: transparent;
        }

        .table-mockup td {
            font-size: 0.875rem;
            padding: 0.9rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .badge-kode {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
            padding: 0.25rem 0.55rem;
            border-radius: 6px;
            font-family: monospace;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-dot-active {
            color: #10b981;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .status-dot-inactive {
            color: #94a3b8;
            font-weight: 500;
            font-size: 0.85rem;
        }

        .btn-action-outline {
            border: 1px solid #e2e8f0;
            background: white;
            color: #4a5568;
            font-size: 0.8rem;
            padding: 0.25rem 0.75rem;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.15s ease;
        }

        .btn-action-outline:hover {
            background: #f8fafc;
            color: #1a202c;
            border-color: #cbd5e1;
        }

        .btn-action-delete {
            border: 1px solid #fecaca;
            background: white;
            color: #dc2626;
            font-size: 0.8rem;
            padding: 0.25rem 0.75rem;
            border-radius: 8px;
            transition: all 0.15s ease;
        }

        .btn-action-delete:hover {
            background: #fee2e2;
            color: #b91c1c;
        }

        .btn-action-activate {
            border: 1px solid #bbf7d0;
            background: white;
            color: #16a34a;
            font-size: 0.8rem;
            padding: 0.25rem 0.75rem;
            border-radius: 8px;
            transition: all 0.15s ease;
        }

        .btn-action-activate:hover {
            background: #dcfce7;
            color: #15803d;
        }

        .app-footer {
            margin-top: auto;
            padding: 1.5rem 0;
            font-size: 0.8rem;
            color: #94a3b8;
            border-top: 1px solid #e9ecef;
            background: #ffffff;
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- Header sesuai mockup --}}
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
                    <a href="{{ route('admin.layanan.index') }}" class="nav-pill-item {{ request()->routeIs('admin.layanan.*') ? 'active' : '' }}">
                        <i class="bi bi-tag me-1"></i> Layanan &amp; Tarif
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
                    <a href="{{ route('konsultasi.create') }}" class="nav-pill-item {{ request()->routeIs('konsultasi.*') ? 'active' : '' }}">
                        <i class="bi bi-chat-dots"></i> <span>Konsultasi</span>
                    </a>
                @elseif(auth()->user()->role === 'dokter')
                    <a href="{{ route('dokter.konsultasi.index') }}" class="nav-pill-item {{ request()->routeIs('dokter.konsultasi.*') ? 'active' : '' }}">
                        <i class="bi bi-clipboard2-pulse me-1"></i> Konsultasi Masuk
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
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.profil.edit') }}" class="user-badge d-none d-sm-inline-flex text-decoration-none text-dark" title="Buka Profil Admin">
                        <span>{{ auth()->user()->name }}</span>
                        <span class="admin-pill">ADMIN</span>
                    </a>
                @elseif(auth()->user()->role === 'pasien')
                    <a href="{{ route('pasien.profil.edit') }}" class="user-badge d-none d-sm-inline-flex text-decoration-none text-dark" title="Buka Profil Pasien">
                        <span>{{ auth()->user()->name }}</span>
                        <span class="badge bg-primary-subtle text-primary border px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;">PASIEN</span>
                    </a>
                @else
                    <div class="user-badge d-none d-sm-inline-flex">
                        <span>{{ auth()->user()->name }}</span>
                        <span class="admin-pill">{{ strtoupper(auth()->user()->role ?? 'USER') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('logout') }}" class="d-inline m-0">
                    @csrf
                    <button type="submit" class="btn-keluar">Keluar</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-sm btn-primary rounded-pill px-3">Masuk</a>
            @endauth
        </div>
    </div>
</header>

{{-- Sub-bar kecil --}}
<div class="container-xl py-2 d-flex justify-content-between align-items-center">
    <small class="text-muted" style="font-size: 0.8rem;">
        Sistem Bonamu &rsaquo; <strong>@yield('title', 'Dashboard')</strong>
    </small>
    <small class="badge bg-light text-secondary border px-2 py-1">
        Cabang: {{ auth()->check() && auth()->user()->cabang ? auth()->user()->cabang->nama_cabang : 'Klinik Bonamu Pusat' }}
    </small>
</div>

{{-- Main Content --}}
<main class="container-xl mb-5 flex-grow-1">
    {{-- Pesan Flash Sukses --}}
    @if(session('sukses') || session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-3" role="alert" dusk="alert-sukses">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div>{{ session('sukses') ?? session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Pesan Error Validasi --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-3" role="alert" dusk="alert-error">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <div>
                    <strong>Terdapat kesalahan pada input:</strong>
                    <ul class="mb-0 mt-1 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

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
    // Fitur Dark Mode Toggle
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

    // Modal/Toast interaktif jika menu modul tim lain diklik
    function tampilkanInfoModul(namaModul, kodePbi) {
        alert('Fitur ' + namaModul + ' (' + kodePbi + ') adalah tanggung jawab modul lain dan saat ini sedang dalam pengerjaan oleh anggota tim terkait.');
    }
</script>

@stack('scripts')
</body>
</html>
