@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Student Dashboard</h4>
        <div class="system-status">
            <span class="badge bg-success-subtle text-success p-2">
                <i class="fas fa-circle me-1 small"></i> System Running
            </span>
        </div>
    </div>

    <!-- <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card p-4 border-0 shadow-sm" style="border-radius: 15px; background: #f8f9fa;">
                <div class="dash-icon-bg mb-3" style="font-size: 2rem; color: #198754;">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h4 class="fw-bold">Welcome, {{ Auth::user()->name }}</h4>
                <p class="text-muted mb-0">User ID: {{ Auth::user()->user_id }}</p>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card p-4 border-0 shadow-sm" style="border-radius: 15px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="mb-0 text-muted">Active Vouchers</p>
                    <i class="fas fa-ticket-alt fa-2x text-primary"></i>
                </div>
                <h3 class="fw-bold mb-1">0</h3>
                <p class="text-muted small mb-0">Vouchers available for use</p>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card p-4 border-0 shadow-sm" style="border-radius: 15px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="mb-0 text-muted">Completed Exams</p>
                    <i class="fas fa-check-circle fa-2x text-success"></i>
                </div>
                <h3 class="fw-bold mb-1">0</h3>
                <p class="text-muted small mb-0">Exams passed successfully</p>
            </div>
        </div>
    </div> -->


       <!-- Stats Cards -->
   <div class="row g-3 mb-4">
      <div class="col-md-3">
         <div class="card shadow-sm border-0 border-start border-primary border-4">
            <div class="card-body">
               <div class="text-muted small mb-1">Total Users</div>
               <div class="d-flex align-items-center">
                  <h3 class="fw-bold mb-0">1</h3>
                  <span class="ms-auto text-success small fw-bold"><i class="fas fa-users me-1"></i>Active</span>
               </div>
            </div>
         </div>
      </div>
      <div class="col-md-3">
         <div class="card shadow-sm border-0 border-start border-warning border-4 h-100">
            <div class="card-body">
               <div class="text-muted small mb-1">Total Revenue</div>
               <div class="d-flex align-items-center">
                  <h3 class="fw-bold mb-0">2</h3>
                  <span class="ms-auto text-warning small fw-bold"><i class="fas fa-money-bill-wave me-1"></i>Total</span>
               </div>
            </div>
         </div>
      </div>
      <div class="col-md-3">
         <div class="card shadow-sm border-0 border-start border-primary border-4">
            <div class="card-body">
               <div class="text-muted small mb-1">Total Voucher Stock</div>
               <div class="d-flex align-items-center">
                  <h3 class="fw-bold mb-0">5</h3>
                  <span class="ms-auto text-success small fw-bold"><i class="fas fa-ticket-alt me-1"></i>Units</span>
               </div>
            </div>
         </div>
      </div>
      <div class="col-md-3">
         <div class="card shadow-sm border-0 border-start border-info border-4 h-100">
            <div class="card-body">
               <div class="text-muted small mb-1">Total Sales (Delivered)</div>
               <div class="d-flex align-items-center">
                  <h3 class="fw-bold mb-0">0</h3>
                  <span class="ms-auto text-info small fw-bold"><i class="fas fa-shopping-cart me-1"></i>Orders</span>
               </div>
            </div>
         </div>
      </div>
      <div class="col-md-3">
         <div class="card shadow-sm border-0 border-start border-success border-4 h-100">
            <div class="card-body">
               <div class="text-muted small mb-1">Total Agents</div>
               <div class="d-flex align-items-center">
                  <h3 class="fw-bold mb-0">5</h3>
                  <span class="ms-auto text-success small fw-bold"><i class="fas fa-user-tie me-1"></i>B2B</span>
               </div>
            </div>
         </div>
      </div>
      <div class="col-md-3">
         <div class="card shadow-sm border-0 border-start border-info border-4 h-100">
            <div class="card-body">
               <div class="text-muted small mb-1">Total Students</div>
               <div class="d-flex align-items-center">
                  <h3 class="fw-bold mb-0">1</h3>
                  <span class="ms-auto text-info small fw-bold"><i class="fas fa-user-graduate me-1"></i>B2C</span>
               </div>
            </div>
         </div>
      </div>
      <div class="col-md-3">
         <div class="card shadow-sm border-0 border-start border-success border-4 h-100">
            <div class="card-body">
               <div class="text-muted small mb-1">Total Referral Points</div>
               <div class="d-flex align-items-center">
                  <h3 class="fw-bold mb-0">5</h3>
                  <span class="ms-auto text-success small fw-bold"><i class="fas fa-star me-1"></i>Total</span>
               </div>
            </div>
         </div>
      </div>
      <div class="col-md-3">
         <div class="card shadow-sm border-0 border-start border-info border-4 h-100">
            <div class="card-body">
               <div class="text-muted small mb-1">Total Bonus Points</div>
               <div class="d-flex align-items-center">
                  <h3 class="fw-bold mb-0">5</h3>
                  <span class="ms-auto text-info small fw-bold"><i class="fas fa-gift me-1"></i>Total</span>
               </div>
            </div>
         </div>
      </div>
   </div>
   <!-- Charts & Alerts -->
   <div class="row g-4">
      <div class="col-lg-8">
         <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
               <h6 class="mb-0 fw-bold">Voucher & Revenue Trends</h6>
               <div class="btn-group btn-group-sm">
                  <button class="btn btn-outline-secondary active">Daily</button>
                  <button class="btn btn-outline-secondary ">Weekly</button>
                  <button class="btn btn-outline-secondary">Monthly</button>
                  <button class="btn btn-outline-secondary">Yearly</button>
               </div>
            </div>
            <div class="card-body">
               <div class="chart-placeholder bg-light rounded d-flex align-items-center justify-content-center" style="height: 300px; border: 2px dashed #ddd;">
                  <div class="text-center text-muted">
                     <i class="fas fa-chart-line fa-3x mb-3"></i>
                     <p>Revenue & Voucher Graph Will Appear Here</p>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="col-lg-4">
         <!-- Low Stock Alerts -->
         <!-- <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-danger"><i class="fas fa-exclamation-circle me-1"></i> Low Stock Alerts</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold small">University Voucher (UK)</div>
                                <small class="text-muted">Only 5 remaining</small>
                            </div>
                            <span class="badge bg-danger">Critical</span>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold small">IELTS Booking Code</div>
                                <small class="text-muted">Only 12 remaining</small>
                            </div>
                            <span class="badge bg-warning text-dark">Warning</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white text-center py-2">
                <a href="{{ route('stock.alerts') }}" class="small text-decoration-none text-primary fw-bold">View All Alerts</a>
            </div>
            </div> -->
         <div class="card custom-accordion-card border-0 mb-4">
            <div class="card-body p-0">
               <div class="accordion" id="dashboardAccordion">
                  <!-- Top -->
                  <div class="accordion-item">
                     <h2 class="accordion-header">
                        <button class="accordion-button collapsed"
                           type="button"
                           data-bs-toggle="collapse"
                           data-bs-target="#collapseOne">
                        <i class="fas fa-chart-line me-2 text-primary"></i>
                        <span>Top</span>
                        </button>
                     </h2>
                     <div id="collapseOne"
                        class="accordion-collapse collapse"
                        data-bs-parent="#dashboardAccordion">
                        <div class="accordion-body">
                           Top content goes here...
                        </div>
                     </div>
                  </div>
                  <!-- Selling Brand -->
                  <div class="accordion-item">
                     <h2 class="accordion-header">
                        <button class="accordion-button collapsed"
                           type="button"
                           data-bs-toggle="collapse"
                           data-bs-target="#collapseTwo">
                        <i class="fas fa-tags me-2 text-success"></i>
                        <span>Selling Brand</span>
                        </button>
                     </h2>
                     <div id="collapseTwo"
                        class="accordion-collapse collapse"
                        data-bs-parent="#dashboardAccordion">
                        <div class="accordion-body">
                           Selling Brand content goes here...
                        </div>
                     </div>
                  </div>
                  <!-- Selling Varient -->
                  <div class="accordion-item">
                     <h2 class="accordion-header">
                        <button class="accordion-button collapsed"
                           type="button"
                           data-bs-toggle="collapse"
                           data-bs-target="#collapseThree">
                        <i class="fas fa-layer-group me-2 text-warning"></i>
                        <span>Selling Varient</span>
                        </button>
                     </h2>
                     <div id="collapseThree"
                        class="accordion-collapse collapse"
                        data-bs-parent="#dashboardAccordion">
                        <div class="accordion-body">
                           Selling Varient content goes here...
                        </div>
                     </div>
                  </div>
                  <!-- Buyer -->
                  <div class="accordion-item">
                     <h2 class="accordion-header">
                        <button class="accordion-button collapsed"
                           type="button"
                           data-bs-toggle="collapse"
                           data-bs-target="#collapseFour">
                        <i class="fas fa-user me-2 text-danger"></i>
                        <span>Buyer</span>
                        </button>
                     </h2>
                     <div id="collapseFour"
                        class="accordion-collapse collapse"
                        data-bs-parent="#dashboardAccordion">
                        <div class="accordion-body">
                           Buyer content goes here...
                        </div>
                     </div>
                  </div>
                  <!-- Country -->
                  <div class="accordion-item">
                     <h2 class="accordion-header">
                        <button class="accordion-button collapsed"
                           type="button"
                           data-bs-toggle="collapse"
                           data-bs-target="#collapseFive">
                        <i class="fas fa-globe me-2 text-info"></i>
                        <span>Country</span>
                        </button>
                     </h2>
                     <div id="collapseFive"
                        class="accordion-collapse collapse"
                        data-bs-parent="#dashboardAccordion">
                        <div class="accordion-body">
                           Country content goes here...
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- System Stats -->
         <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
               <h6 class="mb-0 fw-bold">Revenue Overview</h6>
            </div>
            <div class="card-body">
               <div class="d-flex justify-content-between mb-2">
                  <span class="text-muted small">Daily Revenue</span>
                  <span class="fw-bold small">€1,240.00</span>
               </div>
               <div class="progress mb-3" style="height: 8px;">
                  <div class="progress-bar bg-success" role="progressbar" style="width: 75%"></div>
               </div>
               <div class="d-flex justify-content-between mb-2">
                  <span class="text-muted small">Monthly Revenue</span>
                  <span class="fw-bold small">€28,450.00</span>
               </div>
               <div class="progress" style="height: 8px;">
                  <div class="progress-bar bg-primary" role="progressbar" style="width: 60%"></div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if(session('shufti_response'))
        @php $response = session('shufti_response'); @endphp
        @if(isset($response['event']) && $response['event'] == 'verification.accepted')
            Swal.fire({
                title: 'Congratulations!',
                text: 'Your identity has been successfully verified. Your application is now pending admin approval. You will receive an email once it is approved.',
                icon: 'success',
                confirmButtonColor: '#23AAE2'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('home') }}";
                }
            });
        @elseif(isset($response['error']))
            Swal.fire({
                title: 'Verification Error',
                text: '{{ $response["error"] }}',
                icon: 'error',
                confirmButtonColor: '#d33'
            });
        @else
            Swal.fire({
                title: 'Registration Submitted',
                text: 'Your details have been sent for verification. Please wait for admin approval.',
                icon: 'info',
                confirmButtonColor: '#23AAE2'
            });
        @endif
    @endif
</script>
@endpush
@endsection
