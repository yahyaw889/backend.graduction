@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="mb-1 fw-bold">لوحة تحكم النظام الطبي 🏥</h3>
            <div class="small-muted">نظام الكشف عن الأمراض الجلدية</div>
        </div>
    </div>

    <!-- top metrics -->
    <div class="row g-3 g-md-4 mb-4">
        <div class="col-12 col-sm-6 col-lg-6">
            <div class="card p-3 p-md-4 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="flex-grow-1">
                        <div class="small-muted mb-2">إجمالي المرضى</div>
                        <div class="metric">{{ $stats['total_users'] }}</div>
                        <div class="small-muted mt-2">المسجلين في النظام</div>
                    </div>
                    <div class="text-primary" style="font-size: 2.5rem; opacity: 0.2;">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>



        <div class="col-12 col-sm-6 col-lg-6">
            <div class="card p-3 p-md-4 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="flex-grow-1">
                        <div class="small-muted mb-2">الحالات الحرجة</div>
                        <div class="metric text-danger">{{ $stats['critical_cases'] }}</div>
                        <div class="small-muted mt-2">تحتاج متابعة فورية</div>
                    </div>
                    <div class="text-danger" style="font-size: 2.5rem; opacity: 0.2;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- charts + feed -->
    <div class="row g-3 g-md-4 mb-4">
        <div class="col-12 col-xl-8">
            <div class="card p-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">إحصائيات التشخيص اليومي</h5>
                        <div class="small-muted">آخر 30 يوم</div>
                    </div>
                    <canvas id="salesChart" height="120"></canvas>
                </div>
            </div>

            <div class="card p-3 mt-3">
                <div class="card-body">
                    <h6 class="mb-3">آخر المستخدمين المسجلين</h6>
                    <div class="table-wrap">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>الاسم</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>تاريخ التسجيل</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentUsers as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->created_at->diffForHumans() }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card p-3">
                <div class="card-body">
                    <h6 class="mb-3">آخر الفحوصات الطبية</h6>
                    <div class="list-group list-group-flush small-muted" style="max-height:360px;overflow:auto">
                        @foreach ($recentAssessments as $assessment)
                            <div class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">{{ $assessment->user->name }}</div>
                                    {{ $assessment->recommendation }}
                                </div>
                                <span
                                    class="badge bg-{{ $assessment->status == 'مكتمل' ? 'success' : 'warning' }} rounded-pill">{{ $assessment->status }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const ctx = document.getElementById('salesChart');
        const dailyDiagnoses = @json($dailyDiagnoses);

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
                    backgroundColor: 'rgba(54, 122, 194, 0.1)',
                    borderColor: '#367ac2',
                    borderWidth: 3,
                    pointBackgroundColor: '#367ac2',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>
@endpush
