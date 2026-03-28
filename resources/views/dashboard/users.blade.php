@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 fw-bold">المستخدمين 👥</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">إضافة مستخدم جديد</button>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>الاسم</th>
                            <th>البريد الإلكتروني</th>
                            <th>تاريخ التسجيل</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary"
                                        onclick="editUser({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}')">تعديل</button>
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="deleteUser({{ $user->id }})">حذف</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createUserModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">إضافة مستخدم جديد</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">الاسم</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">البريد الإلكتروني</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">كلمة المرور</label>
                            <input type="password" name="password" class="form-control" required>
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
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">تعديل المستخدم</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">الاسم</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">البريد الإلكتروني</label>
                            <input type="email" name="email" id="edit_email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">كلمة المرور (اتركه فارغاً إذا لم ترد التغيير)</label>
                            <input type="password" name="password" class="form-control">
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
    <div class="modal fade" id="deleteUserModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="deleteUserForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">حذف المستخدم</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>هل أنت متأكد من حذف هذا المستخدم؟</p>
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
            function editUser(id, name, email) {
                document.getElementById('editUserForm').action = '/dashboard/users/' + id;
                document.getElementById('edit_name').value = name;
                document.getElementById('edit_email').value = email;
                new bootstrap.Modal(document.getElementById('editUserModal')).show();
            }

            function deleteUser(id) {
                document.getElementById('deleteUserForm').action = '/dashboard/users/' + id;
                new bootstrap.Modal(document.getElementById('deleteUserModal')).show();
            }
        </script>
    @endpush
@endsection
