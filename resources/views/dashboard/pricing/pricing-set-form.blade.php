@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Set Pricing & Discount Rule</h5>
            <a href="{{ route('pricing.index') }}" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back to Rules
            </a>
        </div>
        <div class="card-body px-4">
            <form id="setPriceForm">
                @csrf
                <div class="row">
                    <div class="col-md-12 mb-4">
                        <label class="form-label fw-bold text-uppercase small">Select Voucher (Auto-fills details)</label>
                        <select name="inventory_voucher_id" id="voucherSelect" class="form-select select2" required>
                            <option value="">Choose a voucher from inventory...</option>
                            @foreach($vouchers as $v)
                            <option value="{{ $v->id }}">{{ $v->brand_name }} ({{ $v->sku_id }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-uppercase small">Brand Name</label>
                        <input type="text" name="brand_name" id="brand_name" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-uppercase small">Currency</label>
                        <input type="text" name="currency" id="currency" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-uppercase small">Country/Region</label>
                        <input type="text" name="country_region" id="country_region" class="form-control">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-uppercase small">Voucher Variant</label>
                        <input type="text" name="voucher_variant" id="voucher_variant" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-uppercase small">Voucher Type</label>
                        <input type="text" name="voucher_type" id="voucher_type" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-uppercase small">Purchase Invoice No.</label>
                        <input type="text" name="purchase_invoice_no" id="purchase_invoice_no" class="form-control">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-uppercase small">Purchase Date</label>
                        <input type="date" name="purchase_date" id="purchase_date" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-uppercase small">Total Quantity</label>
                        <input type="number" name="total_quantity" id="total_quantity" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-uppercase small">Purchase Value</label>
                        <input type="number" step="0.01" name="purchase_value" id="purchase_value" class="form-control">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-uppercase small">Taxes</label>
                        <input type="number" step="0.01" name="taxes" id="taxes" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-uppercase small">Per Unit Price</label>
                        <input type="number" step="0.01" name="per_unit_price" id="per_unit_price" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-uppercase small">Credit Limit</label>
                        <input type="number" step="0.01" name="credit_limit" id="credit_limit" class="form-control">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-uppercase small">Issue Date</label>
                        <input type="date" name="issue_date" id="issue_date" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-uppercase small">Expiry Date</label>
                        <input type="date" name="expiry_date" id="expiry_date" class="form-control">
                    </div>
                </div>

                <hr class="my-4">

                <div class="row">
                    <div class="col-md-12 mb-4">
                        <label class="form-label fw-bold text-uppercase small">Select Target Countries (Multiple)</label>
                        <select name="countries[]" id="countries" class="form-select select2" multiple required>
                            @foreach($allCountries as $code => $name)
                            <option value="{{ $code }}">{{ $name }} ({{ $code }})</option>
                            @endforeach
                        </select>
                        <small class="text-muted">You can apply this pricing rule to multiple countries at once.</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-uppercase small">Sale Price ($)</label>
                        <input type="number" step="0.01" name="sale_price" class="form-control" placeholder="0.00" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-uppercase small">Discount Type</label>
                        <select name="discount_type" class="form-select">
                            <option value="fixed">Fixed Amount ($)</option>
                            <option value="percentage">Percentage (%)</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-uppercase small">Discount Value</label>
                        <input type="number" step="0.01" name="discount_value" class="form-control" value="0">
                    </div>
                </div>

                <div class="mt-4 pb-4 border-top pt-4 text-end">
                    <button type="submit" class="btn btn-primary px-5 shadow-sm" id="submitBtn">
                        <i class="fas fa-check-circle me-1"></i> Save Pricing Rule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2({
        width: '100%',
        placeholder: "Select options"
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
                    }
                }
            });
        }
    });

    $('#setPriceForm').on('submit', function(e) {
        e.preventDefault();
        
        var btn = $('#submitBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

        $.ajax({
            url: "{{ route('pricing.store') }}",
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
                    btn.prop('disabled', false).html('<i class="fas fa-check-circle me-1"></i> Save Pricing Rule');
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
                btn.prop('disabled', false).html('<i class="fas fa-check-circle me-1"></i> Save Pricing Rule');
            }
        });
    });
});
</script>
@endsection
