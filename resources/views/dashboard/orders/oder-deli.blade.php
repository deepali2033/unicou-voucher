@extends('layouts.master')
@section('content')
<div class="container-fluid">

    {{-- Offcanvas Filter --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold" id="filterOffcanvasLabel">
                <i class="fas fa-filter me-2"></i>Filter Orders
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form id="filter-form" action="{{ route('orders.index') }}" method="GET">
                <div class="mb-4">
                    <label class="form-label fw-bold">Order ID</label>
                    <input type="text" name="order_id" class="form-control" placeholder="Search order ID..." value="{{ request('order_id') }}">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">User Role</label>
                    <select name="role" class="form-select">
                        <option value="">All Roles</option>
                        <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Student</option>
                        <option value="reseller_agent" {{ request('role') == 'reseller_agent' ? 'selected' : '' }}>Agent</option>
                    </select>
                </div>

                <div class="d-grid gap-2 pt-3 border-top">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="{{ route('orders.index') }}" class="btn btn-light">Reset All</a>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 fw-bold">Order Deliveries</h5>
                <small class="text-muted">{{ $orders->total() }} Orders Found</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('orders.export', request()->all()) }}" class="btn btn-success btn-sm px-3 shadow-sm">
                    <i class="fas fa-file-csv me-1"></i> CSV
                </a>
                <button class="btn btn-outline-primary btn-sm px-3 shadow-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Order Details</th>
                            <th>Customer</th>
                            <th>Payment</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $order->order_id }}</div>
                                <div class="text-muted small">{{ $order->created_at->format('d M Y H:i') }}</div>
                                <div class="text-muted small">{{ $order->voucher->name ?? $order->voucher_type }} (Qty: {{ $order->quantity }})</div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $order->user->name ?? 'Unknown' }}</div>
                                <div class="text-muted small">{{ $order->user->email ?? 'N/A' }}</div>
                                <span class="badge rounded-pill bg-light text-dark border px-2 mt-1">{{ ucfirst($order->user_role ?? 'user') }}</span>
                            </td>
                            <td>
                                <div class="small">
                                    <span class="text-muted d-block">Method: <span class="text-dark fw-bold text-capitalize">{{ str_replace('_', ' ', $order->payment_method) }}</span></span>
                                    <span class="text-muted d-block">Bank: <span class="text-dark">{{ $order->bank_name ?: 'N/A' }}</span></span>
                                    @if($order->payment_receipt)
                                    <a href="{{ asset('storage/'.$order->payment_receipt) }}" target="_blank" class="text-primary fw-bold small mt-1 d-inline-block">
                                        <i class="fas fa-file-invoice me-1"></i> View Receipt
                                    </a>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold text-primary">RS {{ number_format($order->amount) }}</span>
                            </td>
                            <td>
                                @if($order->status == 'delivered')
                                <span class="badge bg-success-subtle text-success px-3 py-2">Delivered</span>
                                @elseif($order->status == 'cancelled')
                                <span class="badge bg-danger-subtle text-danger px-3 py-2">Cancelled</span>
                                @elseif($order->status == 'pending')
                                <span class="badge bg-warning-subtle text-warning px-3 py-2">Pending</span>
                                @else
                                <span class="badge bg-primary-subtle text-primary px-3 py-2">{{ ucfirst($order->status) }}</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-1">
                                    @if($order->status == 'pending')
                                    <form action="{{ route('orders.approve', $order->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-light" title="Approve" onclick="return confirm('Approve this order?')">
                                            <i class="fas fa-check-circle text-success"></i>
                                        </button>
                                    </form>
                                    @endif

                                    @if($order->status == 'completed')
                                    <button class="btn btn-sm btn-light" title="Deliver" data-bs-toggle="modal" data-bs-target="#deliverModal{{ $order->id }}">
                                        <i class="fas fa-truck text-primary"></i>
                                    </button>
                                    @endif

                                    @if($order->status != 'cancelled' && $order->status != 'delivered')
                                    <button class="btn btn-sm btn-light" title="Cancel" data-bs-toggle="modal" data-bs-target="#cancelModal{{ $order->id }}">
                                        <i class="fas fa-times-circle text-danger"></i>
                                    </button>
                                    @endif

                                    @if($order->status == 'delivered')
                                    <button class="btn btn-sm btn-light" title="View Codes" onclick="alert('Codes: {{ $order->delivery_details }}')">
                                        <i class="fas fa-key text-info"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Deliver Modal -->
                        <div class="modal fade" id="deliverModal{{ $order->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <form action="{{ route('orders.deliver', $order->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-content border-0 shadow" style="border-radius: 15px;">
                                        <div class="modal-header border-0 pt-4 px-4">
                                            <h5 class="fw-bold">Deliver Voucher: {{ $order->order_id }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body px-4">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-muted text-uppercase">Voucher Codes</label>
                                                <textarea name="codes" class="form-control border-0 bg-light p-3" rows="5" placeholder="Enter codes (one per line)..." style="border-radius: 10px;" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pb-4 px-4">
                                            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal" style="border-radius: 10px;">Cancel</button>
                                            <button type="submit" class="btn btn-primary px-4" style="border-radius: 10px;">Deliver Now</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Cancel Modal -->
                        <div class="modal fade" id="cancelModal{{ $order->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <form action="{{ route('orders.cancel', $order->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-content border-0 shadow" style="border-radius: 15px;">
                                        <div class="modal-header border-0 pt-4 px-4">
                                            <h5 class="fw-bold text-danger">Cancel Order: {{ $order->order_id }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body px-4">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-muted text-uppercase">Reason for Cancellation</label>
                                                <textarea name="reason" class="form-control border-0 bg-light p-3" rows="3" placeholder="Explain why the order is being cancelled..." style="border-radius: 10px;" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pb-4 px-4">
                                            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal" style="border-radius: 10px;">Keep Order</button>
                                            <button type="submit" class="btn btn-danger px-4" style="border-radius: 10px;">Confirm Cancellation</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No orders found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0 py-4">
                <div class="d-flex justify-content-center">
                    {{ $orders->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
