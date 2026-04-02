<!-- Sidebar (desktop) -->
<aside class="sidebar d-none d-lg-flex flex-column" id="desktopSidebar">
    <!-- Brand -->
    <a href="{{ route('home') }}" class="sidebar-brand text-decoration-none">
        <div class="brand-icon">
            <i class="fas fa-heartbeat"></i>
        </div>
        <span>MediScan AI</span>
    </a>

    <!-- Navigation -->
    <div class="sidebar-section">
        <div class="sidebar-section-label">{{ __('dashboard.main') }}</div>
        <nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                <span class="nav-icon"><i class="fas fa-home"></i></span>
                <span>{{ __('dashboard.dashboard') }}</span>
            </a>
            <a class="nav-link {{ request()->routeIs('chat.*') ? 'active' : '' }}" href="{{ route('chat.index') }}">
                <span class="nav-icon"><i class="fas fa-comments"></i></span>
                <span>{{ __('dashboard.messages') }}</span>
                @php $unread = \App\Models\Message::where('is_read', false)->count(); @endphp
                @if($unread > 0)
                    <span class="badge bg-primary ms-auto" style="font-size:.68rem;">{{ $unread }}</span>
                @endif
            </a>
        </nav>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-section-label">{{ __('dashboard.management') }}</div>
        <nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                <span class="nav-icon"><i class="fas fa-users"></i></span>
                <span>{{ __('dashboard.users') }}</span>
            </a>
            <a class="nav-link {{ request()->routeIs('medical-advice.*') ? 'active' : '' }}" href="{{ route('medical-advice.index') }}">
                <span class="nav-icon"><i class="fas fa-stethoscope"></i></span>
                <span>{{ __('dashboard.medical_advice') }}</span>
            </a>
            <a class="nav-link {{ request()->routeIs('assessments.*') ? 'active' : '' }}" href="{{ route('assessments.index') ?? '#' }}">
                <span class="nav-icon"><i class="fas fa-notes-medical"></i></span>
                <span>{{ __('dashboard.assessments') }}</span>
            </a>
            <a class="nav-link {{ request()->routeIs('contact-messages.*') ? 'active' : '' }}" href="{{ route('contact-messages.index') ?? '#' }}">
                <span class="nav-icon"><i class="fas fa-envelope"></i></span>
                <span>{{ __('dashboard.contact_messages') }}</span>
            </a>
        </nav>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-section-label">{{ __('dashboard.system') }}</div>
        <nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}">
                <span class="nav-icon"><i class="fas fa-cog"></i></span>
                <span>{{ __('dashboard.settings') }}</span>
            </a>
        </nav>
    </div>

    <!-- Footer -->
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Admin') }}&background=4f46e5&color=fff&size=64"
                 class="user-avatar" alt="avatar">
            <div>
                <div class="user-name">{{ Auth::user()->name ?? 'Admin' }}</div>
                <div class="user-role">{{ ucfirst(Auth::user()->type ?? 'Admin') }}</div>
            </div>
        </div>
        <div class="text-center mt-2 text-muted" style="font-size:.7rem;">© 2025 MediScan AI</div>
    </div>
</aside>

<!-- Mobile Offcanvas Sidebar -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarOffcanvas" style="width: var(--sidebar-width);">
    <div class="offcanvas-header border-bottom">
        <div class="d-flex align-items-center gap-2 text-primary fw-bold">
            <div class="brand-icon"><i class="fas fa-heartbeat" style="color:#fff;"></i></div>
            <span>MediScan AI</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0 d-flex flex-column">
        <div class="sidebar-section">
            <div class="sidebar-section-label">{{ __('dashboard.main') }}</div>
            <nav class="nav flex-column">
                <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                    <span class="nav-icon"><i class="fas fa-home"></i></span><span>{{ __('dashboard.dashboard') }}</span>
                </a>
                <a class="nav-link {{ request()->routeIs('chat.*') ? 'active' : '' }}" href="{{ route('chat.index') }}">
                    <span class="nav-icon"><i class="fas fa-comments"></i></span><span>{{ __('dashboard.messages') }}</span>
                </a>
            </nav>
        </div>
        <div class="sidebar-section">
            <div class="sidebar-section-label">{{ __('dashboard.management') }}</div>
            <nav class="nav flex-column">
                <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                    <span class="nav-icon"><i class="fas fa-users"></i></span><span>{{ __('dashboard.users') }}</span>
                </a>
                <a class="nav-link {{ request()->routeIs('medical-advice.*') ? 'active' : '' }}" href="{{ route('medical-advice.index') }}">
                    <span class="nav-icon"><i class="fas fa-stethoscope"></i></span><span>{{ __('dashboard.medical_advice') }}</span>
                </a>
                <a class="nav-link {{ request()->routeIs('assessments.*') ? 'active' : '' }}" href="{{ route('assessments.index') ?? '#' }}">
                    <span class="nav-icon"><i class="fas fa-notes-medical"></i></span><span>{{ __('dashboard.assessments') }}</span>
                </a>
                <a class="nav-link {{ request()->routeIs('contact-messages.*') ? 'active' : '' }}" href="{{ route('contact-messages.index') ?? '#' }}">
                    <span class="nav-icon"><i class="fas fa-envelope"></i></span><span>{{ __('dashboard.contact_messages') }}</span>
                </a>
            </nav>
        </div>
        <div class="sidebar-section">
            <div class="sidebar-section-label">{{ __('dashboard.system') }}</div>
            <nav class="nav flex-column">
                <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}">
                    <span class="nav-icon"><i class="fas fa-cog"></i></span><span>{{ __('dashboard.settings') }}</span>
                </a>
            </nav>
        </div>
        <div class="sidebar-footer mt-auto">
            <div class="sidebar-user">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Admin') }}&background=4f46e5&color=fff&size=64"
                     class="user-avatar" alt="avatar">
                <div>
                    <div class="user-name">{{ Auth::user()->name ?? 'Admin' }}</div>
                    <div class="user-role">{{ ucfirst(Auth::user()->type ?? 'Admin') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
