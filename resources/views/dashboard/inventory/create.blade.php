@extends('layouts.master')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Add New Voucher Stock</h2>
            <p class="text-muted mb-0">Fill in the details to add a new voucher to the inventory.</p>
        </div>
        <a href="{{ route('inventory.index') }}" class="btn btn-light rounded-pill px-4">
            <i class="fas fa-arrow-left me-2"></i> Back to Inventory
        </a>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
        <div class="card-body p-4">
            <form action="{{ route('inventory.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-4">
                    <!-- Basic Info -->
                    <div class="col-12">
                        <h5 class="fw-bold mb-3 text-primary">Basic Information</h5>
                        <hr>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">SKU ID</label>
                        <input type="text" name="sku_id" class="form-control @error('sku_id') is-invalid @enderror" value="{{ old('sku_id', $next_sku) }}" readonly required>
                        @error('sku_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Brand Name</label>
                        <input type="text" name="brand_name" class="form-control" value="{{ old('brand_name') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Country/Region</label>
                        <input type="text" name="country_region" class="form-control" value="{{ old('country_region') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Currency</label>
                        <input type="text" name="currency" class="form-control" value="{{ old('currency', 'PKR') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Voucher Variant</label>
                        <input type="text" name="voucher_variant" class="form-control" value="{{ old('voucher_variant') }}" placeholder="e.g. Subscription">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Voucher Type</label>
                        <input name="voucher_type" class="form-control" value="{{ old('voucher_type') }}" required>
                    </div>
                    <div class=" col-md-6">
                        <label class="form-label small fw-bold text-uppercase">Logo Image</label>
                        <input type="file" name="logo" class="form-control" accept="image/*">
                    </div>

                    <!-- Purchase Details -->
                    <div class="col-12 mt-5">
                        <h5 class="fw-bold mb-3 text-primary">Purchase & Stock Details</h5>
                        <hr>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Purchase Invoice No.</label>
                        <input type="text" name="purchase_invoice_no" class="form-control" value="{{ old('purchase_invoice_no') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Purchase Date</label>
                        <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Quantity</label>
                        <input type="number" name="quantity" class="form-control" value="{{ old('quantity', 0) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="IN STOCK">IN STOCK</option>

                            <option value="OUT OF STOCK">OUT OF STOCK</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Purchase Value</label>
                        <input type="number" step="0.01" name="purchase_value" class="form-control" value="{{ old('purchase_value', 0) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Purchase Value / Unit</label>
                        <input type="number" step="0.01" name="purchase_value_per_unit" class="form-control" value="{{ old('purchase_value_per_unit', 0) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Taxes</label>
                        <input type="number" step="0.01" name="taxes" class="form-control" value="{{ old('taxes', 0) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Bank Name</label>
                        <input type="text" name="bank" class="form-control" value="{{ old('bank') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Local Currency</label>
                        <input type="text" name="local_currency" class="form-control" value="{{ old('local_currency') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Currency Conv. @</label>
                        <input type="number" step="0.000001" name="currency_conversion_rate" class="form-control" value="{{ old('currency_conversion_rate', 1.0) }}">
                    </div>

                    <!-- Sale & Points Info -->
                    <div class="col-12 mt-5">
                        <h5 class="fw-bold mb-3 text-primary">Sale Price & Rewards</h5>
                        <hr>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-uppercase text-success">Agent Sale Price</label>
                        <input type="number" step="0.01" name="agent_sale_price" class="form-control border-success" value="{{ old('agent_sale_price', 0) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-uppercase text-success">Student Sale Price</label>
                        <input type="number" step="0.01" name="student_sale_price" class="form-control border-success" value="{{ old('student_sale_price', 0) }}" required>
                    </div>


                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Referral Points (Reseller)</label>
                        <input type="number" name="referral_points_reseller" class="form-control" value="{{ old('referral_points_reseller', 0) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Agent Ref. Points / Unit</label>
                        <input type="number" name="agent_referral_points_per_unit" class="form-control" value="{{ old('agent_referral_points_per_unit', 0) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Agent Bonus Points / Unit</label>
                        <input type="number" name="agent_bonus_points_per_unit" class="form-control" value="{{ old('agent_bonus_points_per_unit', 0) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Student Ref. Points / Unit</label>
                        <input type="number" name="student_referral_points_per_unit" class="form-control" value="{{ old('student_referral_points_per_unit', 0) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Student Bonus Points / Unit</label>
                        <input type="number" name="student_bonus_points_per_unit" class="form-control" value="{{ old('student_bonus_points_per_unit', 0) }}">
                    </div>

                    <div class="col-12 mt-4">
                        <label class="form-label small fw-bold text-uppercase">Voucher Codes / Upload Info</label>
                        <textarea name="upload_vouchers" class="form-control" rows="4" placeholder="Enter voucher codes separated by comma...">{{ old('upload_vouchers') }}</textarea>
                        <small class="text-muted">Enter multiple codes separated by commas (e.g., CODE1, CODE2, CODE3)</small>
                    </div>

                    <div class="col-12 text-end mt-4">
                        <hr>
                        <button type="reset" class="btn btn-light rounded-pill px-5">Reset</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-5">Save Voucher Stock</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
    $(document).ready(function() {
        function calculateUnitValue() {
            let quantity = parseFloat($('input[name="quantity"]').val()) || 0;
            let totalValue = parseFloat($('input[name="purchase_value"]').val()) || 0;
            let unitValue = 0;

            if (quantity > 0) {
                unitValue = totalValue / quantity;
            }

            $('input[name="purchase_value_per_unit"]').val(unitValue.toFixed(2));
        }

        $('input[name="quantity"], input[name="purchase_value"]').on('input', function() {
            calculateUnitValue();
        });
    });
</script>
@endpush
@endsection