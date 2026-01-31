@extends('layouts.master')

@section('content')
<div class="container-fluid">

    {{-- Offcanvas Filter --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold" id="filterOffcanvasLabel">
                <i class="fas fa-filter me-2"></i>Filter Pricing Rules
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form id="filter-form" action="{{ route('pricing.index') }}" method="GET">
                <div class="mb-4">
                    <label class="form-label fw-bold">Voucher Name</label>
                    <select name="voucher_id" class="form-select select2-filter">
                        <option value="">All Vouchers</option>

                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Country Name</label>
                    <input type="text" name="country_name" class="form-control" placeholder="Search country..." value="{{ request('country_name') }}">
                </div>

                <div class="d-grid gap-2 pt-3 border-top">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="{{ route('pricing.index') }}" class="btn btn-light">Reset All</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-primary border-4">
                <div class="card-body">
                    <div class="text-muted small mb-1">Total Users</div>
                    <div class="d-flex align-items-center">
                        <h3 class="fw-bold mb-0"></h3>
                        <span class="ms-auto text-success small fw-bold"><i class="fas fa-arrow-up me-1"></i>12%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
<div class="row">
    <!-- Left Column: Order Summary -->
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <p class="text-muted small text-uppercase fw-bold mb-1">ORDER SUMMARY</p>
                <h4 class="fw-bold mb-4">Review & Pay For Your Voucher</h4>

                <!-- Voucher Details Header -->
                <div class="d-flex align-items-center justify-content-between mb-4 pb-4 border-bottom">
                    <div class="d-flex align-items-center">
                        <div class="brand-logo-container me-3">
                            @if($rule->inventoryVoucher->logo)
                            <img src="{{ asset('storage/'.$rule->inventoryVoucher->logo) }}" alt="{{ $rule->inventoryVoucher->brand_name }}" class="img-fluid">
                            @else
                            <i class="fas fa-ticket-alt fa-2x text-primary"></i>
                            @endif
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">{{ $rule->inventoryVoucher->brand_name }}</h5>
                            <p class="text-muted small mb-0">Voucher purchase</p>
                        </div>
                    </div>
                    <div class="quantity-selector d-flex align-items-center bg-light rounded-pill px-2 py-1">
                        <button class="btn btn-sm btn-light rounded-circle shadow-none border-0" id="decrease-qty">
                            <i class="fas fa-minus small text-muted"></i>
                        </button>
                        <input type="number" id="voucher-quantity" class="form-control form-control-sm bg-transparent border-0 text-center fw-bold" value="1" min="1" max="{{ $rule->inventoryVoucher->quantity }}" style="width: 50px;">
                        <button class="btn btn-sm btn-light rounded-circle shadow-none border-0" id="increase-qty">
                            <i class="fas fa-plus small text-muted"></i>
                        </button>
                    </div>
                </div>

                <!-- Price Details Table -->
                <div class="price-details-table">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Price</span>
                        <span class="fw-bold">{{ $rule->inventoryVoucher->currency }} <span id="unit-price">{{ number_format($rule->final_price, 0) }}</span></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Compare at price</span>
                        <span class="text-muted text-decoration-line-through">{{ $rule->inventoryVoucher->currency }} {{ number_format($rule->sale_price, 0) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Quantity</span>
                        <span class="fw-bold" id="display-qty">1</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <span class="text-muted">Subtotal</span>
                        <span class="fw-bold">{{ $rule->inventoryVoucher->currency }} <span id="subtotal">{{ number_format($rule->final_price, 0) }}</span></span>
                    </div>

                    <div class="d-flex justify-content-between mb-2 text-primary">
                        <span>Quarterly Points Discount</span>
                        <span>{{ $rule->inventoryVoucher->currency }} <span id="q-discount">0</span></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 text-primary">
                        <span>Yearly Points Discount</span>
                        <span>{{ $rule->inventoryVoucher->currency }} <span id="y-discount">0</span></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 text-primary">
                        <span>Total Points Discount</span>
                        <span>{{ $rule->inventoryVoucher->currency }} <span id="total-discount">0</span></span>
                    </div>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="text-muted">Store Credit Applied</span>
                        <span class="fw-bold text-success">{{ $rule->inventoryVoucher->currency }} <span id="applied-credit">0</span></span>
                    </div>
                </div>

                <!-- Point Toggles -->
                <!-- <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="point-toggle-card rounded-4 p-3 border">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-circle bg-light me-3">
                                            <i class="fas fa-trophy text-secondary"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0 small fw-bold">Apply Quarterly Points</p>
                                            <p class="mb-0 tiny-text text-muted">{{ $userPoints['quarterly'] }} points available</p>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="toggle-q-points">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="point-toggle-card rounded-4 p-3 border">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-circle bg-light-warning me-3">
                                            <i class="fas fa-medal text-warning"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0 small fw-bold">Apply Yearly Points</p>
                                            <p class="mb-0 tiny-text text-muted">{{ $userPoints['yearly'] }} points available</p>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="toggle-y-points">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->

                <!-- Total & Actions -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <p class="text-muted small mb-0">Total to pay</p>
                        <h3 class="fw-bold text-primary mb-0">{{ $rule->inventoryVoucher->currency }} <span id="final-total">{{ number_format($rule->final_price, 0) }}</span></h3>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('vouchers') }}" class="btn btn-light border-0 px-4 rounded-3 fw-bold">Cancel</a>
                        <button class="btn btn-primary px-5 rounded-3 fw-bold shadow-sm" id="pay-now">Pay Now</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Progress & Earnings -->
    <!-- <div class="col-lg-4">
          
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4 text-center">
                    <h6 class="text-start fw-bold mb-4">Your Progress</h6>

                    <div class="progress-ring-container mb-4">
                        <div class="brand-progress-header d-flex align-items-center justify-content-center mb-3">
                            <div class="brand-logo-sm me-2">
                                @if($rule->inventoryVoucher->logo)
                                <img src="{{ asset('storage/'.$rule->inventoryVoucher->logo) }}" alt="" style="height: 20px;">
                                @endif
                            </div>
                            <span class="fw-bold small">{{ $rule->inventoryVoucher->brand_name }}</span>
                        </div>
                        <div class="progress-ring-wrapper position-relative d-inline-block">
                            <svg width="120" height="120">
                                <circle class="progress-ring-bg" stroke="#f1f1f1" stroke-width="8" fill="transparent" r="50" cx="60" cy="60" />
                                <circle class="progress-ring-bar" stroke="#e84c71" stroke-width="8" fill="transparent" r="50" cx="60" cy="60" style="stroke-dasharray: 314.159; stroke-dashoffset: 314.159;" />
                            </svg>
                            <div class="progress-text position-absolute top-50 start-50 translate-middle">
                                <h5 class="mb-0 fw-bold">0/50</h5>
                                <p class="tiny-text text-muted mb-0">Purchased</p>
                            </div>
                        </div>
                    </div>

                    <p class="fw-bold mb-1">Your Progress Till Now</p>
                    <p class="text-muted small mb-4">Buy 50 more vouchers in next 78 days to get 15,000 quarterly points and 5,000 yearly points</p>

                    <div class="on-track-badge d-inline-flex align-items-center bg-light px-3 py-1 rounded-pill mb-2">
                        <span class="dot bg-primary me-2"></span>
                        <span class="small text-primary fw-bold">On Track</span>
                        <span class="small text-muted ms-2">Keep going!</span>
                    </div>
                </div>
            </div>

           
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-4">What you'll earn</h6>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="earning-item p-2 rounded-3">
                                <div class="icon-circle bg-light mx-auto mb-2">
                                    <i class="fas fa-trophy text-secondary"></i>
                                </div>
                                <p class="tiny-text fw-bold text-muted text-uppercase mb-1">QUARTERLY POINTS</p>
                                <h6 class="fw-bold text-dark mb-0"><span id="display-q-points">
                                        @if(auth()->user()->account_type == 'student')
                                        {{ $rule->inventoryVoucher->student_referral_points_per_unit }}
                                        @else
                                        {{ $rule->inventoryVoucher->agent_referral_points_per_unit }}
                                        @endif
                                    </span> RS</h6>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="earning-item p-2 rounded-3">
                                <div class="icon-circle bg-light-warning mx-auto mb-2">
                                    <i class="fas fa-medal text-warning"></i>
                                </div>
                                <p class="tiny-text fw-bold text-muted text-uppercase mb-1">YEARLY POINTS</p>
                                <h6 class="fw-bold text-dark mb-0"><span id="display-y-points">
                                        @if(auth()->user()->account_type == 'student')
                                        {{ $rule->inventoryVoucher->student_bonus_points_per_unit }}
                                        @else
                                        {{ $rule->inventoryVoucher->agent_bonus_points_per_unit }}
                                        @endif
                                    </span> RS</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
</div>
</div>

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.6.6/css/flag-icons.min.css" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const qtyInput = document.getElementById('voucher-quantity');
        const displayQty = document.getElementById('display-qty');
        const unitPrice = parseFloat("{{ $rule->final_price }}");
        const subtotalEl = document.getElementById('subtotal');
        const finalTotalEl = document.getElementById('final-total');
        const increaseBtn = document.getElementById('increase-qty');
        const decreaseBtn = document.getElementById('decrease-qty');

        function updatePrices() {
            const qty = parseInt(qtyInput.value);
            const subtotal = qty * unitPrice;

            displayQty.innerText = qty;
            subtotalEl.innerText = subtotal.toLocaleString();

            // Final total logic (considering points later)
            let total = subtotal;

            // Add point logic here if needed

            finalTotalEl.innerText = total.toLocaleString();
        }

        increaseBtn.addEventListener('click', () => {
            qtyInput.value = parseInt(qtyInput.value) + 1;
            updatePrices();
        });

        decreaseBtn.addEventListener('click', () => {
            if (qtyInput.value > 1) {
                qtyInput.value = parseInt(qtyInput.value) - 1;
                updatePrices();
            }
        });

        qtyInput.addEventListener('change', updatePrices);

        // Initial progress bar update (mock 0/50)
        const circle = document.querySelector('.progress-ring-bar');
        const radius = circle.r.baseVal.value;
        const circumference = radius * 2 * Math.PI;
        circle.style.strokeDasharray = `${circumference} ${circumference}`;
        circle.style.strokeDashoffset = circumference;

        function setProgress(percent) {
            const offset = circumference - (percent / 100 * circumference);
            circle.style.strokeDashoffset = offset;
        }

        // Example: set to 0% initially
        setProgress(0);
    });
</script>
@endsection
!important;
background-color: #fffafb;
}
</style>


@endsection