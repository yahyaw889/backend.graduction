<nav class="topbar">
    <!-- Mobile toggle -->
    <button class="icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas"
        data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas" id="sidebarToggle">
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
        <!-- Theme toggle -->
        <div class="icon-btn" id="themeToggle" title="Toggle dark mode">
            <i class="fas fa-moon"></i>
        </div>

        <!-- Language Switcher -->
        <div class="dropdown">
            <div class="icon-btn" data-bs-toggle="dropdown" title="Change Language">
                <i class="fas fa-globe"></i>
            </div>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item {{ app()->getLocale() == 'ar' ? 'active' : '' }}" href="{{ route('lang.switch', 'ar') }}">العربية 🇪🇬</a></li>
                <li><a class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}" href="{{ route('lang.switch', 'en') }}">English 🇺🇸</a></li>
            </ul>
        </div>

        <!-- Notifications -->
        <div class="dropdown">
            <div class="icon-btn position-relative" data-bs-toggle="dropdown" title="Notifications">
                <i class="fas fa-bell"></i>
                @php $unreadNotifications = auth()->user()->unreadNotifications->count(); @endphp
                @if($unreadNotifications > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.55rem; padding:0.25em 0.4em;">
                        {{ $unreadNotifications > 99 ? '99+' : $unreadNotifications }}
                    </span>
                @endif
            </div>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="min-width:300px; padding:0;">
                <li class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                    <span class="fw-bold" style="font-size:.85rem;">Notifications</span>
                    @if($unreadNotifications > 0)
                        <span class="badge bg-primary rounded-pill">{{ $unreadNotifications }}</span>
                    @endif
                </li>
                <div class="overflow-auto" style="max-height: 300px;">
                    @forelse(auth()->user()->unreadNotifications->take(5) as $notification)
                        <a href="#" class="dropdown-item px-3 py-2 border-bottom text-wrap" style="font-size: .8rem;">
                            <div class="fw-bold">{{ $notification->data['title'] ?? 'Notification' }}</div>
                            <div class="text-muted" style="font-size:.75rem;">{{ $notification->data['message'] ?? '' }}</div>
                            <div class="text-muted" style="font-size:.7rem; margin-top:2px;">{{ $notification->created_at->diffForHumans() }}</div>
                        </a>
                    @empty
                        <li><div class="px-3 py-4 text-center text-muted" style="font-size:.82rem;">{{ __('dashboard.no_notifications') }}</div></li>
                    @endforelse
                </div>
            </ul>
        </div>

        <!-- User dropdown -->
        <div class="dropdown">
            <div class="d-flex align-items-center gap-2" role="button" data-bs-toggle="dropdown" style="cursor:pointer;">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Admin') }}&background=4f46e5&color=fff&size=64"
                    class="rounded-circle" width="34" height="34" alt="avatar" style="object-fit:cover;">
                <span class="d-none d-lg-block" style="font-size:.85rem; font-weight:600;">{{ Auth::user()->name ?? 'Admin' }}</span>
                <i class="fas fa-chevron-down d-none d-lg-block" style="font-size:.65rem; color:var(--text-muted);"></i>
            </div>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="{{ route('settings.index') }}">
                        <i class="fas fa-user mx-2" style="width:16px; color:var(--text-muted);"></i> {{ __('dashboard.my_profile') }}
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('settings.index') }}">
                        <i class="fas fa-cog mx-2" style="width:16px; color:var(--text-muted);"></i> {{ __('dashboard.settings') }}
                    </a>
                </li>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="fas fa-sign-out-alt mx-2" style="width:16px;"></i> {{ __('dashboard.logout') }}
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
