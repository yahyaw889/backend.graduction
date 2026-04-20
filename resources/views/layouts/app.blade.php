<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MediScan AI — Admin Dashboard</title>
    <meta name="description" content="MediScan AI skin disease detection system admin dashboard.">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('PIC/Tab.png') }}">

    <!-- Bootstrap -->
    @if(app()->getLocale() == 'ar')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts: Tajawal -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary:        #005bb5; /* Professional Corporate Medical Blue */
            --primary-light:  #337ecc;
            --primary-dark:   #003d7a;
            --primary-glow:   #eef5fc; /* Very subtle background for active states */
            --success:        #059669; /* Muted Emerald */
            --warning:        #d97706; /* Muted Amber */
            --danger:         #dc2626; /* Muted Red */
            --info:           #0284c7; /* Standard Cyan/Blue */
            --sidebar-width:  260px;
            --sidebar-bg:     #ffffff;
            --body-bg:        #f3f4f6; /* Standard clean gray */
            --text-main:      #1f2937; /* Gray 800 */
            --text-muted:     #6b7280; /* Gray 500 */
            --border-color:   #e5e7eb;
            --card-shadow:    0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.03);
            --hover-shadow:   0 4px 6px -1px rgba(0,0,0,0.05);
            --navbar-height:  70px;
        }

        [data-bs-theme="dark"] {
            --sidebar-bg:   #0f172a;
            --body-bg:      #020617;
            --text-main:    #f1f5f9;
            --text-muted:   #94a3b8;
            --border-color: #1e293b;
            --card-shadow:  0 1px 3px rgba(0,0,0,.3), 0 4px 16px rgba(0,0,0,.2);
            --hover-shadow: 0 8px 24px rgba(0,0,0,.4);
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Tajawal', system-ui, sans-serif;
            background-color: var(--body-bg);
            color: var(--text-main);
            overflow-x: hidden;
            font-size: 1rem;
        }

        /* ─── Sidebar ─────────────────────────────────────────── */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            border-inline-end: 1px solid var(--border-color);
            position: fixed;
            top: 0;
            inset-inline-start: 0;
            height: 100vh;
            z-index: 1040;
            transition: transform 0.3s cubic-bezier(.4,0,.2,1); /* Optimized transition */
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .sidebar-brand {
            height: var(--navbar-height);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0 1.5rem;
            border-bottom: 1px solid var(--border-color);
            font-weight: 700;
            font-size: 1.15rem;
            color: var(--primary);
            flex-shrink: 0;
            text-decoration: none;
        }

        .sidebar-brand .brand-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .sidebar-section {
            padding: 1.5rem 1rem 0.25rem;
        }

        .sidebar-section-label {
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            color: #94a3b8; /* Slate 400 */
            padding: 0 0.5rem;
            margin-bottom: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem 1rem;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.95rem;
            border-radius: 6px;
            margin: 0.15rem 0.75rem;
            transition: all 0.15s ease-in-out;
            text-decoration: none;
        }

        .nav-link .nav-icon {
            width: 22px;
            text-align: center;
            font-size: 1.1rem;
            flex-shrink: 0;
            color: var(--text-muted);
            transition: color 0.15s ease;
        }

        .nav-link:hover {
            color: var(--text-main);
            background: var(--body-bg);
        }

        .nav-link.active {
            color: var(--primary-dark);
            background-color: var(--primary-glow);
            font-weight: 700;
            box-shadow: none;
        }
        
        .nav-link.active .nav-icon {
            color: var(--primary);
        }

        .sidebar-footer {
            margin-top: auto;
            padding: 1rem;
            border-top: 1px solid var(--border-color);
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            border-radius: 10px;
            background: var(--primary-glow);
        }

        .sidebar-user .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
        }

        .sidebar-user .user-name {
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text-main);
        }

        .sidebar-user .user-role {
            font-size: 0.72rem;
            color: var(--text-muted);
        }

        /* ─── Main Content ─────────────────────────────────────── */
        .main-content {
            margin-inline-start: var(--sidebar-width);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: margin-inline-start 0.3s ease;
        }

        /* Desktop Collapse State - Mini Sidebar */
        @media (min-width: 993px) {
            body.sidebar-collapsed {
                --sidebar-width: 90px;
            }
            body.sidebar-collapsed .sidebar-brand img {
                height: 35px !important;
            }
            body.sidebar-collapsed .sidebar-section-label {
                display: none;
            }
            body.sidebar-collapsed .nav-link span:not(.nav-icon) {
                display: none !important;
            }
            body.sidebar-collapsed .nav-link {
                justify-content: center;
                padding: 0.8rem 0;
                margin: 0.35rem 0.5rem;
            }
            body.sidebar-collapsed .nav-icon {
                margin: 0;
                font-size: 1.3rem;
            }
            body.sidebar-collapsed .sidebar-user > div {
                display: none;
            }
            body.sidebar-collapsed .sidebar-user {
                justify-content: center;
            }
            body.sidebar-collapsed .sidebar-footer .text-muted {
                display: none;
            }
        }

        @media (max-width: 992px) {
            .main-content { margin-inline-start: 0; }
            .sidebar { transform: translateX(calc(var(--sidebar-width) * -1)); }
            [dir="rtl"] .sidebar { transform: translateX(calc(var(--sidebar-width))); }
            .sidebar.show { transform: translateX(0) !important; }
        }

        /* ─── Navbar ───────────────────────────────────────────── */
        .topbar {
            height: var(--navbar-height);
            background: var(--sidebar-bg);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            gap: 1rem;
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .topbar .topbar-search {
            flex: 1;
            max-width: 320px;
        }

        .topbar .search-input {
            border-radius: 8px;
            border: 1px solid var(--border-color);
            background: var(--body-bg);
            font-size: 0.85rem;
            padding: 0.45rem 0.85rem 0.45rem 2.2rem;
            color: var(--text-main);
            width: 100%;
            outline: none;
            transition: border-color 0.2s;
        }

        .topbar .search-input:focus {
            border-color: var(--primary);
        }

        .topbar .search-wrapper {
            position: relative;
        }

        .topbar .search-wrapper .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.8rem;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-inline-start: auto;
        }

        .icon-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            background: var(--body-bg);
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.18s ease;
            font-size: 0.9rem;
        }

        .icon-btn:hover {
            color: var(--primary);
            border-color: var(--primary);
            background: var(--primary-glow);
        }

        /* ─── Content Area ─────────────────────────────────────── */
        .dashboard-content {
            padding: 1.75rem;
            flex-grow: 1;
        }

        @media (max-width: 576px) {
            .dashboard-content { padding: 1rem; }
        }

        /* ─── Cards ─────────────────────────────────────────────── */
        .card {
            background: var(--sidebar-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: var(--hover-shadow);
        }

        /* ─── Metric Cards ─────────────────────────────────────── */
        .metric-card {
            position: relative;
            overflow: hidden;
            padding: 1.5rem;
            border-radius: 12px;
        }

        .metric-card .metric-icon-bg {
            position: absolute;
            inset-inline-end: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            opacity: 0.9;
        }

        .metric-card .metric-label {
            font-size: 0.78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-muted);
            margin-bottom: 0.4rem;
        }

        .metric-card .metric-value {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 0.4rem;
        }

        .metric-card .metric-sub {
            font-size: 0.78rem;
            font-weight: 500;
        }

        .metric-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 100%;
            height: 3px;
            background: currentColor;
            opacity: 0.3;
        }

        /* ─── Tables ─────────────────────────────────────────────── */
        .table > :not(caption) > * > * {
            padding: 0.85rem 1rem;
            background-color: transparent;
        }

        .table thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.06em;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border-color);
        }

        .table tbody tr {
            transition: background 0.15s;
        }

        .table tbody tr:hover {
            background: var(--primary-glow);
        }

        /* ─── Badges ─────────────────────────────────────────────── */
        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.3rem 0.65rem;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 600;
        }

        /* ─── Scrollbar ──────────────────────────────────────────── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* ─── Gradient Text ──────────────────────────────────────── */
        .text-gradient {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ─── Page Header ────────────────────────────────────────── */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .page-title {
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--text-main);
            margin: 0;
        }

        .page-subtitle {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 0.2rem;
        }

        /* ─── Alert toasts ────────────────────────────────────────── */
        .toast-container-custom {
            position: fixed;
            top: 1rem;
            inset-inline-end: 1rem;
            z-index: 9999;
        }

        /* ─── Avatar ─────────────────────────────────────────────── */
        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        /* ─── Dropdown ───────────────────────────────────────────── */
        .dropdown-menu {
            border: 1px solid var(--border-color);
            border-radius: 10px;
            box-shadow: var(--hover-shadow);
            padding: 0.5rem;
        }

        .dropdown-item {
            border-radius: 6px;
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-main);
        }

        .dropdown-item:hover {
            background: var(--primary-glow);
            color: var(--primary);
        }

        .dropdown-item.text-danger:hover {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }

        /* ─── Misc ───────────────────────────────────────────────── */
        .form-control, .form-select {
            border-color: var(--border-color);
            font-size: 0.875rem;
            color: var(--text-main);
            background-color: var(--sidebar-bg);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }

        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .modal-content {
            border: 1px solid var(--border-color);
            border-radius: 14px;
            box-shadow: var(--hover-shadow);
        }

        .modal-header {
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 1.25rem;
        }

        .modal-footer {
            border-top: 1px solid var(--border-color);
            padding: 0.75rem 1.25rem;
        }
    </style>
    @yield('css')
</head>

<body>
    @include('layouts.sidebar')

    <main class="main-content">
        @include('layouts.nav')

        <div class="dashboard-content">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert" style="border-radius:10px; font-size:.875rem;">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert" style="border-radius:10px; font-size:.875rem;">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @include('layouts.script')
    @stack('scripts')

    <script>
        // Theme toggle
        document.addEventListener('DOMContentLoaded', function () {
            const themeToggle = document.getElementById('themeToggle');
            const html = document.documentElement;

            const saved = localStorage.getItem('theme') || 'light';
            if (saved === 'dark') {
                document.body.setAttribute('data-bs-theme', 'dark');
                if (themeToggle) themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
            }

            if (themeToggle) {
                themeToggle.addEventListener('click', () => {
                    const isDark = document.body.getAttribute('data-bs-theme') === 'dark';
                    document.body.setAttribute('data-bs-theme', isDark ? 'light' : 'dark');
                    localStorage.setItem('theme', isDark ? 'light' : 'dark');
                    themeToggle.innerHTML = isDark ? '<i class="fas fa-moon"></i>' : '<i class="fas fa-sun"></i>';
                });
            }

            // Mobile sidebar toggle
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('desktopSidebar');
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', () => {
                    sidebar.classList.toggle('show');
                });
            }
        });
    </script>
</body>

</html>
