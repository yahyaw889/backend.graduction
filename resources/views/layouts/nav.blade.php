<nav class="topbar">
    <!-- Mobile toggle -->
    <button class="icon-btn border-0 shadow-none bg-transparent d-lg-none" type="button" data-bs-toggle="offcanvas"
        data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas" style="font-size: 1.25rem; color: var(--primary);">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Desktop toggle -->
    <button class="icon-btn border-0 shadow-none bg-transparent d-none d-lg-flex" type="button" onclick="document.body.classList.toggle('sidebar-collapsed')" style="font-size: 1.25rem; color: var(--primary);">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Search -->
    <div class="topbar-search d-none d-md-block">
        <div class="search-wrapper">
            <i class="fas fa-search search-icon"></i>
            <input type="text" class="search-input" placeholder="{{ __('dashboard.search') }}">
        </div>
    </div>

    <!-- Actions -->
    <div class="topbar-actions">
        <!-- Fullscreen -->
        <div class="icon-btn border-0 shadow-none bg-transparent" title="Fullscreen" onclick="toggleFullScreen()" style="font-size: 1.1rem;">
            <i class="fas fa-expand"></i>
        </div>

        <!-- Refresh -->
        <div class="icon-btn border-0 shadow-none bg-transparent" title="Refresh" onclick="location.reload()" style="font-size: 1.1rem;">
            <i class="fas fa-sync-alt"></i>
        </div>

        <!-- Notifications -->
        <div class="dropdown">
            <div class="icon-btn border-0 shadow-none bg-transparent position-relative" data-bs-toggle="dropdown" title="Notifications" style="font-size: 1.1rem;">
                <i class="fas fa-bell"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.55rem; padding:0.25em 0.4em;">
                    3
                </span>
            </div>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="min-width:320px; padding:0; border-radius:10px; overflow:hidden;">
                <li class="px-3 py-3 border-bottom d-flex justify-content-between align-items-center bg-light">
                    <span class="fw-bold text-primary" style="font-size:.9rem;">الإشعارات الجديدة</span>
                    <span class="badge bg-danger rounded-pill">3</span>
                </li>
                <div class="overflow-auto" style="max-height: 350px;">
                    <!-- Fake Notification 1 -->
                    <a href="#" class="dropdown-item px-3 py-3 border-bottom text-wrap d-flex gap-3 align-items-start" style="font-size: .85rem; background: #f8fafc;">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px; height:36px;">
                            <i class="fas fa-robot"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">تشخيص ذكاء اصطناعي جديد</div>
                            <div class="text-muted mt-1" style="font-size:.8rem;">تم رفع صورة جديدة وتحليلها بواسطة النظام (المريض: أحمد علي).</div>
                            <div class="text-muted mt-2" style="font-size:.7rem;"><i class="far fa-clock"></i> منذ 5 دقائق</div>
                        </div>
                    </a>
                    <!-- Fake Notification 2 -->
                    <a href="#" class="dropdown-item px-3 py-3 border-bottom text-wrap d-flex gap-3 align-items-start" style="font-size: .85rem; background: #f8fafc;">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px; height:36px;">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">مريض جديد مسجل</div>
                            <div class="text-muted mt-1" style="font-size:.8rem;">قام "محمود خالد" بإنشاء حساب جديد في التطبيق.</div>
                            <div class="text-muted mt-2" style="font-size:.7rem;"><i class="far fa-clock"></i> منذ ساعة</div>
                        </div>
                    </a>
                    <!-- Fake Notification 3 -->
                    <a href="#" class="dropdown-item px-3 py-3 border-bottom text-wrap d-flex gap-3 align-items-start" style="font-size: .85rem; background: #f8fafc;">
                        <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px; height:36px;">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">رسالة شات جديدة</div>
                            <div class="text-muted mt-1" style="font-size:.8rem;">المريض يستفسر عن نتيجة الفحص الأخير الخاص به.</div>
                            <div class="text-muted mt-2" style="font-size:.7rem;"><i class="far fa-clock"></i> منذ ساعتين</div>
                        </div>
                    </a>
                </div>
                <li class="px-3 py-2 text-center bg-light">
                    <a href="#" class="text-decoration-none text-primary fw-bold" style="font-size:.8rem;">عرض كل الإشعارات</a>
                </li>
            </ul>
        </div>

        <!-- Theme toggle -->
        <div class="icon-btn border-0 shadow-none bg-transparent" id="themeToggle" title="Toggle dark mode" style="font-size: 1.1rem;">
            <i class="fas fa-moon"></i>
        </div>

        <!-- User dropdown -->
        <div class="dropdown ms-2 ps-2 border-start border-2">
            <div class="d-flex align-items-center gap-2" role="button" data-bs-toggle="dropdown" style="cursor:pointer;">
                <div class="text-end d-none d-md-block">
                    <div style="font-size:.8rem; color:var(--text-muted); line-height: 1;">مرحباً</div>
                    <div style="font-size:.9rem; font-weight:700; color:var(--primary);">{{ Auth::user()->name ?? 'المدير' }}</div>
                </div>
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Admin') }}&background=0284c7&color=fff&size=64"
                    class="rounded-circle shadow-sm" width="38" height="38" alt="avatar" style="object-fit:cover; border: 2px solid white;">
            </div>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" style="border-radius: 10px;">
                <li>
                    <a class="dropdown-item" href="{{ route('settings.index') ?? '#' }}">
                        <i class="fas fa-user mx-2" style="width:16px; color:var(--text-muted);"></i> الملف الشخصي
                    </a>
                </li>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <form method="POST" action="{{ route('logout') ?? '#' }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger fw-bold">
                            <i class="fas fa-sign-out-alt mx-2" style="width:16px;"></i> تسجيل الخروج
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
