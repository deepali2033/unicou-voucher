@extends('layouts.master')
@section('content')
<div class="container-fluid py-4">

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-primary border-4">
                <div class="card-body">
                    <div class="text-muted small mb-1">Available Store Credit </div>
                    <div class="d-flex align-items-center">
                        <h3 class="fw-bold mb-0">{{ number_format($userPoints['store_credit'], 0) }}</h3>
                        <span class="ms-auto text-success small fw-bold"><i class="fas fa-arrow-up me-1"></i>12%</span>
                    </div>
                </div>
            </div>
        </div>

        @if(auth()->user()->isAgent() )
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-primary border-4">
                <div class="card-body">
                    <div class="text-muted small mb-1">Agent Referral Points Per Unit </div>
                    <div class="d-flex align-items-center">
                        <h3 class="fw-bold mb-0">{{ $rule->inventoryVoucher->agent_referral_points_per_unit}}</h3>
                        <span class="ms-auto text-success small fw-bold"><i class="fas fa-arrow-up me-1"></i>12%</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-primary border-4">
                <div class="card-body">
                    <div class="text-muted small mb-1">Agent Bonus Points Per Unit </div>
                    <div class="d-flex align-items-center">
                        <h3 class="fw-bold mb-0">{{ $rule->inventoryVoucher->agent_bonus_points_per_unit }} <!-- InventoryVoucher --></h3>
                        <span class="ms-auto text-success small fw-bold"><i class="fas fa-arrow-up me-1"></i>12%</span>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @if( auth()->user()->isStudent())
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-primary border-4">
                <div class="card-body">
                    <div class="text-muted small mb-1">Student Referral Points Per Unit </div>
                    <div class="d-flex align-items-center">
                        <h3 class="fw-bold mb-0">{{ $rule->inventoryVoucher->student_referral_points_per_unit}}</h3>
                        <span class="ms-auto text-success small fw-bold"><i class="fas fa-arrow-up me-1"></i>12%</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-primary border-4">
                <div class="card-body">
                    <div class="text-muted small mb-1">Student Bonus Points Per Unit </div>
                    <div class="d-flex align-items-center">
                        <h3 class="fw-bold mb-0">{{ $rule->inventoryVoucher->student_bonus_points_per_unit }} <!-- InventoryVoucher --></h3>
                        <span class="ms-auto text-success small fw-bold"><i class="fas fa-arrow-up me-1"></i>12%</span>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @if( auth()->user()->isResellerAgent())
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-primary border-4">
                <div class="card-body">
                    <div class="text-muted small mb-1">reseller Referral Points Per Unit </div>
                    <div class="d-flex align-items-center">
                        <h3 class="fw-bold mb-0">{{ $rule->inventoryVoucher->referral_points_reseller}}</h3>
                        <span class="ms-auto text-success small fw-bold"><i class="fas fa-arrow-up me-1"></i>12%</span>
                    </div>
                </div>
            </div>
        </div>

        @endif
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
                    <div class="price-details-table mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Price</span>
                            <span class="fw-bold">{{ $rule->inventoryVoucher->currency }} <span id="unit-price">{{ number_format($rule->final_price, 0) }}</span></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Quantity</span>
                            <span class="fw-bold" id="display-qty">1</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-bold">{{ $rule->inventoryVoucher->currency }} <span id="subtotal">{{ number_format($rule->final_price, 0) }}</span></span>
                        </div>

                        <!-- Bank Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Select Payment Bank</label>
                            <select id="bank-selector" class="form-select border-0 bg-light p-3" style="border-radius: 12px;">
                                <option value="">Choose a linked bank account...</option>
                                @foreach($banks as $bank)
                                <option value="{{ $bank->id }}" data-balance="{{ $bank->balance }}">
                                    {{ $bank->bank_name }} - {{ $bank->account_number }} (Bal: RS {{ number_format($bank->balance, 0) }})
                                </option>
                                @endforeach
                            </select>
                            @if(count($banks) == 0)
                            <div class="mt-2">
                                <small class="text-danger">No bank accounts linked. <a href="{{ route('bank.link') }}">Link a bank now</a></small>
                            </div>
                            @endif
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

<style>
    .brand-logo-container {
        width: 60px;
        height: 60px;
        background: #f8f9fa;
        border-radius: 12px;
        padding: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .icon-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .bg-light-warning {
        background-color: #fff9e6;
    }

    .tiny-text {
        font-size: 0.7rem;
        letter-spacing: 0.5px;
    }

    .quantity-selector input::-webkit-outer-spin-button,
    .quantity-selector input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .progress-ring-bar {
        transition: stroke-dashoffset 0.35s;
        transform: rotate(-90deg);
        transform-origin: 50% 50%;
    }

    .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .point-toggle-card {
        transition: border-color 0.2s;
    }

    .point-toggle-card:has(.form-check-input:checked) {
        border-color: #e84c71 !important;
        background-color: #fffafb;
    }
</style>

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

        const payNowBtn = document.getElementById('pay-now');
        payNowBtn.addEventListener('click', function() {
            const qty = qtyInput.value;
            const bankSelector = document.getElementById('bank-selector');
            const bankId = bankSelector.value;
            const selectedOption = bankSelector.options[bankSelector.selectedIndex];
            const bankBalance = parseFloat(selectedOption.getAttribute('data-balance')) || 0;
            const subtotal = parseFloat(document.getElementById('subtotal').innerText.replace(/,/g, ''));

            if (!bankId) {
                toastr.error('Please select a bank account for payment');
                return;
            }

            if (bankBalance < subtotal) {
                toastr.warning('Tumhare account me paise nahi hain. Please add amount to your bank.');
                // Optionally redirect after a delay
                setTimeout(() => {
                    if (confirm('Do you want to link more funds?')) {
                        window.location.href = "{{ route('bank.link') }}";
                    }
                }, 1000);
                return;
            }

            payNowBtn.disabled = true;
            payNowBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

            fetch("{{ route('vouchers.order.post', $rule->id) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        quantity: qty,
                        bank_id: bankId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success('Order placed successfully!');
                        setTimeout(() => {
                            window.location.href = "{{ route('orders.history') }}";
                        }, 1500);
                    } else {
                        toastr.error('Error: ' + (data.message || 'Something went wrong'));
                        payNowBtn.disabled = false;
                        payNowBtn.innerHTML = 'Pay Now';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('An error occurred while placing the order.');
                    payNowBtn.disabled = false;
                    payNowBtn.innerHTML = 'Pay Now';
                });
        });

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