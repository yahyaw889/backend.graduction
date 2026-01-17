@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="mb-1 fw-bold text-gradient">لوحة تحكم النظام الطبي 🏥</h3>
            <div class="text-muted">نظام الكشف عن الأمراض الجلدية - نظرة عامة</div>
        </div>
        <div>
            <button class="btn btn-primary shadow-sm" onclick="window.print()">
                <i class="fas fa-print me-2"></i> طباعة تقرير
            </button>
        </div>
    </div>

    <!-- top metrics -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-lg-6">
            <div class="card metric-card p-4 h-100 border-0">
                <div class="d-flex justify-content-between align-items-start z-1">
                    <div>
                        <div class="metric-label mb-2">إجمالي المرضى</div>
                        <div class="metric-value text-primary">{{ $stats['total_users'] }}</div>
                        <div class="text-success small fw-bold">
                            <i class="fas fa-arrow-up me-1"></i> +12% <span class="text-muted fw-normal ms-1">منذ الشهر
                                الماضي</span>
                        </div>
                    </div>
                </div>
                <i class="fas fa-users metric-icon text-primary"></i>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-6">
            <div class="card metric-card p-4 h-100 border-0">
                <div class="d-flex justify-content-between align-items-start z-1">
                    <div>
                        <div class="metric-label mb-2">الحالات الحرجة</div>
                        <div class="metric-value text-danger">{{ $stats['critical_cases'] }}</div>
                        <div class="text-danger small fw-bold">
                            <i class="fas fa-exclamation-triangle me-1"></i> تحتاج متابعة
                        </div>
                    </div>
                </div>
                <i class="fas fa-heartbeat metric-icon text-danger"></i>
            </div>
        </div>
    </div>

    <!-- charts + feed -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-8">
            <div class="card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0 fw-bold">إحصائيات التشخيص</h5>
                    <select class="form-select form-select-sm w-auto">
                        <option>آخر 30 يوم</option>
                        <option>آخر 7 أيام</option>
                    </select>
                </div>
                <div style="height: 300px;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card p-4 h-100">
                <h5 class="mb-4 fw-bold">آخر الفحوصات الطبية</h5>
                <div class="list-group list-group-flush">
                    @forelse ($recentAssessments as $assessment)
                        <div class="list-group-item px-0 d-flex gap-3 align-items-start border-0 border-bottom">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                style="width: 40px; height: 40px;">
                                <i class="fas fa-notes-medical text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold fs-6">{{ $assessment->user->name }}</span>
                                    <span
                                        class="badge bg-{{ $assessment->status == 'مكتمل' ? 'success' : 'warning' }} bg-opacity-10 text-{{ $assessment->status == 'مكتمل' ? 'success' : 'warning' }} rounded-pill px-2">{{ $assessment->status }}</span>
                                </div>
                                <p class="mb-0 text-muted small text-truncate" style="max-width: 200px;">
                                    {{ $assessment->recommendation ?? 'لا توجد توصيات بعد' }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            لا توجد فحوصات حديثة
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-transparent border-0 p-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">آخر المستخدمين المسجلين</h5>
                    <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">المريض</th>
                                    <th>معلومات الاتصال</th>
                                    <th>تاريخ التسجيل</th>
                                    <th class="text-end pe-4">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentUsers as $user)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="avatar-sm bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center"
                                                    style="width: 40px; height: 40px;">
                                                    {{ substr($user->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $user->name }}</div>
                                                    <div class="text-muted small">ID: #{{ $user->id }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="text-muted small"><i class="far fa-envelope me-1"></i>
                                                    {{ $user->email }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $user->created_at->translatedFormat('d M Y, h:i A') }}</td>
                                        <td class="text-end pe-4">
                                            <span class="text-muted small">--</span>
                                        </td>
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
        const ctx = document.getElementById('salesChart');
        const dailyDiagnoses = @json($dailyDiagnoses);

        // Setup gradients
        let width, height, gradient;

        function getGradient(ctx, chartArea) {
            const chartWidth = chartArea.right - chartArea.left;
            const chartHeight = chartArea.bottom - chartArea.top;
            if (!gradient || width !== chartWidth || height !== chartHeight) {
                width = chartWidth;
                height = chartHeight;
                gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                gradient.addColorStop(0, 'rgba(67, 97, 238, 0.05)');
                gradient.addColorStop(1, 'rgba(67, 97, 238, 0.4)');
            }
            return gradient;
        }

        const labels = Object.keys(dailyDiagnoses);
        const data = Object.values(dailyDiagnoses);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'عدد الفحوصات',
                    data: data,
                    tension: 0.4,
                    fill: true,
                    backgroundColor: function(context) {
                        const chart = context.chart;
                        const {
                            ctx,
                            chartArea
                        } = chart;
                        if (!chartArea) {
                            return;
                        }
                        return getGradient(ctx, chartArea);
                    },
                    borderColor: '#4361ee',
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#4361ee',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                        titleColor: '#2b2d42',
                        bodyColor: '#666',
                        borderColor: '#e0e0e0',
                        borderWidth: 1,
                        padding: 10,
                        titleFont: {
                            family: 'Cairo',
                            size: 14
                        },
                        bodyFont: {
                            family: 'Cairo',
                            size: 13
                        },
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                family: 'Cairo'
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: 'Cairo'
                            }
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });
    </script>
@endpush
