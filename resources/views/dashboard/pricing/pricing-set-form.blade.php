@extends('layouts.master')

@section('content')
@php $isEdit = isset($rule); @endphp
<style>
    /* Fix height & alignment */
    .select2-container--default .select2-selection--multiple,
    .select2-container--default .select2-selection--single {
        min-height: 42px;
        padding: 4px 8px;
        border: 1px solid #ced4da;
        border-radius: 6px;
    }

    /* Selected items style */
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #0d6efd;
        border: none;
        color: #fff;
        padding: 2px 8px;
        margin-top: 4px;
        font-size: 13px;
    }

    /* Clean Select2 Multiple Fix */
    .select2-container--default .select2-selection--multiple {
        min-height: 42px;
        padding: 4px 8px;
        border: 1px solid #ced4da;
        border-radius: 6px;
    }

    .select2-container {
        width: 100% !important;
    }

    /* Fix dropdown position */
    .select2-container--open {
        z-index: 9999;
    }

    /* Remove weird gray full-width highlight */
</style>

<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">{{ $isEdit ? 'Edit Pricing & Discount Rule' : 'Set Pricing & Discount Rule' }}</h5>
            <a href="{{ route('pricing.index') }}" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back to Rules
            </a>
        </div>
        <div class="card-body px-4">
            <form id="setPriceForm">
                @csrf
                @if($isEdit)
                <input type="hidden" name="id" value="{{ $rule->id }}">
                @endif
                <div class="row">
                    <div class="col-md-12 mb-4">
                        <label class="form-label fw-bold text-uppercase small">Select Voucher (Auto-fills details)</label>
                        <select name="inventory_voucher_id" id="voucherSelect" class="form-select select2" required>
                            <option value="">Choose a voucher from inventory...</option>
                            @foreach($vouchers as $v)
                            <option value="{{ $v->id }}" {{ ($isEdit && $rule->inventory_voucher_id == $v->id) ? 'selected' : '' }}>
                                {{ $v->brand_name }} ({{ $v->sku_id }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-uppercase small">Brand Name</label>
                        <input type="text" name="brand_name" id="brand_name" class="form-control" value="{{ $isEdit ? $rule->brand_name : '' }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-uppercase small">Currency</label>
                        <input type="text" name="currency" id="currency" class="form-control" value="{{ $isEdit ? $rule->currency : '' }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-uppercase small">Country/Region</label>
                        <input type="text" name="country_region" id="country_region" class="form-control" value="{{ $isEdit ? $rule->country_region : '' }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-uppercase small">Voucher Variant</label>
                        <input type="text" name="voucher_variant" id="voucher_variant" class="form-control" value="{{ $isEdit ? $rule->voucher_variant : '' }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-uppercase small">Voucher Type</label>
                        <input type="text" name="voucher_type" id="voucher_type" class="form-control" value="{{ $isEdit ? $rule->voucher_type : '' }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-uppercase small">Purchase Invoice No.</label>
                        <input type="text" name="purchase_invoice_no" id="purchase_invoice_no" class="form-control" value="{{ $isEdit ? $rule->purchase_invoice_no : '' }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-uppercase small">Purchase Date</label>
                        <input type="date" name="purchase_date" id="purchase_date" class="form-control" value="{{ $isEdit && $rule->purchase_date ? \Carbon\Carbon::parse($rule->purchase_date)->format('Y-m-d') : '' }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-uppercase small">Total Quantity</label>
                        <input type="number" name="total_quantity" id="total_quantity" class="form-control" value="{{ $isEdit ? $rule->total_quantity : '' }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-uppercase small">Purchase Value</label>
                        <input type="number" step="0.01" name="purchase_value" id="purchase_value" class="form-control" value="{{ $isEdit ? $rule->purchase_value : '' }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-uppercase small">Taxes</label>
                        <input type="number" step="0.01" name="taxes" id="taxes" class="form-control" value="{{ $isEdit ? $rule->taxes : '' }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-uppercase small">Per Unit Price</label>
                        <input type="number" step="0.01" name="per_unit_price" id="per_unit_price" class="form-control" value="{{ $isEdit ? $rule->per_unit_price : '' }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-uppercase small">Credit Limit</label>
                        <input type="number" step="0.01" name="credit_limit" id="credit_limit" class="form-control" value="{{ $isEdit ? $rule->credit_limit : '' }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-uppercase small">Issue Date</label>
                        <input type="date" name="issue_date" id="issue_date" class="form-control" value="{{ $isEdit && $rule->issue_date ? \Carbon\Carbon::parse($rule->issue_date)->format('Y-m-d') : '' }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-uppercase small">Expiry Date</label>
                        <input type="date" name="expiry_date" id="expiry_date" class="form-control" value="{{ $isEdit && $rule->expiry_date ? \Carbon\Carbon::parse($rule->expiry_date)->format('Y-m-d') : '' }}">
                    </div>
                </div>

                <hr class="my-4">

                <div class="row">
                    <div class="col-md-12 mb-4">
                        <label class="form-label fw-bold text-uppercase small">Select Target {{ $isEdit ? 'Country' : 'Countries (Multiple)' }}</label>
                        @if($isEdit)
                        <select name="country_code" id="countries" class="form-select select2" required>
                            @foreach($allCountries as $code => $name)
                            <option value="{{ $code }}" {{ $rule->country_code == $code ? 'selected' : '' }}>{{ $name }} ({{ $code }})</option>
                            @endforeach
                        </select>
                        @else
                        <select name="countries[]" id="countries" class="form-select select2" multiple required>
                            @foreach($allCountries as $code => $name)
                            <option value="{{ $code }}">{{ $name }} ({{ $code }})</option>
                            @endforeach
                        </select>
                        <small class="text-muted">You can apply this pricing rule to multiple countries at once.</small>
                        @endif
                    </div>
                </div>
                <hr class="my-4">

                <div class="row">
                    <div class="col-12 mb-3">
                        <h6 class="fw-bold text-uppercase">24 Hours Purchase Limit Per User Type</h6>
                        <small class="text-muted">Set maximum vouchers a user can buy within 24 hours.</small>
                    </div>

                    <!-- Agent Limit -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-uppercase small">
                            Agent Limit (24 Hours)
                        </label>
                        <input type="number"
                            name="agent_daily_limit"
                            class="form-control"
                            min="0"
                            placeholder="Enter limit"
                            value="{{ $isEdit ? $rule->agent_daily_limit : '' }}">
                    </div>

                    <!-- Student Limit -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-uppercase small">
                            Student Limit (24 Hours)
                        </label>
                        <input type="number"
                            name="student_daily_limit"
                            class="form-control"
                            min="0"
                            placeholder="Enter limit"
                            value="{{ $isEdit ? $rule->student_daily_limit : '' }}">
                    </div>

                    <!-- Reseller Agent Limit -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-uppercase small">
                            Reseller Agent Limit (24 Hours)
                        </label>
                        <input type="number"
                            name="reseller_daily_limit"
                            class="form-control"
                            min="0"
                            placeholder="Enter limit"
                            value="{{ $isEdit ? $rule->reseller_daily_limit : '' }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-uppercase small">Sale Price ($)</label>
                        <input type="number" step="0.01" name="sale_price" class="form-control" placeholder="0.00" value="{{ $isEdit ? $rule->sale_price : '' }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-uppercase small">Discount Type</label>
                        <select name="discount_type" class="form-select">
                            <option value="fixed" {{ ($isEdit && $rule->discount_type == 'fixed') ? 'selected' : '' }}>Fixed Amount ($)</option>
                            <option value="percentage" {{ ($isEdit && $rule->discount_type == 'percentage') ? 'selected' : '' }}>Percentage (%)</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-uppercase small">Discount Value</label>
                        <input type="number" step="0.01" name="discount_value" class="form-control" value="{{ $isEdit ? $rule->discount_value : '0' }}">
                    </div>
                </div>

                <div class="mt-4 pb-4 border-top pt-4 text-end">
                    <button type="submit" class="btn btn-primary px-5 shadow-sm" id="submitBtn">
                        <i class="fas fa-check-circle me-1"></i> {{ $isEdit ? 'Update Pricing Rule' : 'Save Pricing Rule' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize Select2 for all select elements with class 'select2'
        $('.select2').not('#countries').select2({
            width: '100%',
            allowClear: true
        });

        $('#countries').select2({
            width: '100%',
            allowClear: true,
            closeOnSelect: false,
            dropdownAutoWidth: true
        });
        $('#voucherSelect').on('change', function() {
            var voucherId = $(this).val();
            if (voucherId) {
                $.ajax({
                    url: "{{ route('pricing.voucher-details', ':id') }}".replace(':id', voucherId),
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            var data = response.data;
                            $('#brand_name').val(data.brand_name);
                            $('#currency').val(data.currency);
                            $('#country_region').val(data.country_region);
                            $('#voucher_variant').val(data.voucher_variant);
                            $('#voucher_type').val(data.voucher_type);
                            $('#purchase_invoice_no').val(data.purchase_invoice_no);
                            $('#purchase_date').val(data.purchase_date ? data.purchase_date.split('T')[0] : '');
                            $('#total_quantity').val(data.quantity);
                            $('#purchase_value').val(data.purchase_value);
                            $('#taxes').val(data.taxes);
                            $('#per_unit_price').val(data.purchase_value_per_unit);

                            // Auto-fill Sale Price with Agent Sale Price as default
                            if (data.agent_sale_price) {
                                $('input[name="sale_price"]').val(data.agent_sale_price);
                            }
                        }
                    }
                });
            }
        });

        $('#setPriceForm').on('submit', function(e) {
            e.preventDefault();

            var btn = $('#submitBtn');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> {{ $isEdit ? "Updating..." : "Saving..." }}');

            $.ajax({
                url: "{{ $isEdit ? route('pricing.update', $rule->id) : route('pricing.store') }}",
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        setTimeout(function() {
                            window.location.href = "{{ route('pricing.index') }}";
                        }, 1500);
                    } else {
                        toastr.error(response.message);
                        btn.prop('disabled', false).html('<i class="fas fa-check-circle me-1"></i> {{ $isEdit ? "Update Pricing Rule" : "Save Pricing Rule" }}');
                    }
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON.errors;
                    if (errors) {
                        Object.keys(errors).forEach(function(key) {
                            toastr.error(errors[key][0]);
                        });
                    } else {
                        toastr.error('Something went wrong. Please try again.');
                    }
                    btn.prop('disabled', false).html('<i class="fas fa-check-circle me-1"></i> {{ $isEdit ? "Update Pricing Rule" : "Save Pricing Rule" }}');
                }
            });
        });
    });
</script>
@endpush