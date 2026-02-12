<div class="table-responsive">
    <table class="table table-hover align-middle" style="white-space: nowrap;">
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
            @forelse($sales as $sale)
            <tr>
                <td>{{ ($sales->currentPage() - 1) * $sales->perPage() + $loop->iteration }}</td>
                <td class="fw-bold">{{ $sale->order_id }}</td>
                <td>{{ $sale->created_at->format('d M Y') }}</td>
                <td>{{ $sale->created_at->format('H:i A') }}</td>
                <td>
                    <span class="badge rounded-pill bg-light text-dark border px-3">
                        {{ ucfirst(str_replace('_', ' ', $sale->user_role)) }}
                    </span>
                </td>
                <td>{{ $sale->inventoryVoucher->currency ?? 'N/A' }}</td>
                <td>{{ $sale->inventoryVoucher->country_region ?? 'N/A' }}</td>
                <td>{{ $sale->inventoryVoucher->voucher_variant ?? 'N/A' }}</td>
                <td>{{ $sale->voucher_type }}</td>
                <td>{{ $sale->order_id }}</td> {{-- Using Order ID as Invoice No --}}
                <td>{{ $sale->quantity }}</td>
                <td>{{ $sale->inventoryVoucher->bank ?? 'N/A' }}</td>
                <td>{{ number_format($sale->inventoryVoucher->currency_conversion_rate ?? 0, 4) }}</td>
                <td>0.00</td> {{-- Placeholder for FX Gain/Loss --}}
                <td class="text-success fw-bold">{{ $sale->referral_points ?? 0 }}</td>
                <td class="text-danger fw-bold">{{ $sale->points_redeemed ?? 0 }}</td>
                <td class="text-primary fw-bold">{{ number_format($sale->bonus_amount ?? 0, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="17" class="text-center py-5 text-muted">No sales found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4 d-flex justify-content-center ajax-pagination">
    {{ $sales->links('pagination::bootstrap-5') }}
</div>