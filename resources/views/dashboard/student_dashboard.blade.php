@extends('layouts.master')

@section('content')
<div class="container-fluid">
   <div class="row g-3 mb-4">
      <div class="col-md-6">
         <div class="card shadow-sm border-0 border-start border-primary border-4">
            <div class="card-body">
               <div class="text-muted small mb-1">Total Voucher Purchased</div>
               <div class="d-flex align-items-center">
                  <h3 class="fw-bold mb-0">{{ number_format($totalVouchersPurchased, 0) }}</h3>
                  <span class="ms-auto text-success small fw-bold"><i class="fas fa-ticket-alt me-1"></i>Units</span>
               </div>
            </div>
         </div>
      </div>
      <div class="col-md-6">
         <div class="card shadow-sm border-0 border-start border-warning border-4 h-100">
            <div class="card-body">
               <div class="text-muted small mb-1">Total Purchase Amount</div>
               <div class="d-flex align-items-center">
                  <h3 class="fw-bold mb-0">RS {{ number_format($totalPurchaseAmount, 2) }}</h3>
                  <span class="ms-auto text-warning small fw-bold"><i class="fas fa-money-bill-wave me-1"></i>Total Spent</span>
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
               <canvas id="revenueChart" style="height: 300px; width: 100%;"></canvas>
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
                           <span>Top Selling Variant</span>
                        </button>
                     </h2>
                     <div id="collapseOne"
                        class="accordion-collapse collapse"
                        data-bs-parent="#dashboardAccordion">
                        <div class="accordion-body fw-bold text-primary">
                           {{ $topSellingVariant ? $topSellingVariant->voucher_variant : 'N/A' }}
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
                        class="accordion-collapse collapse show"
                        data-bs-parent="#dashboardAccordion">
                        <div class="accordion-body">
                           <ul class="list-group list-group-flush p-0 m-0 border-0">
                              @forelse($topSellingBrands as $brand)
                              <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent border-0">
                                 <span class="fw-bold">{{ $brand->brand_name }}</span>
                                 <span class="badge bg-success-subtle text-success">{{ $brand->total }} Sales</span>
                              </li>
                              @empty
                              <li class="list-group-item bg-transparent border-0 px-0">No selling brands available.</li>
                              @endforelse
                           </ul>
                        </div>
                     </div>
                  </div>

               </div>
            </div>
         </div>


      </div>
   </div>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
   document.addEventListener('DOMContentLoaded', function() {
      const ctx = document.getElementById('revenueChart').getContext('2d');
      const dailyData = @json($dailyPurchases);
      const monthlyData = @json($monthlyPurchases);

      let currentChart;

      function renderChart(labels, revenueData, orderData, title) {
         if (currentChart) {
            currentChart.destroy();
         }

         currentChart = new Chart(ctx, {
            type: 'line',
            data: {
               labels: labels,
               datasets: [{
                     label: 'Revenue',
                     data: revenueData,
                     borderColor: '#23AAE2',
                     backgroundColor: 'rgba(35, 170, 226, 0.1)',
                     borderWidth: 2,
                     fill: true,
                     tension: 0.4
                  },
                  {
                     label: 'Orders',
                     data: orderData,
                     borderColor: '#28a745',
                     backgroundColor: 'rgba(40, 167, 69, 0.1)',
                     borderWidth: 2,
                     fill: true,
                     tension: 0.4
                  }
               ]
            },
            options: {
               responsive: true,
               maintainAspectRatio: false,
               plugins: {
                  legend: {
                     position: 'top',
                  },
                  title: {
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
      }

      // Initial Chart (Daily)
      const dailyLabels = dailyData.map(d => d.date);
      const dailyRevenue = dailyData.map(d => d.total_revenue);
      const dailyOrders = dailyData.map(d => d.total_orders);
      renderChart(dailyLabels, dailyRevenue, dailyOrders, 'Daily Trends');

      // Button Toggles
      document.querySelectorAll('.btn-group button').forEach(btn => {
         btn.addEventListener('click', function() {
            document.querySelectorAll('.btn-group button').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const type = this.textContent.trim();
            if (type === 'Daily') {
               renderChart(dailyLabels, dailyRevenue, dailyOrders, 'Daily Trends');
            } else if (type === 'Monthly') {
               const labels = monthlyData.map(d => d.month);
               const revenue = monthlyData.map(d => d.total_revenue);
               const orders = monthlyData.map(d => d.total_orders);
               renderChart(labels, revenue, orders, 'Monthly Trends');
            }
         });
      });
   });
   @if(session('shufti_response'))
   @php $response = session('shufti_response');
   @endphp
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