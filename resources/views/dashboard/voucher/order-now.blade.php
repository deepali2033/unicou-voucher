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

        @if(auth()->user()->isRegularAgent() )
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-primary border-4">
                <div class="card-body">
                    <div class="text-muted small mb-1">Agent Referral Points </div>
                    <div class="d-flex align-items-center">
                        <h3 class="fw-bold mb-0"><span class="referral-points-display" data-base="{{ $voucher->agent_referral_points_per_unit }}">{{ $voucher->agent_referral_points_per_unit }}</span></h3>
                        <span class="ms-auto text-success small fw-bold"><i class="fas fa-arrow-up me-1"></i>12%</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-primary border-4">
                <div class="card-body">
                    <div class="text-muted small mb-1">Agent Bonus Points </div>
                    <div class="d-flex align-items-center">
                        <h3 class="fw-bold mb-0"><span class="bonus-points-display" data-base="{{ $voucher->agent_bonus_points_per_unit }}">{{ $voucher->agent_bonus_points_per_unit }}</span></h3>
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
                    <div class="text-muted small mb-1">Student Referral Points </div>
                    <div class="d-flex align-items-center">
                        <h3 class="fw-bold mb-0"><span class="referral-points-display" data-base="{{ $voucher->student_referral_points_per_unit }}">{{ $voucher->student_referral_points_per_unit }}</span></h3>
                        <span class="ms-auto text-success small fw-bold"><i class="fas fa-arrow-up me-1"></i>12%</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-primary border-4">
                <div class="card-body">
                    <div class="text-muted small mb-1">Student Bonus Points </div>
                    <div class="d-flex align-items-center">
                        <h3 class="fw-bold mb-0"><span class="bonus-points-display" data-base="{{ $voucher->student_bonus_points_per_unit }}">{{ $voucher->student_bonus_points_per_unit }}</span></h3>
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
                    <div class="text-muted small mb-1">Reseller Referral Points </div>
                    <div class="d-flex align-items-center">
                        <h3 class="fw-bold mb-0"><span class="referral-points-display" data-base="{{ $voucher->referral_points_reseller }}">{{ $voucher->referral_points_reseller }}</span></h3>
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
                                @if($rule ->logo)
                                <img src="{{ asset($rule->logo) }}" alt="{{ $rule->brand_name }}" class="img-fluid">
                                @else
                                <i class="fas fa-ticket-alt fa-2x text-primary"></i>
                                @endif
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">{{ $rule->brand_name }}</h5>
                                <p class="text-muted small mb-0">Voucher purchase</p>
                            </div>
                        </div>
                        <div class="quantity-selector d-flex align-items-center bg-light rounded-pill px-2 py-1">
                            <button class="btn btn-sm btn-light rounded-circle shadow-none border-0" id="decrease-qty">
                                <i class="fas fa-minus small text-muted"></i>
                            </button>
                            <input type="number" id="voucher-quantity" class="form-control form-control-sm bg-transparent border-0 text-center fw-bold" value="1" min="1" max="{{ $userPoints['max_allowed'] > 0 ? min($userPoints['max_allowed'], $rule->quantity) : $rule->quantity }}" style="width: 50px;">
                            <button class="btn btn-sm btn-light rounded-circle shadow-none border-0" id="increase-qty">
                                <i class="fas fa-plus small text-muted"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Price Details Table -->
                    <div class="price-details-table mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Price</span>
                            <span class="fw-bold">{{ $rule->currency }} <span id="unit-price">{{ number_format($rule->final_price, 0) }}</span></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Quantity</span>
                            <span class="fw-bold" id="display-qty">1</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-bold">{{ $rule->currency }} <span id="subtotal">{{ number_format($rule->final_price, 0) }}</span></span>
                        </div>

                        <!-- Payment Method Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Select Payment Method</label>
                            <div class="d-flex gap-3 mb-3">
                                <div class="form-check payment-type-card p-3 border rounded-3 flex-fill cursor-pointer active" id="type-card">
                                    <input class="form-check-input d-none" type="radio" name="payment_type" id="payment_card" value="card" checked>
                                    <label class="form-check-label d-flex align-items-center cursor-pointer" for="payment_card">
                                        <i class="fas fa-credit-card me-2 text-primary"></i>
                                        <span>Card Payment</span>
                                    </label>
                                </div>
                                <div class="form-check payment-type-card p-3 border rounded-3 flex-fill cursor-pointer" id="type-admin-bank">
                                    <input class="form-check-input d-none" type="radio" name="payment_type" id="payment_admin_bank" value="admin_bank">
                                    <label class="form-check-label d-flex align-items-center cursor-pointer" for="payment_admin_bank">
                                        <i class="fas fa-university me-2 text-success"></i>
                                        <span>Direct Transfer (Admin)</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Card Payment Section -->
                            <div id="card-payment-section">
                                <label class="form-label fw-bold small text-uppercase text-muted">Card Details</label>
                                <div class="card border-0 bg-light rounded-4 p-4 shadow-sm">
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <label class="form-label small fw-bold">Card Number</label>
                                            <input type="text" id="card-number" class="form-control border-0 p-3 shadow-sm" placeholder="0000 0000 0000 0000" style="border-radius: 10px;">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">Expiry Date</label>
                                            <input type="text" id="card-expiry" class="form-control border-0 p-3 shadow-sm" placeholder="MM/YY" style="border-radius: 10px;">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">CVV</label>
                                            <input type="text" id="card-cvv" class="form-control border-0 p-3 shadow-sm" placeholder="123" style="border-radius: 10px;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Admin Banks Section -->
                            <div id="admin-banks-section" class="d-none">
                                <label class="form-label fw-bold small text-uppercase text-muted">Select Admin Bank To Pay</label>
                                <select id="admin-bank-selector" class="form-select border-0 bg-light p-3 mb-3" style="border-radius: 12px;">
                                    <option value="">Choose admin's bank account...</option>
                                    @foreach($adminBanks as $abank)
                                    <option value="{{ $abank->id }}"
                                        data-name="{{ $abank->bank_name }}"
                                        data-holder="{{ $abank->account_holder_name }}"
                                        data-number="{{ $abank->account_number }}"
                                        data-ifsc="{{ $abank->ifsc_code }}"
                                        data-swift="{{ $abank->swift_code }}"
                                        data-routing="{{ $abank->routing_number }}"
                                        data-upi="{{ $abank->upi_id }}"
                                        data-qr="{{ $abank->qr_code ? asset('storage/'.$abank->qr_code) : '' }}"
                                        data-notes="{{ $abank->notes }}">
                                        {{ $abank->bank_name }} ({{ $abank->country ?: 'Global' }})
                                    </option>
                                    @endforeach
                                </select>

                                <!-- Admin Bank Details Display -->
                                <div id="admin-bank-details" class="card bg-light border-0 rounded-4 p-3 mb-3 d-none">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <p class="small text-muted mb-0">Bank Name</p>
                                            <p class="fw-bold mb-2" id="detail-bank-name"></p>
                                            <p class="small text-muted mb-0">Account Holder</p>
                                            <p class="fw-bold mb-2" id="detail-holder"></p>
                                            <p class="small text-muted mb-0">Account Number / IBAN</p>
                                            <p class="fw-bold mb-2" id="detail-number"></p>
                                        </div>
                                        <div class="col-md-6" id="detail-codes">
                                            <!-- Codes will be injected here -->
                                        </div>
                                        <div class="col-12 d-none" id="detail-qr-container">
                                            <p class="small text-muted mb-0">QR Code</p>
                                            <img id="detail-qr" src="" class="img-fluid rounded border mt-2" style="max-height: 150px;">
                                        </div>
                                        <div class="col-12 d-none" id="detail-notes-container">
                                            <p class="small text-muted mb-1">Instructions:</p>
                                            <div class="alert alert-info py-2 px-3 small mb-0" id="detail-notes"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Transaction Submission Form (Initially Hidden) -->
                                <div id="admin-transfer-form" class="mt-4 d-none">
                                    <div class="alert alert-primary border-0 shadow-sm mb-4 d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="fas fa-info-circle fa-2x"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0 small fw-bold">REQUIRED TRANSFER AMOUNT</p>
                                            <h4 class="fw-bold mb-0">{{ $rule->currency }} <span class="required-amount-text">0.00</span></h4>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold small text-uppercase text-muted">Upload Payment Receipt / Screenshot</label>
                                        <input type="file" id="payment-receipt" class="form-control border-0 bg-light p-3" style="border-radius: 12px;" accept="image/*">
                                        <div class="form-text text-muted">Upload the screenshot of your successful transaction.</div>

                                        <!-- OCR Progress Bar -->
                                        <div id="ocr-progress" class="mt-3 d-none">
                                            <div class="d-flex justify-content-between mb-1">
                                                <small id="ocr-status" class="text-primary fw-bold">Processing receipt...</small>
                                                <small id="ocr-percentage" class="text-muted">0%</small>
                                            </div>
                                            <div class="progress" style="height: 10px; border-radius: 5px;">
                                                <div id="ocr-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card border-0 bg-light rounded-4 p-4 shadow-sm">
                                        <h6 class="fw-bold mb-4 small text-uppercase text-primary"><i class="fas fa-edit me-2"></i>Manual Transaction Details</h6>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Transaction ID / Reference No.</label>
                                                <input type="text" id="transaction-id" class="form-control border-0 p-3 shadow-sm" placeholder="Enter transaction ID" style="border-radius: 10px;">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Sender Account Holder Name</label>
                                                <input type="text" id="account-holder-name" class="form-control border-0 p-3 shadow-sm" placeholder="Name on your bank account" style="border-radius: 10px;">
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label small fw-bold">Exact Amount Transferred</label>
                                                <div class="input-group">
                                                    <span class="input-group-text border-0 bg-white shadow-sm" style="border-radius: 10px 0 0 10px;">{{ $rule->currency }}</span>
                                                    <input type="number" id="transfer-amount" class="form-control border-0 p-3 shadow-sm" placeholder="0.00" style="border-radius: 0 10px 10px 0;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total & Actions -->
                    <div class="d-flex justify-content-between align-items-center mb-4 pt-4 border-top">
                        <div>
                            <p class="text-muted small mb-0">Total to pay</p>
                            <h3 class="fw-bold text-primary mb-0">{{ $rule->currency }} <span id="final-total">{{ number_format($rule->final_price, 0) }}</span></h3>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('vouchers') }}" class="btn btn-light border-0 px-4 rounded-3 fw-bold">Cancel</a>
                            <button class="btn btn-primary px-5 rounded-3 fw-bold shadow-sm" id="pay-now">Place Order</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .payment-type-card {
        cursor: pointer;
        transition: all 0.2s;
        border: 2px solid #e2e8f0 !important;
    }

    .payment-type-card:hover {
        background-color: #f8fafc;
        border-color: #2563eb !important;
    }

    .payment-type-card label {
        cursor: pointer;
        user-select: none;
    }

    .payment-type-card.active {
        background-color: #f0f7ff;
        border-color: #2563eb !important;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1);
    }

    .brand-logo-container {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
        border-radius: 12px;
        padding: 5px;
    }

    .brand-logo-container img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
<script>
    $(document).ready(function() {
        let finalPrice = {{ $rule->final_price }};
        let maxQty = {{ $userPoints['max_allowed'] > 0 ? min($userPoints['max_allowed'], $rule->quantity) : $rule->quantity }};
        let capturedDetails = null;

        // Tesseract OCR Logic
        $('#payment-receipt').on('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function() {
                const img = new Image();
                img.src = reader.result;
                img.onload = function() {
                    $('#ocr-progress').removeClass('d-none');
                    $('#ocr-status').text('Scanning receipt...');

                    Tesseract.recognize(
                        img,
                        'eng', {
                            logger: m => {
                                if (m.status === 'recognizing text') {
                                    let prog = Math.round(m.progress * 100);
                                    $('#ocr-bar').css('width', prog + '%');
                                    $('#ocr-percentage').text(prog + '%');
                                }
                            }
                        }
                    ).then(({
                        data: {
                            text
                        }
                    }) => {
                        $('#ocr-progress').addClass('d-none');

                        // Simple regex extraction
                        const txIdMatch = text.match(/(?:Txn ID|Transaction ID|Ref No|Reference|TXN)[:\s]*([A-Z0-9]+)/i);
                        const amountMatch = text.match(/(?:Amount|Total|Paid|Sum)[:\s]*[^\d]*([\d,]+\.?\d*)/i);

                        let data = {
                            raw_text: text,
                            extracted_tx_id: txIdMatch ? txIdMatch[1] : null,
                            extracted_amount: amountMatch ? amountMatch[1] : null,
                            captured_at: new Date().toISOString()
                        };

                        capturedDetails = JSON.stringify(data);

                        if (data.extracted_tx_id) {
                            $('#transaction-id').val(data.extracted_tx_id);
                            toastr.success('Transaction ID captured!');
                        }
                        if (data.extracted_amount) {
                            $('#transfer-amount').val(data.extracted_amount.replace(/,/g, ''));
                            toastr.success('Amount captured!');
                        }
                    }).catch(err => {
                        console.error(err);
                        $('#ocr-progress').addClass('d-none');
                        toastr.warning('Could not auto-capture details. Please enter manually.');
                    });
                };
            };
            reader.readAsDataURL(file);
        });

        function updateTotal() {
            let qty = parseInt($('#voucher-quantity').val()) || 1;
            let total = finalPrice * qty;
            $('#display-qty').text(qty);
            $('#subtotal').text(total.toLocaleString());
            $('#final-total').text(total.toLocaleString());
            $('.required-amount-text').text(total.toLocaleString());

            // Auto fill transfer amount
            $('#transfer-amount').val(total);

            // Update Points Display
            $('.referral-points-display').each(function() {
                let base = parseInt($(this).data('base')) || 0;
                $(this).text(base * qty);
            });
            $('.bonus-points-display').each(function() {
                let base = parseInt($(this).data('base')) || 0;
                $(this).text(base * qty);
            });
        }

        // Initial call
        updateTotal();

        $('#increase-qty').click(function() {
            let qty = parseInt($('#voucher-quantity').val());
            if (qty < maxQty) {
                $('#voucher-quantity').val(qty + 1);
                updateTotal();
            } else {
                if (qty >= {{ $userPoints['max_allowed'] }}) {
                    toastr.warning('Aapki 24 ghanto ki voucher kharidne ki limit puri ho gayi he.');
                } else {
                    toastr.warning('Stock me bas itne hi vouchers available hain.');
                }
            }
        });

        $('#decrease-qty').click(function() {
            let qty = parseInt($('#voucher-quantity').val());
            if (qty > 1) {
                $('#voucher-quantity').val(qty - 1);
                updateTotal();
            }
        });

        $('#voucher-quantity').on('change input', function() {
            let val = parseInt($(this).val());
            if (isNaN(val) || val < 1) $(this).val(1);
            if (val > maxQty) $(this).val(maxQty);
            updateTotal();
        });

        // Payment Type Toggle
        $('.payment-type-card').on('click', function(e) {
            $('.payment-type-card').removeClass('active');
            $(this).addClass('active');

            let radio = $(this).find('input[type="radio"]');
            radio.prop('checked', true);

            let type = radio.val();
            if (type === 'card') {
                $('#card-payment-section').removeClass('d-none');
                $('#admin-banks-section').addClass('d-none');
            } else {
                $('#card-payment-section').addClass('d-none');
                $('#admin-banks-section').removeClass('d-none');

                // Auto select first admin bank if none selected
                if (!$('#admin-bank-selector').val()) {
                    $('#admin-bank-selector').find('option:eq(1)').prop('selected', true).trigger('change');
                } else {
                    $('#admin-bank-selector').trigger('change');
                }
            }
        });

        // Admin Bank Detail Update
        $('#admin-bank-selector').change(function() {
            let selected = $(this).find(':selected');
            if (!selected.val()) {
                $('#admin-bank-details').addClass('d-none');
                $('#admin-transfer-form').addClass('d-none');
                return;
            }

            $('#detail-bank-name').text(selected.data('name'));
            $('#detail-holder').text(selected.data('holder'));
            $('#detail-number').text(selected.data('number'));

            let codesHtml = '';
            if (selected.data('ifsc')) codesHtml += `<p class="small text-muted mb-0">IFSC (India)</p><p class="fw-bold mb-2">${selected.data('ifsc')}</p>`;
            if (selected.data('swift')) codesHtml += `<p class="small text-muted mb-0">SWIFT/BIC</p><p class="fw-bold mb-2">${selected.data('swift')}</p>`;
            if (selected.data('routing')) codesHtml += `<p class="small text-muted mb-0">Routing No.</p><p class="fw-bold mb-2">${selected.data('routing')}</p>`;
            if (selected.data('upi')) codesHtml += `<p class="small text-muted mb-0">UPI ID</p><p class="fw-bold mb-2 text-success">${selected.data('upi')}</p>`;
            $('#detail-codes').html(codesHtml);

            if (selected.data('qr')) {
                $('#detail-qr').attr('src', selected.data('qr'));
                $('#detail-qr-container').removeClass('d-none');
            } else {
                $('#detail-qr-container').addClass('d-none');
            }

            if (selected.data('notes')) {
                $('#detail-notes').text(selected.data('notes'));
                $('#detail-notes-container').removeClass('d-none');
            } else {
                $('#detail-notes-container').addClass('d-none');
            }

            $('#admin-bank-details').removeClass('d-none');
            $('#admin-transfer-form').removeClass('d-none');
        });

        $('#pay-now').click(function() {
            let paymentType = $('input[name="payment_type"]:checked').val();
            let qtyElement = $('#voucher-quantity');
            let qty = parseInt(qtyElement.val()) || 1;
            let btn = $(this);

            console.log('🔵 Pay Now clicked');
            console.log('Payment Type:', paymentType);
            console.log('Quantity Raw:', qtyElement.val());
            console.log('Quantity Parsed:', qty);
            console.log('Quantity Type:', typeof qty);

            if (!paymentType) {
                toastr.error('Please select a payment method');
                return;
            }

            if (qty < 1) {
                toastr.error('Please enter a valid quantity');
                return;
            }

            let formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('quantity', qty);
            formData.append('payment_type', paymentType);

            if (paymentType === 'card') {
                let cardNumber = $('#card-number').val();
                let cardExpiry = $('#card-expiry').val();
                let cardCvv = $('#card-cvv').val();

                if (!cardNumber || !cardExpiry || !cardCvv) {
                    toastr.error('Please enter all card details');
                    return;
                }
                
                let cardDetails = {
                    card_number: cardNumber,
                    card_expiry: cardExpiry,
                    card_cvv: cardCvv
                };
                formData.append('captured_details', JSON.stringify(cardDetails));
            } else {
                let adminBankId = $('#admin-bank-selector').val();
                let receiptFile = $('#payment-receipt')[0].files[0];

                if (!adminBankId) {
                    toastr.error("Please select admin's bank for payment");
                    return;
                }
                if (!receiptFile) {
                    toastr.error('Please upload payment receipt screenshot');
                    return;
                }

                formData.append('admin_bank_id', adminBankId);
                formData.append('payment_receipt', receiptFile);
                formData.append('transaction_id', $('#transaction-id').val());
                formData.append('account_holder_name', $('#account-holder-name').val());
                formData.append('transfer_amount', $('#transfer-amount').val());
                if (capturedDetails) {
                    formData.append('captured_details', capturedDetails);
                }
            }

            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Processing...');

            $.ajax({
                url: "{{ route('vouchers.order.post', $rule->id) }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        window.location.href = "{{ route('orders.history') }}";
                    }
                },
                error: function(xhr) {
                    let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Something went wrong';
                    toastr.error(msg);
                    btn.prop('disabled', false).html('Place Order');
                }
            });
        });
    });
</script>
@endpush
@endsection