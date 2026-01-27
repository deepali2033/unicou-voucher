@extends('admin.layout.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold"><i class="fas fa-truck me-2"></i> Orders & Delivery</h4>
            <p class="text-muted">Manage voucher deliveries, resend codes, and cancel orders.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Order ID</th>
                            <th>User</th>
                            <th>Voucher</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td class="ps-4 fw-bold text-primary">{{ $order->order_id }}</td>
                            <td>
                                <div class="fw-bold">{{ $order->user->name ?? 'Unknown' }}</div>
                                <div class="small text-muted">{{ $order->user->user_id ?? 'N/A' }}</div>
                            </td>
                            <td>{{ $order->voucher->name ?? $order->voucher_id ?? 'N/A' }}</td>
                            <td>PKR {{ number_format($order->amount) }}</td>
                            <td>
                                <span class="badge {{ $order->status == 'delivered' ? 'bg-success' : ($order->status == 'cancelled' ? 'bg-danger' : 'bg-warning') }}">
                                    {{ strtoupper($order->status) }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                @if($order->status == 'pending')
                                <button class="btn btn-sm btn-success px-3" data-bs-toggle="modal" data-bs-target="#deliverModal{{ $order->id }}">Deliver</button>
                                <form action="{{ route('admin.orders.cancel', $order->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-danger px-3">Cancel</button>
                                </form>
                                @elseif($order->status == 'delivered')
                                <button class="btn btn-sm btn-outline-info px-3" onclick="alert('Codes: {{ $order->delivery_details }}')">Resend</button>
                                @endif
                            </td>
                        </tr>

                        <!-- Deliver Modal -->
                        <div class="modal fade" id="deliverModal{{ $order->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <form action="{{ route('admin.orders.deliver', $order->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Deliver Voucher: {{ $order->order_id }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <label class="form-label">Voucher Codes (one per line)</label>
                                            <textarea name="codes" class="form-control" rows="5" required></textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success">Mark as Delivered</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">No orders found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
