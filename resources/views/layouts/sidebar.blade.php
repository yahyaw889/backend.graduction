<!-- Sidebar (desktop) -->
<aside class="sidebar d-none d-lg-block" id="desktopSidebar">
    <div class="logo"><i class="fas fa-heartbeat me-2"></i>النظام الطبي</div>
    <nav class="nav flex-column mt-3 px-1">
        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}"><i
                class="fas fa-home"></i><span>الرئيسية</span></a>
        <a class="nav-link {{ request()->routeIs('chat.*') ? 'active' : '' }}" href="{{ route('chat.index') }}"><i
                class="fas fa-comments"></i><span>المحادثات</span></a>
        <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}"><i
                class="fas fa-users"></i><span>المستخدمين</span></a>
        <a class="nav-link" href="#"><i class="fas fa-cog"></i><span>الإعدادات</span></a>
    </nav>

    <div class="mt-auto p-3 small-muted">© 2025 - النظام الطبي</div>
</aside>

<!-- Offcanvas sidebar (mobile) -->
<div class="offcanvas offcanvas-end offcanvas-lg" tabindex="-1" id="sidebarOffcanvas">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">النظام الطبي</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}"><i
                    class="fas fa-home"></i><span>الرئيسية</span></a>
            <a class="nav-link {{ request()->routeIs('chat.*') ? 'active' : '' }}" href="{{ route('chat.index') }}"><i
                    class="fas fa-comments"></i><span>المحادثات</span></a>
            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
                href="{{ route('users.index') }}"><i class="fas fa-users"></i><span>المستخدمين</span></a>
            <a class="nav-link" href="#"><i class="fas fa-cog"></i><span>الإعدادات</span></a>
        </nav>
    </div>
</div>
