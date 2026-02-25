@extends('layouts.master')

@section('content')
<div class="container-fluid py-4">
    {{-- Offcanvas Filter --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold" id="filterOffcanvasLabel">
                <i class="fas fa-filter me-2"></i>Filter Purchases
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form id="filter-form" action="{{ route('orders.history') }}" method="GET">
                <div class="mb-4">
                    <label class="form-label fw-bold">Date Wise Report (From - To)</label>
                    <div class="d-flex flex-column gap-2">
                        <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                        <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Voucher Purchases Summary (Currency)</label>
                    <select name="currency" class="form-select">
                        <option value="">All Currencies</option>
                        <option value="USD" {{ request('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                        <option value="GBP" {{ request('currency') == 'GBP' ? 'selected' : '' }}>GBP</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Brand Purchase Report</label>
                    <input type="text" name="brand_name" class="form-control" placeholder="Brand Name" value="{{ request('brand_name') }}">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Variant Purchase Report</label>
                    <input type="text" name="voucher_variant" class="form-control" placeholder="Variant" value="{{ request('voucher_variant') }}">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Exam Type (Online/Center)</label>
                    <select name="voucher_type" class="form-select">
                        <option value="">All Types</option>
                        <option value="Online" {{ request('voucher_type') == 'Online' ? 'selected' : '' }}>Online</option>
                        <option value="Center" {{ request('voucher_type') == 'Center' ? 'selected' : '' }}>Center</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Supplier Region (UK/USA)</label>
                    <select name="country_region" class="form-select">
                        <option value="">All Regions</option>
                        <option value="UK" {{ request('country_region') == 'UK' ? 'selected' : '' }}>UK</option>
                        <option value="USA" {{ request('country_region') == 'USA' ? 'selected' : '' }}>USA</option>
                    </select>
                </div>

                <div class="d-grid gap-2 pt-3 border-top">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="{{ route('orders.history') }}" class="btn btn-light">Reset All</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 fw-bold">My Purchase History</h5>
                <small class="text-muted">Track your voucher purchases and referral points.</small>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary btn-sm px-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                <a href="{{ route('orders.user_export', request()->all()) }}" class="btn btn-success btn-sm px-3">
                    <i class="fas fa-file-csv me-1"></i> Export CSV
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-nowrap ps-4">Sr. No.</th>
                            <th class="text-nowrap">Purchase ID</th>
                            <th class="text-nowrap">Date</th>
                            <th class="text-nowrap">Time</th>
                            <th class="text-nowrap">Brand Name</th>
                            <th class="text-nowrap">Currency</th>
                            <th class="text-nowrap">Country/Region</th>
                            <th class="text-nowrap">Voucher Variant</th>
                            <th class="text-nowrap">Voucher Type</th>
                            <th class="text-nowrap">Purchase Invoice No.</th>
                            <th class="text-nowrap">Purchase Date</th>
                            <th class="text-nowrap">Total Quantity</th>
                            <th class="text-nowrap text-end">Purchase Value</th>
                            <th class="text-nowrap">Taxes</th>
                            <th class="text-nowrap text-end">Per Unit Price</th>
                            <th class="text-nowrap">Issue Date</th>
                            <th class="text-nowrap">Expiry Date</th>
                            <th class="text-nowrap">Credit Limit</th>
                            <th class="text-nowrap text-center">Status</th>
                            <th class="text-nowrap text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td class="ps-4">{{ $loop->iteration + ($orders->currentPage() - 1) * $orders->perPage() }}</td>
                            <td class="fw-bold text-primary">{{ $order->order_id }}</td>
                            <td class="text-nowrap">{{ $order->created_at->format('d-m-Y') }}</td>
                            <td class="text-nowrap">{{ $order->created_at->format('H:i:s') }}</td>
                            <td>{{ $order->inventoryVoucher->brand_name ?? 'N/A' }}</td>
                            <td>{{ $order->inventoryVoucher->currency ?? 'N/A' }}</td>
                            <td>{{ $order->inventoryVoucher->country_region ?? 'N/A' }}</td>
                            <td>{{ $order->inventoryVoucher->voucher_variant ?? 'N/A' }}</td>
                            <td>{{ $order->inventoryVoucher->voucher_type ?? 'N/A' }}</td>
                            <td>{{ $order->order_id }}</td>
                            <td class="text-nowrap">{{ $order->created_at->format('d-m-Y') }}</td>
                            <td class="text-center">{{ $order->quantity }}</td>
                            <td class="text-end fw-bold text-dark">RS {{ number_format($order->amount, 2) }}</td>
                            <td>0.00</td>
                            <td class="text-end">RS {{ number_format($order->amount / $order->quantity, 2) }}</td>
                            <td class="text-nowrap">{{ $order->created_at->format('d-m-Y') }}</td>
                            <td class="text-nowrap">{{ $order->inventoryVoucher->expiry_date ? $order->inventoryVoucher->expiry_date->format('d-m-Y') : 'N/A' }}</td>
                            <td>{{ auth()->user()->voucher_limit ?? 'N/A' }}</td>
                            <td class="text-center">
                                @if($order->status == 'delivered')
                                <span class="badge bg-success">Delivered</span>
                                @elseif($order->status == 'completed')
                                <span class="badge bg-info">Completed</span>
                                @elseif($order->status == 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                                @else
                                <span class="badge bg-danger">{{ ucfirst($order->status) }}</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('purches.invoice', $order->order_id) }}" class="btn btn-sm btn-light border" title="Invoice">
                                        <i class="fas fa-file-invoice text-info"></i>
                                    </a>
                                    <button class="btn btn-sm btn-light border view-order-btn"
                                        data-order="{{ json_encode($order->load('inventoryVoucher')) }}"
                                        title="View Details">
                                        <i class="fas fa-eye text-primary"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="20" class="text-center py-5 text-muted">No purchases found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($orders->hasPages())
        <div class="card-footer bg-white border-0">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Order Detail Modal -->
<div class="modal fade" id="orderDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-header border-0 px-4 pt-4">
                <h5 class="modal-title fw-bold">Purchase Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <div class="order-info">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Purchase ID:</span>
                        <span class="fw-bold" id="m-order-id"></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Brand Name:</span>
                        <span class="fw-bold" id="m-brand-name"></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Voucher Type:</span>
                        <span id="m-voucher-type"></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Quantity:</span>
                        <span id="m-quantity"></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Amount:</span>
                        <span class="fw-bold text-primary" id="m-amount"></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Date:</span>
                        <span id="m-date"></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Status:</span>
                        <span id="m-status"></span>
                    </div>
                    <hr>
                    <div class="mb-2">
                        <span class="text-muted d-block mb-1">Delivered Codes:</span>
                        <div class="bg-light p-3 rounded" id="m-delivery"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('.view-order-btn').on('click', function() {
            const order = $(this).data('order');

            $('#m-order-id').text(order.order_id);
            $('#m-brand-name').text(order.inventory_voucher ? order.inventory_voucher.brand_name : 'N/A');
            $('#m-voucher-type').text(order.voucher_type);
            $('#m-quantity').text(order.quantity);
            $('#m-amount').text('RS ' + parseFloat(order.amount).toLocaleString());
            $('#m-date').text(new Date(order.created_at).toLocaleDateString());
            let statusBadge = '';
            if (order.status === 'delivered') statusBadge = '<span class="badge bg-success">DELIVERED</span>';
            else if (order.status === 'completed') statusBadge = '<span class="badge bg-info">COMPLETED</span>';
            else if (order.status === 'pending') statusBadge = '<span class="badge bg-warning text-dark">PENDING</span>';
            else statusBadge = '<span class="badge bg-danger">' + order.status.toUpperCase() + '</span>';

            $('#m-status').html(statusBadge);
            $('#m-delivery').text(order.delivery_details || 'Pending delivery...');

            $('#orderDetailModal').modal('show');
        });
    });
</script>

<style>
    th {
        font-size: 0.8rem;
        background-color: #f8f9fa;
    }

    td {
        font-size: 0.85rem;
    }

    .bg-soft-success {
        background-color: rgba(40, 167, 69, 0.1);
    }
</style>
@endpush
@endsection