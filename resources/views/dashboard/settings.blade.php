@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">Settings</h1>
        <div class="page-subtitle">Manage your profile and system preferences</div>
    </div>
</div>

<div class="row g-3">
    {{-- Settings Navigation --}}
    <div class="col-12 col-md-3">
        <div class="card p-0 overflow-hidden">
            <div class="list-group list-group-flush" id="settingsTabs">
                <a href="#profile" class="list-group-item list-group-item-action active d-flex align-items-center gap-2" data-bs-toggle="list" style="font-size:.875rem; padding:.8rem 1rem;">
                    <i class="fas fa-user-circle" style="width:18px; color:var(--text-muted);"></i> Profile
                </a>
                <a href="#security" class="list-group-item list-group-item-action d-flex align-items-center gap-2" data-bs-toggle="list" style="font-size:.875rem; padding:.8rem 1rem;">
                    <i class="fas fa-lock" style="width:18px; color:var(--text-muted);"></i> Security
                </a>
            </div>
        </div>
    </div>

    {{-- Settings Content --}}
    <div class="col-12 col-md-9">
        <div class="card">
            <div class="card-body p-4">
                <div class="tab-content">

                    {{-- Profile Settings --}}
                    <div class="tab-pane fade show active" id="profile">
                        <h6 class="fw-bold mb-4">Profile Information</h6>

                        {{-- Avatar --}}
                        <div class="d-flex align-items-center gap-3 mb-4 p-3 rounded-3" style="background: var(--primary-glow);">
                            <div class="avatar bg-primary text-white" style="width:56px; height:56px; font-size:1.3rem;">
                                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-bold">{{ auth()->user()->name ?? 'User' }}</div>
                                <div class="text-muted" style="font-size:.8rem;">{{ ucfirst(auth()->user()->type ?? 'Admin') }}</div>
                            </div>
                        </div>

                        <form action="{{ route('settings.update') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" style="font-size:.82rem; font-weight:600;">Full Name</label>
                                    <input type="text" class="form-control" name="name" value="{{ auth()->user()->name ?? '' }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" style="font-size:.82rem; font-weight:600;">Email Address</label>
                                    <input type="email" class="form-control" name="email" value="{{ auth()->user()->email ?? '' }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" style="font-size:.82rem; font-weight:600;">Phone Number</label>
                                    <input type="tel" class="form-control" name="phone" value="{{ auth()->user()->phone ?? '' }}" placeholder="+1 (555) 000-0000">
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary btn-sm px-4">
                                    <i class="fas fa-save me-2"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Security Settings --}}
                    <div class="tab-pane fade" id="security">
                        <h6 class="fw-bold mb-4">Change Password</h6>
                        <form action="{{ route('settings.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="type" value="password">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label" style="font-size:.82rem; font-weight:600;">Current Password</label>
                                    <input type="password" class="form-control" name="current_password">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" style="font-size:.82rem; font-weight:600;">New Password</label>
                                    <input type="password" class="form-control" name="new_password">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" style="font-size:.82rem; font-weight:600;">Confirm New Password</label>
                                    <input type="password" class="form-control" name="new_password_confirmation">
                                </div>
                            </div>
                            <div class="alert alert-info mt-3 mb-0 d-flex align-items-center gap-2" style="font-size:.82rem; border-radius:8px;">
                                <i class="fas fa-info-circle"></i>
                                Password must be at least 8 characters and contain letters and numbers.
                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary btn-sm px-4">
                                    <i class="fas fa-shield-alt me-2"></i> Update Password
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
