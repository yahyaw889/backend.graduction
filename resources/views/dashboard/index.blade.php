@extends('layouts.app')

@section('content')

<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div style="font-weight: 700; font-size: 1.1rem; color: #1e3a8a;">
            <i class="far fa-calendar-alt me-2"></i> {{ date('Y-m-d') }}
        </div>
    </div>
    <div class="text-start" style="text-align: right;">
        <h1 class="page-title mb-1" style="font-family: 'Cairo', sans-serif; font-weight: 800; font-size: 1.5rem; color: #1e293b;">لوحة التحكم</h1>
        <div class="page-subtitle text-muted" style="font-size: .85rem;">نظرة عامة على أداء النظام والفحوصات</div>
    </div>
</div>

<div class="row g-3 mb-4">
    <!-- Active Users (Yellow) -->
    <div class="col-6 col-lg-3">
        <div class="card p-4 text-center border-0 shadow-sm h-100" style="border-radius: 12px; background: #fff;">
            <div class="d-flex justify-content-center align-items-center mb-3">
                <span class="text-warning fw-bold d-flex align-items-center gap-2" style="background: rgba(234,179,8,0.1); padding: 5px 12px; border-radius: 8px; font-size: .85rem;">
                    <i class="fas fa-users"></i> إجمالي المستخدمين
                </span>
            </div>
            <div class="fs-3 fw-bold text-dark">{{ $stats['total_users'] ?? 0 }}</div>
            <div class="text-muted" style="font-size: .75rem;">مريض / طبيب</div>
        </div>
    </div>

    <!-- Critical Cases (Red) -->
    <div class="col-6 col-lg-3">
        <div class="card p-4 text-center border-0 shadow-sm h-100" style="border-radius: 12px; background: #fff;">
            <div class="d-flex justify-content-center align-items-center mb-3">
                <span class="text-danger fw-bold d-flex align-items-center gap-2" style="background: rgba(239,68,68,0.1); padding: 5px 12px; border-radius: 8px; font-size: .85rem;">
                    <i class="fas fa-heartbeat"></i> الحالات الحرجة
                </span>
            </div>
            <div class="fs-3 fw-bold text-danger">{{ $stats['critical_cases'] ?? 0 }}</div>
            <div class="text-muted" style="font-size: .75rem;">تحتاج متابعة</div>
        </div>
    </div>

    <!-- AI Diagnoses (Purple) -->
    <div class="col-6 col-lg-3">
        <div class="card p-4 text-center border-0 shadow-sm h-100" style="border-radius: 12px; background: #fff;">
            <div class="d-flex justify-content-center align-items-center mb-3">
                <span class="fw-bold d-flex align-items-center gap-2" style="color: #8b5cf6; background: rgba(139,92,246,0.1); padding: 5px 12px; border-radius: 8px; font-size: .85rem;">
                    <i class="fas fa-robot"></i> الفحوصات الذكية
                </span>
            </div>
            <div class="fs-3 fw-bold text-dark">{{ $stats['total_assessments'] ?? 0 }}</div>
            <div class="text-muted" style="font-size: .75rem;">تشخيص AI</div>
        </div>
    </div>

    <!-- Total Assessments (Green) -->
    <div class="col-6 col-lg-3">
        <div class="card p-4 text-center border-0 shadow-sm h-100" style="border-radius: 12px; background: #fff;">
            <div class="d-flex justify-content-center align-items-center mb-3">
                <span class="text-success fw-bold d-flex align-items-center gap-2" style="background: rgba(16,185,129,0.1); padding: 5px 12px; border-radius: 8px; font-size: .85rem;">
                    <i class="fas fa-chart-line"></i> إجمالي التقييمات
                </span>
            </div>
            <div class="fs-3 fw-bold text-success">{{ $stats['total_assessments'] ?? 0 }}</div>
            <div class="text-muted" style="font-size: .75rem;">تقييم طبي مكتمل</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <!-- Chart Section -->
    <div class="col-12 col-xl-9">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold m-0" style="color: #1e3a8a; border-inline-start: 4px solid #1e3a8a; padding-inline-start: 10px;">تحليل الفحوصات والنمو الشهري</h6>
                    <div class="d-flex gap-3 align-items-center">
                        <div class="d-flex align-items-center gap-2" style="font-size: .75rem; font-weight: 600;">
                            <span style="width: 25px; height: 10px; background: #10b981; border-radius: 2px;"></span> نمو الحالات
                        </div>
                        <div class="d-flex align-items-center gap-2" style="font-size: .75rem; font-weight: 600;">
                            <span style="width: 25px; height: 10px; background: #1e3a8a; border-radius: 2px;"></span> الفحوصات
                        </div>
                    </div>
                </div>
                <div style="height: 280px;">
                    <canvas id="assessmentChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Doctors / Quick Stats -->
    <div class="col-12 col-xl-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-4 pb-2 border-bottom text-center">حالة النظام</h6>
                <div class="text-center mb-4">
                    <div class="fs-1 fw-bold text-primary">{{ $stats['total_users'] ?? 0 }}</div>
                    <div class="text-muted" style="font-size: .85rem;">المستخدمين النشطين</div>
                    <div class="progress mt-2 mx-auto" style="height: 6px; width: 80%; border-radius: 10px;">
                        <div class="progress-bar bg-info" style="width: 75%"></div>
                    </div>
                </div>
                
                <div class="text-center mb-4 mt-5">
                    <div class="fs-1 fw-bold text-success">{{ $stats['total_assessments'] ?? 0 }}</div>
                    <div class="text-muted" style="font-size: .85rem;">إجمالي الفحوصات</div>
                </div>

                <div class="p-3 mt-5 rounded text-center" style="background: rgba(239,68,68,0.05); border: 1px dashed #ef4444;">
                    <i class="fas fa-exclamation-triangle text-danger mb-2 fs-4"></i>
                    <div class="fw-bold text-danger">{{ $stats['pending_assessments'] ?? 0 }} حالة بانتظار المراجعة</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Recent Assessments Table -->
    <div class="col-12 col-xl-7">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-body p-0">
                <div class="d-flex justify-content-between align-items-center p-4 border-bottom">
                    <h6 class="fw-bold m-0" style="color: #1e3a8a; border-inline-start: 4px solid #1e3a8a; padding-inline-start: 10px;">أحدث الفحوصات المسجلة</h6>
                    <button class="btn btn-light btn-sm text-muted fw-bold">عرض الكل</button>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0 text-center" style="font-size: .85rem;">
                        <thead class="bg-light text-muted">
                            <tr>
                                <th>تاريخ التسجيل</th>
                                <th>الحالة</th>
                                <th>المنشأة (المريض)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentAssessments as $assessment)
                                <tr>
                                    <td class="text-muted fw-bold">{{ $assessment->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        @if($assessment->status == 'completed')
                                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">مكتمل</span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">نشط</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold text-primary">
                                        <i class="fas fa-user-circle me-1 text-muted"></i> {{ $assessment->user->name ?? 'غير معروف' }}
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-muted py-4">لا توجد فحوصات حديثة</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="col-12 col-xl-5">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-body p-0">
                <div class="d-flex justify-content-between align-items-center p-4 border-bottom">
                    <h6 class="fw-bold m-0" style="color: #1e3a8a; border-inline-start: 4px solid #1e3a8a; padding-inline-start: 10px;">أخر الحسابات المسجلة</h6>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0 text-center" style="font-size: .85rem;">
                        <thead class="bg-light text-muted">
                            <tr>
                                <th>المبلغ (التقييم)</th>
                                <th>التاريخ</th>
                                <th>البند (الاسم)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentUsers as $user)
                                <tr>
                                    <td class="text-danger fw-bold">{{ $user->type == 'doctor' ? 'طبيب' : 'مريض' }}</td>
                                    <td class="text-muted fw-bold">{{ $user->created_at->format('H:i:s Y-m-d') }}</td>
                                    <td class="fw-bold text-dark">{{ $user->name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
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

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    type: 'line',
                    label: 'النمو',
                    data: data,
                    borderColor: '#10b981',
                    borderWidth: 2,
                    tension: 0.4,
                    pointBackgroundColor: '#10b981',
                    fill: false,
                    yAxisID: 'y'
                },
                {
                    label: 'الإيرادات (الفحوصات)',
                    data: data,
                    backgroundColor: '#1e3a8a',
                    borderRadius: 4,
                    barPercentage: 0.3,
                    yAxisID: 'y'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,.04)' } },
                x: { grid: { display: false } }
            }
        }
    });
</script>
@endpush
