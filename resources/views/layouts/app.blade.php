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
      :root{--primary: #6366f1;--sidebar-width:260px}
      html,body{height:100%}
      body{font-family: 'Cairo', 'Segoe UI', Tahoma, sans-serif;background:#f8fafc;margin:0;transition: background-color 0.3s ease, color 0.3s ease}

      /* Sidebar */
      .sidebar{position:fixed;top:0;right:0;width:var(--sidebar-width);height:100vh;background:#fff;box-shadow:-6px 0 30px rgba(0,0,0,.06);padding-top:20px;z-index:1050;transition: background-color 0.3s ease}
      .sidebar .logo{padding:18px 26px;font-weight:800;color:var(--primary);font-size:1.4rem;border-bottom:1px solid #f2f4f7}
      .sidebar .nav-link{color:#556070;padding:12px 22px;border-radius:12px;margin:6px 12px;transition: background-color 0.3s ease, color 0.3s ease}
      .sidebar .nav-link i{width:28px;margin-left:10px}
      .sidebar .nav-link.active, .sidebar .nav-link:hover{background:rgba(99,102,241,.08);color:var(--primary)}

      /* small collapse */
      @media (max-width:992px){.sidebar{width:84px}.sidebar .logo,.sidebar .nav-link span{display:none}.main-content{margin-right:84px}}
      @media (max-width:768px){.sidebar{display:none}.offcanvas-lg{visibility:visible!important}}

      /* main */
      .main-content{margin-right:var(--sidebar-width);padding:28px;min-height:100vh}
      .navbar{height:70px;background:#fff;box-shadow:0 4px 20px rgba(0,0,0,.06);z-index:1040;transition: background-color 0.3s ease}
      .card{border:none;border-radius:14px;box-shadow:0 8px 28px rgba(15,23,42,.04);transition:transform .25s, background-color 0.3s ease}
      .card:hover{transform:translateY(-6px)}

      /* widgets */
      .metric{font-size:1.4rem;font-weight:700}
      .small-muted{font-size:.85rem;color:#6b7280}

      /* table wrapper */
      .table-wrap{max-height:300px;overflow:auto}

      /* theme */
      body.dark-mode{background:#0b1220;color:#e6edf3}
      body.dark-mode .sidebar{background:#081023;box-shadow:-6px 0 30px rgba(0,0,0,.3)}
      body.dark-mode .navbar{background:#081023;box-shadow:0 4px 20px rgba(0,0,0,.3)}
      body.dark-mode .card{background:#0d1829;box-shadow:0 8px 40px rgba(2,6,23,.6);color:#e6edf3}
      body.dark-mode .sidebar .nav-link{color:#9ca3af}
      body.dark-mode .sidebar .nav-link.active, body.dark-mode .sidebar .nav-link:hover{background:rgba(99,102,241,.15);color:#818cf8}
      body.dark-mode .small-muted{color:#9ca3af}
      body.dark-mode .table{color:#e6edf3}
      body.dark-mode .modal-content{background:#0d1829;color:#e6edf3}
      body.dark-mode .form-control{background:#081023;border-color:#1e293b;color:#e6edf3}
      body.dark-mode .dropdown-menu{background:#0d1829;border-color:#1e293b}
      body.dark-mode .dropdown-item{color:#e6edf3}
      body.dark-mode .dropdown-item:hover{background:#1e293b}
      .theme-toggle{cursor:pointer;font-size:1.2rem;color:#6b7280;transition: color 0.3s ease}
      .theme-toggle:hover{color:var(--primary)}

      /* responsive tweaks */
      @media (max-width:576px){.main-content{padding:16px}}
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
  </body>
</html>