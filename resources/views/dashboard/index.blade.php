@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('dashboard.dashboard_overview') }}</h1>
        <div class="page-subtitle">{{ __('dashboard.dashboard_sub') }}</div>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
            <i class="fas fa-print me-1"></i> {{ __('dashboard.print_report') }}
        </button>
        <a href="{{ route('users.index') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-users me-1"></i> {{ __('dashboard.manage_users') }}
        </a>
    </div>
</div>

{{-- Metric Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card metric-card" style="color: var(--primary);">
            <div class="metric-label">{{ __('dashboard.total_patients') }}</div>
            <div class="metric-value" style="color:var(--text-main);">{{ $stats['total_users'] }}</div>
            <div class="metric-sub" style="color:var(--success);"><i class="fas fa-arrow-up mx-1"></i>{{ __('dashboard.registered_users') }}</div>
            <div class="metric-icon-bg" style="background: rgba(79,70,229,.12); color:var(--primary);">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card metric-card" style="color: var(--info);">
            <div class="metric-label">{{ __('dashboard.total_assessments') }}</div>
            <div class="metric-value" style="color:var(--text-main);">{{ $stats['total_assessments'] }}</div>
            <div class="metric-sub" style="color:var(--info);"><i class="fas fa-notes-medical mx-1"></i>{{ __('dashboard.all_time') }}</div>
            <div class="metric-icon-bg" style="background: rgba(6,182,212,.12); color:var(--info);">
                <i class="fas fa-notes-medical"></i>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card metric-card" style="color: var(--warning);">
            <div class="metric-label">{{ __('dashboard.pending_review') }}</div>
            <div class="metric-value" style="color:var(--text-main);">{{ $stats['pending_assessments'] }}</div>
            <div class="metric-sub" style="color:var(--warning);"><i class="fas fa-clock mx-1"></i>{{ __('dashboard.awaiting_analysis') }}</div>
            <div class="metric-icon-bg" style="background: rgba(245,158,11,.12); color:var(--warning);">
                <i class="fas fa-hourglass-half"></i>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card metric-card" style="color: var(--danger);">
            <div class="metric-label">{{ __('dashboard.critical_cases') }}</div>
            <div class="metric-value" style="color:var(--text-main);">{{ $stats['critical_cases'] }}</div>
            <div class="metric-sub" style="color:var(--danger);"><i class="fas fa-exclamation-triangle mx-1"></i>{{ __('dashboard.need_follow_up') }}</div>
            <div class="metric-icon-bg" style="background: rgba(239,68,68,.12); color:var(--danger);">
                <i class="fas fa-heartbeat"></i>
            </div>
        </div>
    </div>
</div>

{{-- Charts + Recent Feed --}}
<div class="row g-3 mb-4">
    {{-- Chart --}}
    <div class="col-12 col-xl-8">
        <div class="card h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="mb-0 fw-bold">Assessment Analytics</h6>
                        <div class="text-muted" style="font-size:.78rem;">Diagnoses per day — last 30 days</div>
                    </div>
                    <select class="form-select form-select-sm w-auto" id="chartRangeSelect">
                        <option value="30">Last 30 days</option>
                        <option value="7">Last 7 days</option>
                    </select>
                </div>
                <div style="height: 260px;">
                    <canvas id="assessmentChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Assessments --}}
    <div class="col-12 col-xl-4">
        <div class="card h-100">
            <div class="card-body p-4">
                <h6 class="mb-3 fw-bold">{{ __('dashboard.recent_assessments') }}</h6>
                <div class="d-flex flex-column gap-3">
                    @forelse ($recentAssessments as $assessment)
                        <div class="d-flex align-items-start gap-3">
                            <div class="avatar bg-primary bg-opacity-10 text-primary" style="font-size:.8rem;">
                                {{ strtoupper(mb_substr($assessment->user->name ?? 'U', 0, 1, 'UTF-8')) }}
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-600 text-truncate" style="font-size:.85rem; font-weight:600; max-width:120px;">
                                        {{ $assessment->user->name ?? 'Unknown' }}
                                    </span>
                                    @php
                                        $statusClass = match($assessment->status) {
                                            'completed' => 'success',
                                            'pending'   => 'warning',
                                            default     => 'secondary',
                                        };
                                        $statusKey = $assessment->status === 'completed' ? 'dashboard.completed' : 'dashboard.pending';
                                    @endphp
                                    <span class="badge bg-{{ $statusClass }} bg-opacity-10 text-{{ $statusClass }}" style="font-size:.68rem;">
                                        {{ __($statusKey) }}
                                    </span>
                                </div>
                                <div class="text-muted text-truncate" style="font-size:.77rem; max-width:180px;">
                                    {{ $assessment->recommendation ?? __('dashboard.no_recommendation') }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-3" style="font-size:.85rem;">
                            <i class="fas fa-inbox mb-2 d-block" style="font-size:1.5rem; opacity:.4;"></i>
                            {{ __('dashboard.no_recent_assessments') }}
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Recent Users Table --}}
<div class="card">
    <div class="card-body p-0">
        <div class="d-flex justify-content-between align-items-center p-4 pb-0 mb-3">
            <h6 class="mb-0 fw-bold">{{ __('dashboard.recently_registered') }}</h6>
            <a href="{{ route('users.index') }}" class="btn btn-outline-primary btn-sm">{{ __('dashboard.view_all') }}</a>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th class="pe-4 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recentUsers as $user)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar bg-primary bg-opacity-10 text-primary" style="font-size:.8rem;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-600" style="font-weight:600; font-size:.875rem;">{{ $user->name }}</div>
                                        <div class="text-muted" style="font-size:.75rem;">ID #{{ $user->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="font-size:.855rem;">{{ $user->email }}</td>
                            <td>
                                @php
                                    $roleClass = match($user->type ?? 'patient') {
                                        'admin'   => 'danger',
                                        'doctor'  => 'info',
                                        default   => 'success',
                                    };
                                @endphp
                                <span class="badge bg-{{ $roleClass }} bg-opacity-10 text-{{ $roleClass }}" style="font-size:.7rem;">
                                    {{ ucfirst($user->type ?? 'patient') }}
                                </span>
                            </td>
                            <td style="font-size:.855rem; color:var(--text-muted);">{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:6px; font-size:.78rem;">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('assessmentChart');
    const dailyDiagnoses = @json($dailyDiagnoses);

    const labels = Object.keys(dailyDiagnoses);
    const data   = Object.values(dailyDiagnoses);

    let chartInst = null;

    function buildChart(labels, data) {
        if (chartInst) chartInst.destroy();

        chartInst = new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Assessments',
                    data,
                    tension: 0.4,
                    fill: true,
                    backgroundColor: (ctx) => {
                        const g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 260);
                        g.addColorStop(0, 'rgba(79,70,229,.25)');
                        g.addColorStop(1, 'rgba(79,70,229,.01)');
                        return g;
                    },
                    borderColor: '#4f46e5',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#4f46e5',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(15,23,42,.9)',
                        titleColor: '#f1f5f9',
                        bodyColor: '#94a3b8',
                        borderColor: '#1e293b',
                        borderWidth: 1,
                        padding: 10,
                        titleFont: { family: 'Inter', size: 13 },
                        bodyFont: { family: 'Inter', size: 12 },
                        displayColors: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,.04)', drawBorder: false },
                        ticks: { font: { family: 'Inter', size: 11 }, color: '#64748b' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Inter', size: 11 }, color: '#64748b' }
                    }
                },
                interaction: { mode: 'nearest', axis: 'x', intersect: false }
            }
        });
    }

    buildChart(labels, data);
</script>
@endpush
