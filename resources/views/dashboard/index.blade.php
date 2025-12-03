@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 fw-bold">لوحة تحكم النظام الطبي 🏥</h3>
        <div class="small-muted">نظام الكشف عن الأمراض الجلدية</div>
    </div>

    <!-- top metrics -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-md-3">
            <div class="card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small-muted">إجمالي المرضى</div>
                        <div class="metric">{{ $stats['total_users'] }}</div>
                        <div class="small-muted">المسجلين في النظام</div>
                    </div>
                    <div class="text-muted"><i class="fas fa-users fa-2x"></i></div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-3">
            <div class="card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small-muted">التقارير الطبية</div>
                        <div class="metric">{{ $stats['total_reports'] }}</div>
                        <div class="small-muted">{{ $stats['pending_assessments'] }} قيد المراجعة</div>
                    </div>
                    <div class="text-muted"><i class="fas fa-file-medical fa-2x"></i></div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-3">
            <div class="card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small-muted">الحالات الحرجة</div>
                        <div class="metric">{{ $stats['critical_cases'] }}</div>
                        <div class="small-muted">تحتاج متابعة فورية</div>
                    </div>
                    <div class="text-muted"><i class="fas fa-exclamation-triangle fa-2x"></i></div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-3">
            <div class="card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small-muted">معدل الدقة</div>
                        <div class="metric">94.5%</div>
                        <div class="small-muted">دقة التشخيص بالذكاء الاصطناعي</div>
                    </div>
                    <div class="text-muted"><i class="fas fa-chart-line fa-2x"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- charts + feed -->
    <div class="row g-4 mb-4">
        <div class="col-xl-8">
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

        <div class="col-xl-4">
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
                    backgroundColor: 'rgba(13,110,253,0.08)',
                    borderColor: 'rgba(13,110,253,1)'
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
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endpush
