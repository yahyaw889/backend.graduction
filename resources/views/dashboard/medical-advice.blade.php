@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">Medical Advice</h1>
        <div class="page-subtitle">Manage health tips and medical advice shown to patients</div>
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createAdviceModal">
        <i class="fas fa-plus me-1"></i> Add Advice
    </button>
</div>

{{-- Filters --}}
<div class="card mb-3">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('medical-advice.index') }}" class="row g-2 align-items-end">
            <div class="col-12 col-md-5">
                <label class="form-label mb-1" style="font-size:.78rem; font-weight:600; color:var(--text-muted); text-transform:uppercase;">Search</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Title or description…" value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1" style="font-size:.78rem; font-weight:600; color:var(--text-muted); text-transform:uppercase;">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-6 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm px-3"><i class="fas fa-filter me-1"></i> Filter</button>
                <a href="{{ route('medical-advice.index') }}" class="btn btn-outline-secondary btn-sm px-3">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Advice Table --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" style="width:50px;">#</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="pe-4 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($advices as $advice)
                        <tr>
                            <td class="ps-4 text-muted" style="font-size:.8rem;">{{ $advice->id }}</td>
                            <td>
                                <div class="fw-600" style="font-weight:600; font-size:.875rem;">{{ $advice->title }}</div>
                            </td>
                            <td>
                                <div class="text-muted text-truncate" style="max-width:320px; font-size:.855rem;">{{ $advice->desc }}</div>
                            </td>
                            <td>
                                <span class="badge {{ $advice->status ? 'bg-success bg-opacity-10 text-success' : 'bg-secondary bg-opacity-10 text-secondary' }}" style="font-size:.7rem;">
                                    {{ $advice->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td style="font-size:.855rem; color:var(--text-muted);">{{ $advice->created_at->format('M d, Y') }}</td>
                            <td class="pe-4 text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <button class="btn btn-sm btn-outline-primary"
                                        onclick="editAdvice({{ $advice->id }}, '{{ addslashes($advice->title) }}', '{{ addslashes($advice->desc) }}', {{ $advice->status ? 'true' : 'false' }})"
                                        style="border-radius:6px; font-size:.78rem; padding:.25rem .6rem;">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="deleteAdvice({{ $advice->id }}, '{{ addslashes($advice->title) }}')"
                                        style="border-radius:6px; font-size:.78rem; padding:.25rem .6rem;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-stethoscope mb-2 d-block" style="font-size:2rem; opacity:.3;"></i>
                                No medical advice found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($advices->hasPages())
            <div class="px-4 py-3 border-top">
                {{ $advices->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Create Modal --}}
<div class="modal fade" id="createAdviceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form action="{{ route('medical-advice.store') }}" method="POST">
            @csrf
            <div class="modal-content border-0 shadow" style="border-radius: 16px; overflow:hidden;">
                <div class="modal-header border-0 px-4 py-3" style="background: rgba(79, 70, 229, 0.04);">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar bg-primary text-white shadow-sm" style="width: 32px; height: 32px;"><i class="fas fa-stethoscope" style="font-size: .8rem;"></i></div>
                        <h5 class="modal-title fw-bold mb-0" style="font-size: 1.05rem;">Add Medical Advice</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label" style="font-size:.82rem; font-weight:600; color:var(--text-muted);">Advice Title</label>
                            <input type="text" name="title" class="form-control form-control-lg" style="font-size:.9rem; border-radius:10px;" required placeholder="e.g. Daily Skincare Routine">
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-size:.82rem; font-weight:600; color:var(--text-muted);">Detailed Description</label>
                            <textarea name="desc" class="form-control" rows="5" style="font-size:.9rem; border-radius:10px; resize:none;" required placeholder="Write the advice content here..."></textarea>
                        </div>
                        <div class="col-12">
                            <div class="d-flex align-items-center justify-content-between p-3 rounded-3 mt-1" style="background:var(--body-bg); border:1px solid var(--border-color);">
                                <div>
                                    <div class="fw-bold" style="font-size:.9rem;">Status Publication</div>
                                    <div class="text-muted" style="font-size:.75rem;">Make this advice visible to patients immediately</div>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="status" value="1" id="createStatus" checked style="width: 2.5rem; height: 1.25rem;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 py-3 bg-light">
                    <button type="button" class="btn btn-outline-secondary px-4 shadow-sm" style="border-radius:10px;" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm" style="border-radius:10px;">
                        <i class="fas fa-check-circle me-1"></i> Create Advice
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editAdviceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form id="editAdviceForm" method="POST">
            @csrf @method('PUT')
            <div class="modal-content border-0 shadow" style="border-radius: 16px; overflow:hidden;">
                <div class="modal-header border-0 px-4 py-3" style="background: rgba(16, 185, 129, 0.04);">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar bg-success text-white shadow-sm" style="width: 32px; height: 32px;"><i class="fas fa-edit" style="font-size: .8rem;"></i></div>
                        <h5 class="modal-title fw-bold mb-0" style="font-size: 1.05rem;">Edit Medical Advice</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label" style="font-size:.82rem; font-weight:600; color:var(--text-muted);">Advice Title</label>
                            <input type="text" name="title" id="edit_title" class="form-control form-control-lg" style="font-size:.9rem; border-radius:10px;" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-size:.82rem; font-weight:600; color:var(--text-muted);">Detailed Description</label>
                            <textarea name="desc" id="edit_desc" class="form-control" rows="5" style="font-size:.9rem; border-radius:10px; resize:none;" required></textarea>
                        </div>
                        <div class="col-12">
                            <div class="d-flex align-items-center justify-content-between p-3 rounded-3 mt-1" style="background:var(--body-bg); border:1px solid var(--border-color);">
                                <div>
                                    <div class="fw-bold" style="font-size:.9rem;">Status Publication</div>
                                    <div class="text-muted" style="font-size:.75rem;">Make this advice visible to patients immediately</div>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="status" value="1" id="edit_status" style="width: 2.5rem; height: 1.25rem;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 py-3 bg-light">
                    <button type="button" class="btn btn-outline-secondary px-4 shadow-sm" style="border-radius:10px;" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4 shadow-sm" style="border-radius:10px; color:#fff;">
                        <i class="fas fa-save me-1"></i> Update Advice
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Delete Modal --}}
<div class="modal fade" id="deleteAdviceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="deleteAdviceForm" method="POST">
            @csrf @method('DELETE')
            <div class="modal-content border-0 shadow" style="border-radius: 16px; overflow:hidden;">
                <div class="modal-header border-0 px-4 py-3 bg-danger bg-opacity-10">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar bg-danger text-white shadow-sm" style="width: 32px; height: 32px;"><i class="fas fa-exclamation-triangle" style="font-size: .8rem;"></i></div>
                        <h5 class="modal-title fw-bold text-danger mb-0" style="font-size: 1.05rem;">Delete Advice</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 pt-4 text-center">
                    <i class="fas fa-trash-alt text-danger mb-3" style="font-size: 3rem; opacity:.2;"></i>
                    <p class="mb-0" style="font-size: 1rem;">Are you sure you want to delete <strong id="delete_advice_title"></strong>?</p>
                    <p class="text-muted mt-2" style="font-size: .85rem;">This action will permanently remove this medical advice.</p>
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
    function editAdvice(id, title, desc, status) {
        document.getElementById('editAdviceForm').action = '/medical-advice/' + id;
        document.getElementById('edit_title').value = title;
        document.getElementById('edit_desc').value  = desc;
        document.getElementById('edit_status').checked = status;
        new bootstrap.Modal(document.getElementById('editAdviceModal')).show();
    }

    function deleteAdvice(id, title) {
        document.getElementById('deleteAdviceForm').action = '/medical-advice/' + id;
        document.getElementById('delete_advice_title').textContent = title;
        new bootstrap.Modal(document.getElementById('deleteAdviceModal')).show();
    }
</script>
@endpush

@endsection
