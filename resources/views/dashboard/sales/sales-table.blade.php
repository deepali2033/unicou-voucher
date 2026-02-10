<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Sr. No.</th>
                <th>Sale ID</th>
                <th>Date</th>
                <th>Time</th>
                <th>Buyer Type</th>
                <th>Local currency</th>
                <th>Country/Region</th>
                <th>Voucher Variant</th>
                <th>Voucher Type</th>
                <th>Sales Invoice No.</th>
                <th>Quantity</th>
                <th>Bank</th>
                <th>Currency Conversion @</th>
                <th>FX Gain/Loss</th>
                <th>Referral Points to Earned</th>
                <th>Referral Points Redeemed</th>
                <th>Customer Bonus Points</th>

            </tr>
        </thead>
        <tbody>
            @forelse($purchases as $index => $purchase)
            <tr>
                <td>{{ $purchases->firstItem() + $index }}</td>
                <td>{{ $purchase->sku_id }}</td>
                <td>{{ $purchase->purchase_date->format('Y-m-d') }}</td>
                <td>{{ $purchase->brand_name }}</td>
                <td>{{ $purchase->currency }}</td>
                <td>{{ $purchase->country_region }}</td>
                <td>{{ $purchase->voucher_variant }}</td>
                <td>{{ $purchase->voucher_type }}</td>
                <td>{{ $purchase->purchase_invoice_no }}</td>
                <td>{{ $purchase->quantity }}</td>
                <td>{{ $purchase->purchase_value }}</td>
                <td>{{ $purchase->taxes }}</td>
                <td>{{ $purchase->purchase_value_per_unit }}</td>
                <td>
                    <span class="badge {{ $purchase->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                        {{ ucfirst($purchase->status) }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="14" class="text-center py-4">No records found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4 d-flex justify-content-center ajax-pagination">
    {{ $purchases->appends(request()->all())->links('pagination::bootstrap-5') }}
</div>