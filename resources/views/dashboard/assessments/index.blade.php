@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('dashboard.assessments') }}</h1>
        <div class="page-subtitle">Review patient skin assessments and provide recommendations</div>
    </div>
</div>

{{-- Filters --}}
<div class="card mb-3">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('assessments.index') }}" class="row g-2 align-items-end">
            <div class="col-12 col-md-5">
                <label class="form-label mb-1" style="font-size:.78rem; font-weight:600; color:var(--text-muted); text-transform:uppercase;">Search Patient</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Name or email…" value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label mb-1" style="font-size:.78rem; font-weight:600; color:var(--text-muted); text-transform:uppercase;">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Pending Review</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div class="col-6 col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm px-3"><i class="fas fa-filter me-1"></i> Filter</button>
                <a href="{{ route('assessments.index') }}" class="btn btn-outline-secondary btn-sm px-3">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4" style="width:60px;">ID</th>
                        <th>Patient</th>
                        <th>Predicted Condition</th>
                        <th>Risk Level</th>
                        <th>Status</th>
                        <th>Submitted At</th>
                        <th class="pe-4 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assessments as $assessment)
                        <tr>
                            <td class="ps-4 text-muted fw-bold" style="font-size:.85rem;">#{{ $assessment->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar bg-primary bg-opacity-10 text-primary" style="width:32px;height:32px;font-size:.75rem;">
                                        {{ strtoupper(substr($assessment->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold" style="font-size:.85rem;">{{ $assessment->user->name ?? 'Unknown' }}</div>
                                        <div class="text-muted" style="font-size:.75rem;">{{ $assessment->user->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold" style="font-size:.85rem;">{{ $assessment->prediction ?? 'Analyzing...' }}</span>
                                @if($assessment->confidence)
                                    <div class="text-muted" style="font-size:.7rem;">Conf: {{ number_format($assessment->confidence, 2) }}%</div>
                                @endif
                            </td>
                            <td>
                                @php
                                    $riskColor = match(strtolower($assessment->risk_level ?? '')) {
                                        'high'   => 'danger',
                                        'medium' => 'warning',
                                        'low'    => 'success',
                                        default  => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $riskColor }} bg-opacity-10 text-{{ $riskColor }}" style="font-size:.7rem;">
                                    {{ ucfirst($assessment->risk_level ?? 'Unknown') }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $statusColor = $assessment->status === 'completed' ? 'success' : 'warning';
                                @endphp
                                <span class="badge bg-{{ $statusColor }} text-white shadow-sm" style="font-size:.7rem;">
                                    {{ ucfirst($assessment->status ?? 'pending') }}
                                </span>
                            </td>
                            <td class="text-muted" style="font-size:.8rem;">
                                {{ $assessment->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('assessments.show', $assessment->id) }}" class="btn btn-sm btn-outline-primary" style="border-radius:6px;">
                                    <i class="fas fa-microscope me-1"></i> Review
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-clipboard-check mb-3 d-block" style="font-size:2.5rem; opacity:.3;"></i>
                                No assessments found matching criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($assessments->hasPages())
            <div class="px-4 py-3 border-top bg-light">
                {{ $assessments->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

@endsection
