@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="mb-1 fw-bold">الإعدادات ⚙️</h3>
            <div class="small-muted">إدارة ملفك الشخصي وتفضيلات النظام</div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Settings Navigation -->
        <div class="col-12 col-md-3">
            <div class="card p-0 overflow-hidden">
                <div class="list-group list-group-flush">
                    <a href="#profile" class="list-group-item list-group-item-action active" data-bs-toggle="list">
                        <i class="fas fa-user-circle me-2"></i> الملف الشخصي
                    </a>
                    <a href="#security" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="fas fa-lock me-2"></i> الأمان وكلمة المرور
                    </a>
                    {{--                <a href="#notifications" class="list-group-item list-group-item-action" data-bs-toggle="list"> --}}
                    {{--                    <i class="fas fa-bell me-2"></i> التنبيهات --}}
                    {{--                </a> --}}
                    {{--                <a href="#appearance" class="list-group-item list-group-item-action" data-bs-toggle="list"> --}}
                    {{--                    <i class="fas fa-palette me-2"></i> المظهر --}}
                    {{--                </a> --}}
                </div>
            </div>
        </div>

        <!-- Settings Content -->
        <div class="col-12 col-md-9">
            <div class="card p-4">
                <div class="tab-content">

                    <!-- Profile Settings -->
                    <div class="tab-pane fade show active" id="profile">
                        <h5 class="mb-4 fw-bold">معلومات الملف الشخصي</h5>
                        <form action="{{ route('settings.update') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-12 d-flex align-items-center mb-3">
                                    <div class="position-relative me-3">
                                        <div class="bg-primary text-white d-flex align-items-center justify-content-center rounded-circle"
                                            style="width: 80px; height: 80px; font-size: 2rem;">
                                            {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 fw-bold">{{ auth()->user()->name ?? 'مستخدم' }}</h6>
                                        <div class="text-muted small">المسؤول</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small-muted">الاسم الكامل</label>
                                    <input type="text" class="form-control" name="name"
                                        value="{{ auth()->user()->name ?? '' }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small-muted">البريد الإلكتروني</label>
                                    <input type="email" class="form-control" name="email"
                                        value="{{ auth()->user()->email ?? '' }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small-muted">رقم الهاتف</label>
                                    <input type="tel" class="form-control" name="phone"
                                        value="{{ auth()->user()->phone ?? '' }}">
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save me-2"></i> حفظ التغييرات
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Security Settings -->
                    <div class="tab-pane fade" id="security">
                        <h5 class="mb-4 fw-bold">تغيير كلمة المرور</h5>
                        <form action="{{ route('settings.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="type" value="password">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label small-muted">كلمة المرور الحالية</label>
                                    <input type="password" class="form-control" name="current_password">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small-muted">كلمة المرور الجديدة</label>
                                    <input type="password" class="form-control" name="new_password">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small-muted">تأكيد كلمة المرور الجديدة</label>
                                    <input type="password" class="form-control" name="new_password_confirmation">
                                </div>
                            </div>

                            <div class="alert alert-info mt-4 mb-0 small">
                                <i class="fas fa-info-circle me-2"></i>
                                يجب أن تكون كلمة المرور مكونة من 8 أحرف على الأقل وتحتوي على أحرف وأرقام.
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-shield-alt me-2"></i> تحديث كلمة المرور
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Additional tabs can be enabled later
                <!-- Notifications Settings -->
                <div class="tab-pane fade" id="notifications">
                    <h5 class="mb-4 fw-bold">تفضيلات التنبيهات</h5>
                    <!-- Content -->
                </div>

                <!-- Appearance Settings -->
                <div class="tab-pane fade" id="appearance">
                    <h5 class="mb-4 fw-bold">تخصيص المظهر</h5>
                    <!-- Content -->
                </div>
                --}}
                </div>
            </div>
        </div>
    </div>
@endsection
