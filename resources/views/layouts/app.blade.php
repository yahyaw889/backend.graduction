<!doctype html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>داش بورد متقدم — Bootstrap</title>

    <!-- Bootstrap RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
    <!-- Flatpickr for date range (optional nice picker) -->
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap');

        :root {
            --primary: #4361ee;
            --primary-light: #4895ef;
            --primary-dark: #3f37c9;
            --success: #2ec4b6;
            --warning: #ff9f1c;
            --danger: #e71d36;
            --sidebar-width: 280px;
            --sidebar-bg: #ffffff;
            --body-bg: #f8f9fa;
            --text-main: #2b2d42;
            --text-muted: #8d99ae;
            --border-color: #edf2f4;
            --card-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.05);
            --hover-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.1);
        }

        [data-bs-theme="dark"] {
            --sidebar-bg: #1a1a1a;
            --body-bg: #121212;
            --text-main: #edf2f4;
            --text-muted: #8d99ae;
            --border-color: #2b2d42;
            --card-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.3);
        }

        body {
            font-family: 'Cairo', sans-serif;
            background-color: var(--body-bg);
            color: var(--text-main);
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            border-left: 1px solid var(--border-color);
            position: fixed;
            top: 0;
            right: 0;
            height: 100vh;
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            color: var(--primary);
            font-weight: 800;
            font-size: 1.5rem;
            height: 70px;
            /* Align with navbar height if needed, or just standard padding */
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.5rem;
            color: var(--text-muted);
            font-weight: 600;
            transition: all 0.2s ease;
            border-right: 3px solid transparent;
            margin: 0.25rem 0;
        }

        .nav-link i {
            width: 24px;
            font-size: 1.1rem;
            transition: transform 0.2s ease;
        }

        .nav-link:hover {
            color: var(--primary);
            background: rgba(67, 97, 238, 0.05);
        }

        .nav-link.active {
            color: var(--primary);
            background: rgba(67, 97, 238, 0.08);
            border-right-color: var(--primary);
        }

        .nav-link.active i {
            transform: translateX(-3px);
        }

        /* Main Content */
        .main-content {
            margin-right: var(--sidebar-width);
            padding: 0;
            /* Removed padding to allow navbar to be flush */
            transition: margin-right 0.3s ease;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        @media (max-width: 992px) {
            .main-content {
                margin-right: 0;
            }
        }

        /* Content Wrapper for padding */
        .dashboard-content {
            padding: 2rem;
            flex-grow: 1;
        }

        @media (max-width: 576px) {
            .dashboard-content {
                padding: 1rem;
            }
        }

        /* Navbar */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            /* Slightly less transp to hide content scrolling under */
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-color);
            padding: 0.75rem 1rem;
            border-radius: 0;
            /* Remove radius */
            margin-bottom: 0;
            /* Remove margin */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            /* Lighter shadow */
            position: sticky;
            top: 0;
            z-index: 1020;
        }

        /* Cards */
        .card {
            background: var(--sidebar-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: var(--hover-shadow);
        }

        .metric-card {
            position: relative;
            overflow: hidden;
        }

        .metric-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
        }

        .metric-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-main);
            line-height: 1;
            margin: 0.5rem 0;
        }

        .metric-label {
            color: var(--text-muted);
            font-size: 0.9rem;
            font-weight: 600;
        }

        .metric-icon {
            position: absolute;
            bottom: 1rem;
            left: 1rem;
            font-size: 4rem;
            opacity: 0.05;
            transform: rotate(15deg);
        }

        /* Tables */
        .table-responsive {
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        .table> :not(caption)>*>* {
            padding: 1rem 1.25rem;
            background-color: transparent;
            border-bottom-width: 1px;
            box-shadow: inset 0 0 0 9999px transparent;
        }

        .table thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            background: rgba(0, 0, 0, 0.02);
            border-bottom: 1px solid var(--border-color);
        }

        /* Offcanvas for mobile */
        .offcanvas {
            border-right: none !important;
            border-left: 1px solid var(--border-color);
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Utilities */
        .bg-gradient-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        }

        .text-gradient {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
    @yield('css')
</head>

<body>

    @include('layouts.sidebar')

    <!-- Main content -->
    <main class="main-content">
        @include('layouts.nav')

        <div class="dashboard-content">
            @yield('content')
        </div>

    </main>

    @include('layouts.script')
    @stack('scripts')

    <!-- Bootstrap JS Bundle (includes Popper) for Offcanvas -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Theme toggle logic if exists in nav
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('themeToggle');
            const body = document.body;

            // Check local storage
            if (localStorage.getItem('theme') === 'dark') {
                body.setAttribute('data-bs-theme', 'dark');
                if (themeToggle) themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
            }

            if (themeToggle) {
                themeToggle.addEventListener('click', () => {
                    if (body.getAttribute('data-bs-theme') === 'dark') {
                        body.setAttribute('data-bs-theme', 'light');
                        localStorage.setItem('theme', 'light');
                        themeToggle.innerHTML = '<i class="fas fa-moon"></i>';
                    } else {
                        body.setAttribute('data-bs-theme', 'dark');
                        localStorage.setItem('theme', 'dark');
                        themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
                    }
                });
            }
        });
    </script>
</body>

</html>
