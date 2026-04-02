@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">User Management</h1>
        <div class="page-subtitle">Manage all registered users — patients, doctors, and admins</div>
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createUserModal">
        <i class="fas fa-plus me-1"></i> Add User
    </button>
</div>

{{-- Filters --}}
<div class="card mb-3">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('users.index') }}" class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label mb-1" style="font-size:.78rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.05em;">Search</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Name or email…" value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1" style="font-size:.78rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.05em;">Role</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="">All Roles</option>
                    <option value="patient"  {{ request('type') === 'patient'  ? 'selected' : '' }}>Patient</option>
                    <option value="doctor"   {{ request('type') === 'doctor'   ? 'selected' : '' }}>Doctor</option>
                    <option value="admin"    {{ request('type') === 'admin'    ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1" style="font-size:.78rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.05em;">Per Page</label>
                <select name="per_page" class="form-select form-select-sm">
                    <option value="10" {{ request('per_page','10') == '10' ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                </select>
            </div>
            <div class="col-12 col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm px-3">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm px-3">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Users Table --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" style="width:50px;">#</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th class="pe-4 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td class="ps-4 text-muted" style="font-size:.8rem;">{{ $user->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar bg-primary bg-opacity-10 text-primary" style="font-size:.8rem;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="fw-600" style="font-weight:600; font-size:.875rem;">{{ $user->name }}</div>
                                </div>
                            </td>
                            <td style="font-size:.855rem;">{{ $user->email }}</td>
                            <td>
                                @php
                                    $roleClass = match($user->type ?? 'patient') {
                                        'admin'  => 'danger',
                                        'doctor' => 'info',
                                        default  => 'success',
                                    };
                                @endphp
                                <span class="badge bg-{{ $roleClass }} bg-opacity-10 text-{{ $roleClass }}" style="font-size:.7rem;">
                                    {{ ucfirst($user->type ?? 'patient') }}
                                </span>
                            </td>
                            <td style="font-size:.855rem; color:var(--text-muted);">{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="pe-4 text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <button class="btn btn-sm btn-outline-primary"
                                        onclick="editUser({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->email }}', '{{ $user->type ?? 'patient' }}')"
                                        style="border-radius:6px; font-size:.78rem; padding:.25rem .6rem;">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="deleteUser({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                        style="border-radius:6px; font-size:.78rem; padding:.25rem .6rem;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-users mb-2 d-block" style="font-size:2rem; opacity:.3;"></i>
                                No users found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($users->hasPages())
            <div class="px-4 py-3 border-top">
                {{ $users->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Create Modal --}}
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="modal-content border-0 shadow" style="border-radius: 16px; overflow:hidden;">
                <div class="modal-header border-0 px-4 py-3" style="background: rgba(79, 70, 229, 0.04);">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar bg-primary text-white shadow-sm" style="width: 32px; height: 32px;"><i class="fas fa-user-plus" style="font-size: .8rem;"></i></div>
                        <h5 class="modal-title fw-bold mb-0" style="font-size: 1.05rem;">Add New User</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:.82rem; font-weight:600; color:var(--text-muted);">Full Name</label>
                            <input type="text" name="name" class="form-control form-control-lg" style="font-size:.9rem; border-radius:10px;" required placeholder="e.g. John Doe">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:.82rem; font-weight:600; color:var(--text-muted);">Email Address</label>
                            <input type="email" name="email" class="form-control form-control-lg" style="font-size:.9rem; border-radius:10px;" required placeholder="user@example.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:.82rem; font-weight:600; color:var(--text-muted);">Password</label>
                            <input type="password" name="password" class="form-control form-control-lg" style="font-size:.9rem; border-radius:10px;" required placeholder="••••••••">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:.82rem; font-weight:600; color:var(--text-muted);">Role</label>
                            <select name="type" class="form-select form-select-lg" style="font-size:.9rem; border-radius:10px;">
                                <option value="patient">Patient</option>
                                <option value="doctor">Doctor</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 py-3 bg-light">
                    <button type="button" class="btn btn-outline-secondary px-4 shadow-sm" style="border-radius:10px;" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm" style="border-radius:10px;">
                        <i class="fas fa-check-circle me-1"></i> Create User
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form id="editUserForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content border-0 shadow" style="border-radius: 16px; overflow:hidden;">
                <div class="modal-header border-0 px-4 py-3" style="background: rgba(16, 185, 129, 0.04);">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar bg-success text-white shadow-sm" style="width: 32px; height: 32px;"><i class="fas fa-user-edit" style="font-size: .8rem;"></i></div>
                        <h5 class="modal-title fw-bold mb-0" style="font-size: 1.05rem;">Edit User</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:.82rem; font-weight:600; color:var(--text-muted);">Full Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control form-control-lg" style="font-size:.9rem; border-radius:10px;" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:.82rem; font-weight:600; color:var(--text-muted);">Email Address</label>
                            <input type="email" name="email" id="edit_email" class="form-control form-control-lg" style="font-size:.9rem; border-radius:10px;" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:.82rem; font-weight:600; color:var(--text-muted);">New Password <span class="fw-normal" style="font-size:.7rem;">(optional)</span></label>
                            <input type="password" name="password" class="form-control form-control-lg" style="font-size:.9rem; border-radius:10px;" placeholder="Leave blank to keep current">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:.82rem; font-weight:600; color:var(--text-muted);">Role</label>
                            <select name="type" id="edit_type" class="form-select form-select-lg" style="font-size:.9rem; border-radius:10px;">
                                <option value="patient">Patient</option>
                                <option value="doctor">Doctor</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 py-3 bg-light">
                    <button type="button" class="btn btn-outline-secondary px-4 shadow-sm" style="border-radius:10px;" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4 shadow-sm" style="border-radius:10px; color:#fff;">
                        <i class="fas fa-save me-1"></i> Update User
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Delete Modal --}}
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="deleteUserForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-content border-0 shadow" style="border-radius: 16px; overflow:hidden;">
                <div class="modal-header border-0 px-4 py-3 bg-danger bg-opacity-10">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar bg-danger text-white shadow-sm" style="width: 32px; height: 32px;"><i class="fas fa-exclamation-triangle" style="font-size: .8rem;"></i></div>
                        <h5 class="modal-title fw-bold text-danger mb-0" style="font-size: 1.05rem;">Delete User</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 pt-4 text-center">
                    <i class="fas fa-trash-alt text-danger mb-3" style="font-size: 3rem; opacity:.2;"></i>
                    <p class="mb-0" style="font-size: 1rem;">Are you sure you want to delete <strong id="delete_user_name"></strong>?</p>
                    <p class="text-muted mt-2" style="font-size: .85rem;">This action cannot be undone and will remove all their data.</p>
                </div>
                <div class="modal-footer border-0 px-4 py-3 bg-light">
                    <button type="button" class="btn btn-outline-secondary px-4 shadow-sm" style="border-radius:10px;" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger px-4 shadow-sm" style="border-radius:10px;">
                        <i class="fas fa-trash me-1"></i> Delete Permanent
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function editUser(id, name, email, type) {
        document.getElementById('editUserForm').action = '/users/' + id;
        document.getElementById('edit_name').value  = name;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_type').value  = type;
        new bootstrap.Modal(document.getElementById('editUserModal')).show();
    }

    function deleteUser(id, name) {
        document.getElementById('deleteUserForm').action = '/users/' + id;
        document.getElementById('delete_user_name').textContent = name;
        new bootstrap.Modal(document.getElementById('deleteUserModal')).show();
    }
</script>
@endpush

@endsection
