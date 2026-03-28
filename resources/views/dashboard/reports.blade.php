@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 fw-bold">التقارير الطبية 📄</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createReportModal">
            إنشاء تقرير جديد
        </button>
    </div>

    <!-- Statistics -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card p-3">
                <div class="card-body">
                    <h6 class="text-muted">إجمالي التقارير</h6>
                    <h3>{{ $reportStats['total'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports Table -->
    <div class="card">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>المريض</th>
                            <th>النوع</th>
                            <th>التاريخ</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($reports as $report)
                            <tr>
                                <td>{{ $report->id }}</td>
                                <td>{{ $report->user->name }}</td>
                                <td>{{ $report->report_type }}</td>
                                <td>{{ $report->generated_at }}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary"
                                        onclick="editReport({{ $report->id }}, '{{ $report->report_type }}')">
                                        تعديل
                                    </button>

                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="deleteReport({{ $report->id }})">
                                        حذف
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $reports->links() }}
            </div>

        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createReportModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('reports.store') }}" method="POST">
                @csrf
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">إنشاء تقرير جديد</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">المستخدم</label>
                            <select name="user_id" class="form-select" required>
                                @foreach (\App\Models\User::all() as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">نوع التقرير</label>
                            <select name="report_type" class="form-select" required>
                                <option value="assessment">Assessment</option>
                                <option value="health_summary">Health Summary</option>
                                <option value="monthly">Monthly</option>
                            </select>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">حفظ</button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editReportModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="editReportForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">تعديل التقرير</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">نوع التقرير</label>
                            <select name="report_type" id="edit_report_type" class="form-select" required>
                                <option value="assessment">Assessment</option>
                                <option value="health_summary">Health Summary</option>
                                <option value="monthly">Monthly</option>
                            </select>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">تحديث</button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteReportModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="deleteReportForm" method="POST">
                @csrf
                @method('DELETE')

                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">حذف التقرير</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <p>هل أنت متأكد من حذف هذا التقرير؟</p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-danger">حذف</button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function editReport(id, type) {
                document.getElementById('editReportForm').action = '/dashboard/reports/' + id;
                document.getElementById('edit_report_type').value = type;
                new bootstrap.Modal(document.getElementById('editReportModal')).show();
            }

            function deleteReport(id) {
                document.getElementById('deleteReportForm').action = '/dashboard/reports/' + id;
                new bootstrap.Modal(document.getElementById('deleteReportModal')).show();
            }
        </script>
    @endpush
@endsection
