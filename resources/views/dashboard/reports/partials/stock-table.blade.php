<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle mb-0">
        <thead class="text-center align-middle">
            <tr>
                <th rowspan="2" class="bg-light">Sr. No.</th>
                <th rowspan="2" class="bg-light">SKU ID</th>
                <th rowspan="2" class="bg-light">Voucher Type</th>
                <th colspan="3" style="background-color: #ffff00;">Total Purchased</th>
                <th colspan="3" style="background-color: #92d050;">In Stock</th>
                <th colspan="3" style="background-color: #00b0f0;">Sold</th>
                <th colspan="3" style="background-color: #ffc000;">Pending Delivery</th>
                <th colspan="3" style="background-color: #ff0000; color: white;">Cancelled / Refunded</th>
                <th colspan="3" style="background-color: #f8cbad;">Lost / Damaged</th>
            </tr>
            <tr>
                <th style="background-color: #ffff99;">Quantity</th>
                <th style="background-color: #ffff99;">Value (Buying Currency)</th>
                <th style="background-color: #ffff99;">Taxes Misc</th>
                
                <th style="background-color: #c6e0b4;">Quantity</th>
                <th style="background-color: #c6e0b4;">Value (Buying Currency)</th>
                <th style="background-color: #c6e0b4;">Taxes Misc</th>
                
                <th style="background-color: #9bc2e6;">Quantity</th>
                <th style="background-color: #9bc2e6;">Value (Buying Currency)</th>
                <th style="background-color: #9bc2e6;">Taxes Misc</th>
                
                <th style="background-color: #fdb913;">Quantity</th>
                <th style="background-color: #fdb913;">Value (Buying Currency)</th>
                <th style="background-color: #fdb913;">Taxes Misc</th>
                
                <th style="background-color: #ff8080;">Quantity</th>
                <th style="background-color: #ff8080;">Value (Buying Currency)</th>
                <th style="background-color: #ff8080;">Taxes Misc</th>
                
                <th style="background-color: #fce4d6;">Quantity</th>
                <th style="background-color: #fce4d6;">Value (Buying Currency)</th>
                <th style="background-color: #fce4d6;">Taxes Misc</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stock_data as $index => $item)
            <tr>
                <td class="text-center">{{ $stock_data->firstItem() + $index }}</td>
                <td class="fw-bold">{{ $item->sku_id }}</td>
                <td>{{ $item->voucher_type }}</td>
                
                <td class="text-center">{{ $item->total_purchased_qty }}</td>
                <td class="text-end">{{ number_format($item->total_purchased_value, 2) }}</td>
                <td class="text-end">{{ number_format($item->total_purchased_taxes, 2) }}</td>
                
                <td class="text-center">{{ $item->in_stock_qty }}</td>
                <td class="text-end">{{ number_format($item->in_stock_value, 2) }}</td>
                <td class="text-end">{{ number_format($item->in_stock_taxes, 2) }}</td>
                
                <td class="text-center">{{ $item->sold_qty }}</td>
                <td class="text-end">{{ number_format($item->sold_value, 2) }}</td>
                <td class="text-end">{{ number_format($item->sold_taxes, 2) }}</td>
                
                <td class="text-center">{{ $item->pending_qty }}</td>
                <td class="text-end">{{ number_format($item->pending_value, 2) }}</td>
                <td class="text-end">{{ number_format($item->pending_taxes, 2) }}</td>
                
                <td class="text-center">{{ $item->refunded_qty }}</td>
                <td class="text-end">{{ number_format($item->refunded_value, 2) }}</td>
                <td class="text-end">{{ number_format($item->refunded_taxes, 2) }}</td>
                
                <td class="text-center">{{ $item->lost_qty }}</td>
                <td class="text-end">{{ number_format($item->lost_value, 2) }}</td>
                <td class="text-end">{{ number_format($item->lost_taxes, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="21" class="text-center py-5 text-muted">No stock records found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="card-footer bg-white border-top-0 ajax-pagination">
    {{ $stock_data->links() }}
</div>
