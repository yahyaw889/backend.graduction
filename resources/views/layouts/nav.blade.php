    <nav class="navbar navbar-expand-lg sticky-top">
      <div class="container-fluid px-4">
        <button class="btn btn-light d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas"><i class="fas fa-bars"></i></button>

        <form class="d-none d-md-flex ms-auto me-3" role="search" onsubmit="return false;">
          <div class="input-group">
            <input type="search" class="form-control" id="globalSearch" placeholder="ابحث..." aria-label="Search">
            <button class="btn btn-outline-secondary" onclick="doSearch()"><i class="fas fa-search"></i></button>
          </div>
        </form>

        <div class="ms-auto d-flex align-items-center gap-3">
          <div class="d-flex align-items-center small-muted me-2">
            <i class="fas fa-calendar-day me-2"></i>
            <input id="dateRange" placeholder="اختر نطاق تاريخ" class="form-control form-control-sm" style="min-width:160px" />
          </div>

          <button class="btn btn-outline-secondary btn-sm" id="exportCsvBtn"><i class="fas fa-file-csv me-1"></i>تصدير CSV</button>

          <div class="dropdown">
            <a class="dropdown-toggle d-flex align-items-center text-decoration-none" data-bs-toggle="dropdown">
              <img src="https://ui-avatars.com/api/?name=أحمد&background=0D8ABC&color=fff" class="rounded-circle" width="42" alt="avatar"/>
              <span class="me-2 d-none d-lg-inline">أحمد محمد</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="#">الملف الشخصي</a></li>
              <li><a class="dropdown-item" href="#">الإعدادات</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="#">تسجيل الخروج</a></li>
            </ul>
          </div>

          <div class="theme-toggle" id="themeToggle" title="تبديل الوضع الداكن"><i class="fas fa-moon"></i></div>
        </div>
      </div>
    </nav>
