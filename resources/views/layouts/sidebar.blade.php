<!-- Sidebar (desktop) -->
<aside class="sidebar d-none d-lg-flex" id="desktopSidebar">
    <div class="sidebar-header">
        <i class="fas fa-heartbeat text-primary"></i>
        <span>النظام الطبي</span>
    </div>
    <nav class="nav flex-column px-2 gap-1">
        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
            <i class="fas fa-home"></i><span>الرئيسية</span>
        </a>
        <a class="nav-link {{ request()->routeIs('chat.*') ? 'active' : '' }}" href="{{ route('chat.index') }}">
            <i class="fas fa-comments"></i><span>المحادثات</span>
        </a>
        <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
            <i class="fas fa-users"></i><span>المستخدمين</span>
        </a>
        <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}">
            <i class="fas fa-cog"></i><span>الإعدادات</span>
        </a>
    </nav>

    <div class="mt-auto p-4">
        <div class="p-3 bg-light rounded-3">
            <div class="small fw-bold mb-1">تحتاج مساعدة؟</div>
            <div class="text-muted small mb-2" style="font-size: 0.8rem">تواصل مع الدعم الفني</div>
            <a href="mailto:support@medical-system.com" class="btn btn-sm btn-primary w-100 rounded-pill">تواصل معنا</a>
        </div>
        <div class="text-center mt-3 text-muted small" style="font-size: 0.75rem">© 2025 - النظام الطبي</div>
    </div>
</aside>

<!-- Offcanvas sidebar (mobile) -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarOffcanvas">
    <div class="offcanvas-header border-bottom">
        <div class="d-flex align-items-center gap-2 text-primary fw-bold fs-5">
            <i class="fas fa-heartbeat"></i>
            <span>النظام الطبي</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0">
        <nav class="nav flex-column p-3 gap-1">
            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                <i class="fas fa-home"></i><span>الرئيسية</span>
            </a>
            <a class="nav-link {{ request()->routeIs('chat.*') ? 'active' : '' }}" href="{{ route('chat.index') }}">
                <i class="fas fa-comments"></i><span>المحادثات</span>
            </a>
            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                <i class="fas fa-users"></i><span>المستخدمين</span>
            </a>
            <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}"
                href="{{ route('settings.index') }}">
                <i class="fas fa-cog"></i><span>الإعدادات</span>
            </a>
        </nav>

        <div class="p-4 mt-auto border-top">
            <div class="p-3 bg-light rounded-3">
                <div class="small fw-bold mb-1">تحتاج مساعدة؟</div>
                <div class="text-muted small mb-2" style="font-size: 0.8rem">تواصل مع الدعم الفني</div>
                <a href="mailto:support@medical-system.com" class="btn btn-sm btn-primary w-100 rounded-pill">تواصل
                    معنا</a>
            </div>
            <div class="text-center mt-3 text-muted small" style="font-size: 0.75rem">© 2025 - النظام الطبي</div>
        </div>
    </div>
</div>
