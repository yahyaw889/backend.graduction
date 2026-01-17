    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container-fluid px-4">
            <button class="btn btn-light d-lg-none me-2" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas" style="border-radius: 8px;">
                <i class="fas fa-bars"></i>
            </button>


            <div class="ms-auto d-flex align-items-center gap-3">

                <div class="dropdown mx-3">
                    <a class="dropdown-toggle d-flex align-items-center text-decoration-none" data-bs-toggle="dropdown"
                        style="cursor: pointer;">
                        <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'Admin' }}&background=367ac2&color=fff"
                            class="rounded-circle mx-2" width="42" alt="avatar" />
                        <span class="me-2 d-none d-lg-inline">{{ Auth::user()->name ?? 'Admin' }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" style="border-radius: 12px;">
                        <li><a class="dropdown-item" href="{{ route('settings.index') }}#profile"><i
                                    class="fas fa-user me-2"></i>الملف الشخصي</a>
                        </li>
                        <li><a class="dropdown-item" href="{{ route('settings.index') }}"><i
                                    class="fas fa-cog me-2"></i>الإعدادات</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i>تسجيل الخروج
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>

                <div class="theme-toggle " id="themeToggle" title="تبديل الوضع الداكن"><i class="fas fa-moon"></i></div>
            </div>
        </div>
    </nav>
