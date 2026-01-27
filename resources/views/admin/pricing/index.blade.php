@extends('admin.layout.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold"><i class="fas fa-tags me-2"></i> Pricing & Discounts</h4>
            <p class="text-muted">Set base prices, country-specific pricing, and discount rules.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
                <h5 class="fw-bold mb-4">Set New Pricing Rule</h5>
                <form action="{{ route('admin.pricing.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Voucher</label>
                        <select name="voucher_id" class="form-select" required>
                            @foreach($vouchers as $v)
                            <option value="{{ $v->voucher_id }}">{{ $v->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Country Code</label>
                        <input type="text" name="country_code" class="form-control" placeholder="e.g. PK, IN, ALL" value="ALL" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Base Price (PKR)</label>
                        <input type="number" name="base_price" class="form-control" placeholder="0.00" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Discounted Price (PKR)</label>
                        <input type="number" name="discount_price" class="form-control" placeholder="Optional">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mt-2">Update Pricing</button>
                </form>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">Active Pricing Rules</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Voucher ID</th>
                                    <th>Country</th>
                                    <th>Base Price</th>
                                    <th>Discount Price</th>
                                    <th class="text-end pe-4">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rules as $rule)
                                <tr>
                                    <td class="ps-4 fw-bold">{{ $rule->voucher_id }}</td>
                                    <td><span class="badge bg-secondary">{{ $rule->country_code }}</span></td>
                                    <td>PKR {{ number_format($rule->base_price) }}</td>
                                    <td class="text-success">{{ $rule->discount_price ? 'PKR ' . number_format($rule->discount_price) : '-' }}</td>
                                    <td class="text-end pe-4">
                                        <span class="badge bg-success">ACTIVE</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">No specific rules set.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
