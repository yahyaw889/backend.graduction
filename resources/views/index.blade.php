<!doctype html>
<html lang="ar" dir="rtl">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>داشبورد بسيط - Bootstrap</title>
    <!-- Bootstrap RTL CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
      body { min-height: 100vh; }
      .sidebar { height: 100vh; }
      .card-title { font-size: 1rem; }
      .metric { font-weight: 700; font-size: 1.5rem; }
      .table-wrap { max-height: 300px; overflow:auto; }
    </style>
  </head>
  <body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-white bg-white border-bottom">
      <div class="container-fluid">
        <button class="btn btn-outline-secondary d-md-none me-2" id="toggleSidebar">☰</button>
        <a class="navbar-brand" href="#">لوحة التحكم</a>
        <div class="ms-auto d-flex align-items-center">
          <div class="me-3 text-muted">مرحبا، يحيى</div>
          <img src="https://ui-avatars.com/api/?name=Y+Y&background=0D8ABC&color=fff" class="rounded-circle" width="40" height="40" alt="avatar">
        </div>
      </div>
    </nav>

    <div class="container-fluid">
      <div class="row">
        <!-- Sidebar -->
        <aside class="col-md-2 bg-white border-end sidebar d-none d-md-block p-3" id="sidebar">
          <h6 class="text-uppercase">القائمة</h6>
          <ul class="nav nav-pills flex-column">
            <li class="nav-item"><a class="nav-link active" href="#">الرئيسية</a></li>
            <li class="nav-item"><a class="nav-link" href="#">المستخدمين</a></li>
            <li class="nav-item"><a class="nav-link" href="#">التقارير</a></li>
            <li class="nav-item"><a class="nav-link" href="#">الإعدادات</a></li>
          </ul>
        </aside>

        <!-- Main -->
        <main class="col-md-10 ms-sm-auto col-lg-10 px-4 py-4">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>لوحة المراقبة</h2>
            <div>
              <button class="btn btn-primary me-2">إنشاء تقرير</button>
              <button class="btn btn-outline-secondary">تصدير</button>
            </div>
          </div>

          <!-- Metrics -->
          <div class="row g-3 mb-4">
            <div class="col-sm-6 col-md-3">
              <div class="card p-3">
                <div class="card-body">
                  <div class="card-title text-muted">المستخدمون النشطون</div>
                  <div class="metric text-success">1,254</div>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-md-3">
              <div class="card p-3">
                <div class="card-body">
                  <div class="card-title text-muted">الزيارات اليوم</div>
                  <div class="metric text-primary">3,421</div>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-md-3">
              <div class="card p-3">
                <div class="card-body">
                  <div class="card-title text-muted">المبيعات</div>
                  <div class="metric text-danger">₤ 9,120</div>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-md-3">
              <div class="card p-3">
                <div class="card-body">
                  <div class="card-title text-muted">المهام المتبقية</div>
                  <div class="metric text-warning">8</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Chart + Table -->
          <div class="row g-4">
            <div class="col-lg-7">
              <div class="card p-3">
                <div class="card-body">
                  <h5 class="card-title">نشاط الزيارات - آخر 7 أيام</h5>
                  <canvas id="visitsChart" height="120"></canvas>
                </div>
              </div>
            </div>
            <div class="col-lg-5">
              <div class="card p-3">
                <div class="card-body">
                  <h5 class="card-title">آخر المستخدمين</h5>
                  <div class="table-wrap mt-3">
                    <table class="table table-striped table-sm">
                      <thead>
                        <tr>
                          <th>الاسم</th>
                          <th>البريد</th>
                          <th>الحالة</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr><td>أحمد</td><td>ahmad@example.com</td><td><span class="badge bg-success">نشط</span></td></tr>
                        <tr><td>مروة</td><td>marwa@example.com</td><td><span class="badge bg-secondary">غير نشط</span></td></tr>
                        <tr><td>سامي</td><td>sami@example.com</td><td><span class="badge bg-success">نشط</span></td></tr>
                        <tr><td>ليلى</td><td>layla@example.com</td><td><span class="badge bg-warning">مؤقت</span></td></tr>
                        <tr><td>زيد</td><td>zayed@example.com</td><td><span class="badge bg-danger">محظور</span></td></tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Footer small -->
          <footer class="mt-4 text-center text-muted small">© 2025 لوحة التحكم - مثال تعليمي</footer>
        </main>
      </div>
    </div>

    <!-- Bootstrap JS + Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
      // Sidebar toggle for small screens
      document.getElementById('toggleSidebar').addEventListener('click', function () {
        var sb = document.getElementById('sidebar');
        if (sb.classList.contains('d-none')) sb.classList.remove('d-none'); else sb.classList.add('d-none');
      });

      // Sample chart
      const ctx = document.getElementById('visitsChart');
      new Chart(ctx, {
        type: 'line',
        data: {
          labels: ['الاثنين','الثلاثاء','الاربعاء','الخميس','الجمعة','السبت','الأحد'],
          datasets: [{
            label: 'الزيارات',
            data: [120, 190, 150, 220, 170, 240, 300],
            tension: 0.4,
            fill: true,
            backgroundColor: 'rgba(13,110,253,0.08)',
            borderColor: 'rgba(13,110,253,1)'
          }]
        },
        options: {
          maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          scales: {
            y: { beginAtZero: true }
          }
        }
      });
    </script>
  </body>
</html>
