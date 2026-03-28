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
        :root {
            --primary: #367ac2;
            --primary-light: #4a8fd4;
            --primary-dark: #2a5f9a;
            --sidebar-width: 260px;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Cairo', 'Segoe UI', Tahoma, sans-serif;
            background: #f5f7fa;
            transition: background-color 0.3s ease, color 0.3s ease;
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            right: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: #ffffff;
            box-shadow: -4px 0 20px rgba(0, 0, 0, .08);
            padding-top: 20px;
            z-index: 1050;
            transition: all 0.3s ease;
            overflow-y: auto;
        }

        .sidebar .logo {
            padding: 18px 26px;
            font-weight: 800;
            color: var(--primary);
            font-size: 1.4rem;
            border-bottom: 1px solid #e8ecef;
            margin-bottom: 10px;
        }

        .sidebar .nav-link {
            color: #556070;
            padding: 12px 22px;
            border-radius: 10px;
            margin: 6px 12px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .sidebar .nav-link i {
            width: 28px;
            margin-left: 10px;
        }

        .sidebar .nav-link.active,
        .sidebar .nav-link:hover {
            background: linear-gradient(135deg, rgba(54, 122, 194, 0.1) 0%, rgba(54, 122, 194, 0.05) 100%);
            color: var(--primary);
        }

        /* Main Content */
        .main-content {
            margin-right: var(--sidebar-width);
            padding: 28px;
            min-height: 100vh;
            transition: margin-right 0.3s ease;
        }

        .navbar {
            height: 70px;
            background: #ffffff;
            box-shadow: 0 2px 15px rgba(0, 0, 0, .06);
            z-index: 1040;
            transition: background-color 0.3s ease;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(54, 122, 194, .08);
            transition: all 0.3s ease;
            background: #ffffff;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(54, 122, 194, .15);
        }

        /* Metrics */
        .metric {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            margin: 8px 0;
        }

        .small-muted {
            font-size: .875rem;
            color: #6b7280;
        }

        /* Tables */
        .table-wrap {
            max-height: 400px;
            overflow-x: auto;
            overflow-y: auto;
            border-radius: 8px;
        }

        .table {
            margin-bottom: 0;
            width: 100%;
        }

        .table thead th {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: #ffffff;
            font-weight: 600;
            border: none;
            padding: 12px 16px;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .table tbody tr {
            transition: background-color 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(54, 122, 194, 0.05);
        }

        .table tbody td {
            padding: 12px 16px;
            border-bottom: 1px solid #e8ecef;
            vertical-align: middle;
        }

        /* List Groups */
        .list-group-item {
            border: none;
            border-bottom: 1px solid #e8ecef;
            padding: 12px 16px;
            transition: background-color 0.2s ease;
        }

        .list-group-item:hover {
            background-color: rgba(54, 122, 194, 0.05);
        }

        /* Dark Mode */
        body.dark-mode {
            background: #0f1419;
            color: #e6edf3;
        }

        body.dark-mode .sidebar {
            background: #1a1f2e;
            box-shadow: -4px 0 20px rgba(0, 0, 0, .4);
        }

        body.dark-mode .sidebar .logo {
            border-bottom-color: #2a3441;
        }

        body.dark-mode .navbar {
            background: #1a1f2e;
            box-shadow: 0 2px 15px rgba(0, 0, 0, .3);
        }

        body.dark-mode .card {
            background: #1a1f2e;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .3);
            color: #e6edf3;
        }

        body.dark-mode .sidebar .nav-link {
            color: #9ca3af;
        }

        body.dark-mode .sidebar .nav-link.active,
        body.dark-mode .sidebar .nav-link:hover {
            background: linear-gradient(135deg, rgba(54, 122, 194, 0.2) 0%, rgba(54, 122, 194, 0.1) 100%);
            color: #4a8fd4;
        }

        body.dark-mode .small-muted {
            color: #9ca3af;
        }

        body.dark-mode .metric {
            color: #4a8fd4;
        }

        body.dark-mode .table {
            color: #e6edf3;
        }

        body.dark-mode .table thead th {
            background: linear-gradient(135deg, #2a5f9a 0%, #367ac2 100%);
        }

        body.dark-mode .table tbody td {
            border-bottom-color: #2a3441;
        }

        body.dark-mode .table tbody tr:hover {
            background-color: rgba(54, 122, 194, 0.15);
        }

        body.dark-mode .list-group-item {
            background: transparent;
            border-bottom-color: #2a3441;
            color: #e6edf3;
        }

        body.dark-mode .list-group-item:hover {
            background-color: rgba(54, 122, 194, 0.15);
        }

        body.dark-mode .modal-content {
            background: #1a1f2e;
            color: #e6edf3;
        }

        body.dark-mode .form-control {
            background: #0f1419;
            border-color: #2a3441;
            color: #e6edf3;
        }

        body.dark-mode .form-control:focus {
            background: #1a1f2e;
            border-color: var(--primary);
        }

        body.dark-mode .dropdown-menu {
            background: #1a1f2e;
            border-color: #2a3441;
        }

        body.dark-mode .dropdown-item {
            color: #e6edf3;
        }

        body.dark-mode .dropdown-item:hover {
            background: #2a3441;
        }

        .theme-toggle {
            cursor: pointer;
            font-size: 1.2rem;
            color: #6b7280;
            transition: color 0.3s ease;
        }

        .theme-toggle:hover {
            color: var(--primary);
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .main-content {
                padding: 20px;
            }
        }

        @media (max-width: 992px) {
            .sidebar {
                width: 80px;
            }

            .sidebar .logo,
            .sidebar .nav-link span {
                display: none;
            }

            .sidebar .nav-link {
                text-align: center;
                padding: 12px;
            }

            .sidebar .nav-link i {
                margin: 0;
                width: auto;
            }

            .main-content {
                margin-right: 80px;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(100%);
                width: var(--sidebar-width);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-right: 0;
                padding: 16px;
            }

            .navbar {
                margin-bottom: 16px;
            }

            .metric {
                font-size: 1.5rem;
            }

            .table-wrap {
                max-height: 300px;
            }
        }

        @media (max-width: 576px) {
            .main-content {
                padding: 12px;
            }

            .card {
                margin-bottom: 16px;
            }

            .metric {
                font-size: 1.3rem;
            }

            .small-muted {
                font-size: 0.75rem;
            }

            .table thead th,
            .table tbody td {
                padding: 8px 10px;
                font-size: 0.875rem;
            }
        }

        /* Scrollbar Styling */
        .table-wrap::-webkit-scrollbar,
        .sidebar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .table-wrap::-webkit-scrollbar-track,
        .sidebar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .table-wrap::-webkit-scrollbar-thumb,
        .sidebar::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 10px;
        }

        body.dark-mode .table-wrap::-webkit-scrollbar-track,
        body.dark-mode .sidebar::-webkit-scrollbar-track {
            background: #2a3441;
        }

        body.dark-mode .table-wrap::-webkit-scrollbar-thumb,
        body.dark-mode .sidebar::-webkit-scrollbar-thumb {
            background: var(--primary-light);
        }
    </style>
    @yield('css')
</head>

<body>

    @include('layouts.sidebar')

    <!-- Navbar -->

    @include('layouts.nav')
    <!-- Main content -->
    <main class="main-content">
        @yield('content')

    </main>

    @include('layouts.script')
    @stack('scripts')

    <script>
        // Sidebar Toggle for Mobile - Inline to ensure it loads
        (function() {
            const sidebarToggleBtn = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');

            if (sidebarToggleBtn && sidebar) {
                console.log('Sidebar toggle initialized'); // Debug log

                // Toggle sidebar on button click
                sidebarToggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Toggle clicked'); // Debug log
                    sidebar.classList.toggle('show');
                });

                // Close sidebar when clicking outside on mobile
                document.addEventListener('click', function(event) {
                    if (window.innerWidth <= 768) {
                        if (!sidebar.contains(event.target) && !sidebarToggleBtn.contains(event.target)) {
                            sidebar.classList.remove('show');
                        }
                    }
                });

                // Close sidebar when clicking on a link inside it
                sidebar.querySelectorAll('.nav-link').forEach(link => {
                    link.addEventListener('click', function() {
                        if (window.innerWidth <= 768) {
                            sidebar.classList.remove('show');
                        }
                    });
                });

                // Close sidebar on window resize if screen becomes larger
                window.addEventListener('resize', function() {
                    if (window.innerWidth > 768) {
                        sidebar.classList.remove('show');
                    }
                });
            } else {
                console.log('Sidebar or toggle button not found'); // Debug log
            }
        })();
    </script>
</body>

</html>
