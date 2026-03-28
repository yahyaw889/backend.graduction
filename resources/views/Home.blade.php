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
                <div class="metric">1,247</div>
                <div class="small-muted">+23 مريض جديد هذا الأسبوع</div>
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
                <div class="metric">892</div>
                <div class="small-muted">15 قيد المراجعة</div>
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
                <div class="metric">12</div>
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

          <div class="row g-3 mt-3">
            <div class="col-md-6">
              <div class="card p-3">
                <div class="card-body">
                  <h6 class="mb-3">المرضى حسب الفئة العمرية</h6>
                  <canvas id="visitorsChart" height="80"></canvas>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="card p-3">
                <div class="card-body">
                  <h6 class="mb-3">أكثر الأمراض شيوعاً</h6>
                  <canvas id="categoryChart" height="80"></canvas>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-4">
          <div class="card p-3">
            <div class="card-body">
              <h6 class="mb-3">آخر الفحوصات الطبية</h6>
              <div class="list-group list-group-flush small-muted" id="activityFeed" style="max-height:360px;overflow:auto">
                <!-- activities injected by JS -->
              </div>
            </div>
          </div>
          

          <div class="card p-3 mt-3">
            <div class="card-body">
              <h6 class="mb-3">التقارير الطبية الأخيرة</h6>
              <div class="table-wrap">
                <table class="table table-sm table-hover mb-0" id="ordersTable">
                  <thead>
                    <tr>
                      <th>رقم</th>
                      <th>المريض</th>
                      <th>التشخيص</th>
                      <th>الحالة</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- rows injected by JS -->
                  </tbody>
                </table>
              </div>

              <nav class="mt-2">
                <ul class="pagination pagination-sm mb-0" id="ordersPagination"></ul>
              </nav>
            </div>
          </div>
        </div>
      </div>

      <!-- modal for order details -->
      <div class="modal fade" id="orderModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">تفاصيل التقرير الطبي</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="orderModalBody">...</div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
            </div>
          </div>
        </div>
      </div>
@endsection
