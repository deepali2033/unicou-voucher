@extends('layouts.master')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Edit Voucher Stock</h2>
            <p class="text-muted mb-0">Update the details for voucher: <strong>{{ $inventory->brand_name }} ({{ $inventory->sku_id }})</strong></p>
        </div>
        <a href="{{ route('inventory.index') }}" class="btn btn-light rounded-pill px-4">
            <i class="fas fa-arrow-left me-2"></i> Back to Inventory
        </a>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
        <div class="card-body p-4">
            <form action="{{ route('inventory.update', $inventory->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-4">
                    <!-- Basic Info -->
                    <div class="col-12">
                        <h5 class="fw-bold mb-3 text-primary">Basic Information</h5>
                        <hr>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">SKU ID</label>
                        <input type="text" name="sku_id" id="sku_id" class="form-control @error('sku_id') is-invalid @enderror" value="{{ old('sku_id', $inventory->sku_id) }}" readonly required>
                        @error('sku_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Brand Name</label>
                        <input type="text" name="brand_name" id="brand_name" class="form-control" value="{{ old('brand_name', $inventory->brand_name) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Country/Region</label>
                        <select name="country_region" id="country" class="form-select" required>
                            <option value="{{ $inventory->country_region }}" selected>{{ $inventory->country_region }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">State</label>
                        <select name="state" id="state" class="form-select">
                            <option value="{{ $inventory->state }}" selected>{{ $inventory->state ?: 'Select State' }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Currency</label>
                        <input type="text" name="currency" id="currency" class="form-control" value="{{ old('currency', $inventory->currency) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Voucher Variant</label>
                        <input type="text" name="voucher_variant" id="voucher_variant" class="form-control" value="{{ old('voucher_variant', $inventory->voucher_variant) }}" placeholder="e.g. Subscription">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Voucher Type</label>
                        <input name="voucher_type" class="form-control" value="{{ old('voucher_type', $inventory->voucher_type) }}" required>


                    </div>
                    <div class=" col-md-6">
                        <label class="form-label small fw-bold text-uppercase">Logo Image</label>
                        <div class="d-flex gap-3 align-items-center">
                            @if($inventory->logo)
                            <img src="{{ $inventory->logo }}" alt="Logo" style="width: 40px; height: 40px; border-radius: 8px; object-fit: contain; background: #f8f9fa;">
                            @endif
                            <input type="file" name="logo" class="form-control" accept="image/*">
                        </div>
                    </div>

                    <!-- Purchase Details -->
                    <div class="col-12 mt-5">
                        <h5 class="fw-bold mb-3 text-primary">Purchase & Stock Details</h5>
                        <hr>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Purchase Invoice No.</label>
                        <input type="text" name="purchase_invoice_no" class="form-control" value="{{ old('purchase_invoice_no', $inventory->purchase_invoice_no) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Purchase Date</label>
                        <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date', $inventory->purchase_date ? $inventory->purchase_date->format('Y-m-d') : '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Quantity</label>
                        <input type="number" name="quantity" class="form-control bg-light" value="{{ old('quantity', $inventory->quantity) }}" readonly required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="IN STOCK" {{ $inventory->status == 'IN STOCK' ? 'selected' : '' }}>IN STOCK</option>

                            <option value="OUT OF STOCK" {{ $inventory->status == 'OUT OF STOCK' ? 'selected' : '' }}>OUT OF STOCK</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Purchase Value</label>
                        <input type="number" step="0.01" name="purchase_value" class="form-control" value="{{ old('purchase_value', $inventory->purchase_value) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Purchase Value / Unit</label>
                        <input type="number" step="0.01" name="purchase_value_per_unit" class="form-control" value="{{ old('purchase_value_per_unit', $inventory->purchase_value_per_unit) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Taxes</label>
                        <input type="number" step="0.01" name="taxes" class="form-control" value="{{ old('taxes', $inventory->taxes) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Bank Name</label>
                        <input type="text" name="bank" class="form-control" value="{{ old('bank', $inventory->bank) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Local Currency</label>
                        <input type="text" name="local_currency" class="form-control" value="{{ old('local_currency', $inventory->local_currency) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Currency Conv. @</label>
                        <input type="number" step="0.000001" name="currency_conversion_rate" class="form-control" value="{{ old('currency_conversion_rate', $inventory->currency_conversion_rate) }}">
                    </div>

                    <!-- Sale & Points Info -->
                    <div class="col-12 mt-5">
                        <h5 class="fw-bold mb-3 text-primary">Sale Price & Rewards</h5>
                        <hr>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-uppercase text-success">Agent Sale Price</label>
                        <input type="number" step="0.01" name="agent_sale_price" class="form-control border-success" value="{{ old('agent_sale_price', $inventory->agent_sale_price) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-uppercase text-success">Student Sale Price</label>
                        <input type="number" step="0.01" name="student_sale_price" class="form-control border-success" value="{{ old('student_sale_price', $inventory->student_sale_price) }}" required>
                    </div>



                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Referral Points (Reseller)</label>
                        <input type="number" name="referral_points_reseller" class="form-control" value="{{ old('referral_points_reseller', $inventory->referral_points_reseller) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Agent Ref. Points / Unit</label>
                        <input type="number" name="agent_referral_points_per_unit" class="form-control" value="{{ old('agent_referral_points_per_unit', $inventory->agent_referral_points_per_unit) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Agent Bonus Points / Unit</label>
                        <input type="number" name="agent_bonus_points_per_unit" class="form-control" value="{{ old('agent_bonus_points_per_unit', $inventory->agent_bonus_points_per_unit) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Student Ref. Points / Unit</label>
                        <input type="number" name="student_referral_points_per_unit" class="form-control" value="{{ old('student_referral_points_per_unit', $inventory->student_referral_points_per_unit) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Student Bonus Points / Unit</label>
                        <input type="number" name="student_bonus_points_per_unit" class="form-control" value="{{ old('student_bonus_points_per_unit', $inventory->student_bonus_points_per_unit) }}">
                    </div>

                    <div class="col-12 mt-4">
                        <label class="form-label small fw-bold text-uppercase">Voucher Codes / Upload Info</label>
                        <textarea name="upload_vouchers" class="form-control" rows="4" placeholder="Enter voucher codes or links here...">{{ old('upload_vouchers', is_array($inventory->upload_vouchers) ? implode(', ', $inventory->upload_vouchers) : $inventory->upload_vouchers) }}</textarea>
                    </div>

                    <div class="col-12 text-end mt-4">
                        <hr>
                        <a href="{{ route('inventory.index') }}" class="btn btn-light rounded-pill px-5">Cancel</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5">Update Voucher Stock</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
    $(document).ready(function() {
        const currentId = "{{ $inventory->id }}";

        function generateSKU() {
            let brand = $('#brand_name').val().trim();
            let variant = $('#voucher_variant').val().trim();
            let country = $('#country').val().trim();

            if (brand.length >= 1 && country.length >= 1) {
                // 1. Country Part (First 2-3 characters)
                let countryPart = country.substring(0, 3).toUpperCase();

                // 2. Brand Part (2 characters)
                let brandWords = brand.split(/\s+/).filter(w => w.length > 0);
                let brandPart = "";
                if (brandWords.length >= 2) {
                    brandPart = brandWords[0][0].toUpperCase() + brandWords[1][0].toUpperCase();
                } else if (brand.length >= 2) {
                    brandPart = brand.substring(0, 2).toUpperCase();
                } else {
                    brandPart = brand.toUpperCase() + "X";
                }

                // 3. Variant Part (2 characters)
                let variantPart = "VAR";
                if (variant) {
                    let variantWords = variant.split(/\s+/).filter(w => w.length > 0);
                    if (variantWords.length >= 2) {
                        variantPart = variantWords[0][0].toUpperCase() + variantWords[1][0].toUpperCase();
                    } else if (variant.length >= 2) {
                        variantPart = variant.substring(0, 2).toUpperCase();
                    } else {
                        variantPart = variant.toUpperCase() + "X";
                    }
                }

                // 4. Numeric ID
                let suffix = "001";
                let currentSku = "{{ $inventory->sku_id }}";
                if (currentSku.includes('-')) {
                    let parts = currentSku.split('-');
                    suffix = parts[parts.length - 1];
                }

                // Final SKU: COUNTRY-BRAND-VARIANT-001
                $('#sku_id').val(countryPart + "-" + brandPart + "-" + variantPart + "-" + suffix);
            }
        }

        $('#brand_name, #voucher_variant, #country').on('input change', function() {
            generateSKU();
        });

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

        function formatVouchers(val) {
            // Replace newlines, tabs, and spaces with a comma
            let cleaned = val.replace(/[\n\r\t\s]+/g, ',');
            // Replace multiple commas with a single comma
            cleaned = cleaned.replace(/,+/g, ',');
            // Trim leading/trailing commas
            cleaned = cleaned.replace(/^,|,$/g, '');

            // Calculate quantity based on number of codes
            let codes = cleaned.split(',').filter(code => code.trim().length > 0);
            let count = codes.length;

            // For display, use ", " for better readability
            let displayValue = codes.join(', ');
            
            return {
                display: displayValue,
                count: count
            };
        }

        // Auto-format voucher codes and update quantity
        $('textarea[name="upload_vouchers"]').on('input change blur', function() {
            let res = formatVouchers($(this).val());
            $(this).val(res.display);
            $('input[name="quantity"]').val(res.count);
            calculateUnitValue();
        });

        // Better handling for paste
        $('textarea[name="upload_vouchers"]').on('paste', function(e) {
            let self = this;
            setTimeout(function() {
                let res = formatVouchers($(self).val());
                $(self).val(res.display);
                $('input[name="quantity"]').val(res.count);
                calculateUnitValue();
            }, 100);
        });
    });
</script>

<script type="module">
    import {
        Country,
        State
    } from "https://cdn.jsdelivr.net/npm/country-state-city@3.2.1/+esm";

    document.addEventListener("DOMContentLoaded", function() {
        const countrySelect = document.getElementById("country");
        const stateSelect = document.getElementById("state");
        const initialCountry = "{{ $inventory->country_region }}";
        const initialState = "{{ $inventory->state }}";

        // Populate Countries
        const countries = Country.getAllCountries();
        countrySelect.innerHTML = '<option value="">Select Country</option>';
        countries.forEach(country => {
            const option = document.createElement('option');
            option.value = country.name;
            option.textContent = country.name;
            option.dataset.code = country.isoCode;
            if (country.name === initialCountry) {
                option.selected = true;
            }
            countrySelect.appendChild(option);
        });

        function updateStates(countryCode, selectedState = null) {
            stateSelect.innerHTML = '<option value="">Select State</option>';
            if (countryCode) {
                const states = State.getStatesOfCountry(countryCode);
                states.forEach(state => {
                    const option = document.createElement('option');
                    option.value = state.name;
                    option.textContent = state.name;
                    if (state.name === selectedState) {
                        option.selected = true;
                    }
                    stateSelect.appendChild(option);
                });
            }
        }

        // Initial states
        const initialCountryCode = countrySelect.options[countrySelect.selectedIndex]?.dataset.code;
        if (initialCountryCode) {
            updateStates(initialCountryCode, initialState);
        }

        // Country Change Event
        countrySelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const countryCode = selectedOption.dataset.code;
            updateStates(countryCode);
        });
    });
</script>
@endpush
@endsection