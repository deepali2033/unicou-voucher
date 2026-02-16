@extends('layouts.master')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 fw-bold">My Order History</h5>
                <small class="text-muted">Track your voucher purchases and referral points.</small>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary btn-sm" id="filter-btn">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                <a href="{{ route('orders.export') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-download me-1"></i> CSV
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 border-0">Order ID</th>
                            <th class="py-3 border-0">Voucher Type</th>
                            <th class="py-3 border-0">Bank</th>
                            <th class="py-3 border-0">Date</th>
                            <th class="py-3 border-0">Quantity</th>
                            <th class="py-3 border-0">Total Amount</th>
                            <th class="py-3 border-0">Points Earned</th>
                            <th class="py-3 border-0 text-center">Status</th>
                            <th class="px-4 py-3 border-0 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td class="px-4 fw-bold text-primary">{{ $order->order_id }}</td>
                            <td>{{ $order->voucher_type }}</td>
                            <td>
                                @if($order->bank_name)
                                    <span class="small text-muted"><i class="fas fa-university me-1"></i>{{ $order->bank_name }}</span>
                                @else
                                    <span class="text-muted small">N/A</span>
                                @endif
                            </td>
                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                            <td>{{ $order->quantity }}</td>
                            <td class="fw-bold">RS {{ number_format($order->amount, 0) }}</td>
                            <td>
                                <span class="badge bg-soft-success text-success" title="Referral: {{ $order->referral_points }}, Bonus: {{ (int)$order->bonus_amount }}">
                                    <i class="fas fa-coins me-1"></i>{{ $order->referral_points + (int)$order->bonus_amount }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($order->status == 'completed')
                                <span class="badge bg-success">Completed</span>
                                @elseif($order->status == 'pending')
                                <span class="badge bg-warning">Pending</span>
                                @else
                                <span class="badge bg-danger">{{ ucfirst($order->status) }}</span>
                                @endif
                            </td>
                            <td class="px-4 text-end">
                                <button class="btn btn-sm btn-light border view-order-btn"
                                    data-order="{{ json_encode($order) }}"
                                    title="View Details">
                                    <i class="fas fa-eye text-primary"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">No orders found.</td>
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
                <h5 class="modal-title fw-bold">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <div class="order-info">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Order ID:</span>
                        <span class="fw-bold" id="m-order-id"></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Voucher Type:</span>
                        <span id="m-voucher-type"></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Voucher ID:</span>
                        <span id="m-sku-id"></span>
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
                        <span class="text-muted">Points Earned:</span>
                        <span class="text-success fw-bold" id="m-points"></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Date:</span>
                        <span id="m-date"></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Status:</span>
                        <span id="m-status"></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Paid Via:</span>
                        <span class="fw-bold" id="m-bank"></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2" id="m-bank-details-row">
                        <span class="text-muted">Bank Details:</span>
                        <span class="small text-muted" id="m-bank-details"></span>
                    </div>
                    <hr>
                    <div class="mb-2">
                        <span class="text-muted d-block mb-1">Delivery Details:</span>
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
            $('#m-voucher-type').text(order.voucher_type);
            $('#m-sku-id').text(order.voucher_id || 'N/A');
            $('#m-quantity').text(order.quantity);
            $('#m-amount').text('RS ' + parseFloat(order.amount).toLocaleString());
            $('#m-points').text((parseInt(order.referral_points) || 0) + (parseInt(order.bonus_amount) || 0));
            $('#m-date').text(new Date(order.created_at).toLocaleDateString());
            $('#m-status').html('<span class="badge bg-' + (order.status === 'completed' ? 'success' : 'warning') + '">' + order.status.toUpperCase() + '</span>');
            $('#m-bank').text(order.bank_name || 'N/A');
            if (order.account_number) {
                $('#m-bank-details').text(order.account_number + (order.ifsc_code ? ' (' + order.ifsc_code + ')' : ''));
                $('#m-bank-details-row').show();
            } else {
                $('#m-bank-details-row').hide();
            }
            $('#m-delivery').text(order.delivery_details || 'Pending delivery...');

            $('#orderDetailModal').modal('show');
        });
    });
</script>

<style>
    .bg-soft-success {
        background-color: rgba(40, 167, 69, 0.1);
    }
</style>
@endpush
@endsection