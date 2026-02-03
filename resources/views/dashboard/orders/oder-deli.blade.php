@extends('layouts.master')

@section('content')
<div class="container-fluid py-4">
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

    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Order Deliveries</h4>
            <p class="text-muted small mb-0">Track and manage voucher deliveries for all users.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary bg-white shadow-sm px-3" style="border-radius: 10px;" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas">
                <i class="fas fa-filter me-2 small"></i> Filter
            </button>
            <a href="{{ route('orders.export', request()->all()) }}" class="btn btn-success shadow-sm px-4" style="border-radius: 10px; border: none;">
                <i class="fas fa-file-csv me-1"></i> Export CSV
            </a>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-uppercase small fw-bold text-muted">
                        <tr>
                            <th class="ps-4 py-3">Order Details</th>
                            <th class="py-3">Customer</th>
                            <th class="py-3">Amount</th>
                            <th class="py-3">Ref Points</th>
                            <th class="py-3">Bonus Points</th>
                            <th class="py-3">Status</th>
                            <th class="text-end pe-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr style="border-bottom: 1px solid #f1f3f5;">
                            <td class="ps-4 py-4">
                                <div class="fw-bold mb-1">{{ $order->order_id }}</div>
                                <div class="text-muted small mb-1">{{ $order->created_at->format('Y-m-d') }}</div>
                                <div class="text-muted extra-small">{{ $order->voucher->name ?? $order->voucher_type }}</div>
                            </td>
                            <td class="py-4">
                                <div class="fw-bold mb-1">{{ $order->user->name ?? 'Unknown' }}</div>
                                <div class="text-muted small">{{ $order->user->email ?? 'N/A' }}</div>
                                <div class="badge bg-light text-dark extra-small">{{ ucfirst($order->user_role ?? 'user') }}</div>
                            </td>
                            <td class="py-4">
                                <span class="fw-bold">RS {{ number_format($order->amount) }}</span>
                            </td>
                            <td class="py-4">
                                <span class="badge bg-soft-success text-success">{{ $order->referral_points }}</span>
                            </td>
                            <td class="py-4">
                                <span class="badge bg-soft-info text-info">{{ $order->bonus_amount }}</span>
                            </td>
                            <td class="py-4">
                                @if($order->status == 'delivered')
                                <span class="badge rounded-pill px-3 py-2" style="background-color: #e8f7f0; color: #198754; font-weight: 500;">Delivered</span>
                                @elseif($order->status == 'cancelled')
                                <span class="badge rounded-pill px-3 py-2" style="background-color: #fdeaea; color: #dc3545; font-weight: 500;">Cancelled</span>
                                @else
                                <span class="badge rounded-pill px-3 py-2" style="background-color: #fff4e6; color: #fd7e14; font-weight: 500;">{{ ucfirst($order->status) }}</span>
                                @endif
                            </td>
                            <td class="text-end pe-4 py-4">
                                <div class="d-flex justify-content-end gap-3">
                                    @if($order->status == 'pending' || $order->status == 'completed')
                                    <button class="btn btn-link text-decoration-none p-0 fw-bold" style="color: #5e5ce6; font-size: 0.85rem;" data-bs-toggle="modal" data-bs-target="#deliverModal{{ $order->id }}">
                                        Deliver Voucher
                                    </button>
                                    @endif
                                    @if($order->status != 'cancelled')
                                    <form action="{{ route('orders.cancel', $order->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-link text-decoration-none p-0 fw-bold" style="color: #ef9a9a; font-size: 0.85rem;" onclick="return confirm('Are you sure?')">
                                            Cancel
                                        </button>
                                    </form>
                                    @endif
                                    @if($order->status == 'delivered')
                                    <button class="btn btn-link text-decoration-none p-0 fw-bold" style="color: #5e5ce6; font-size: 0.85rem;" onclick="alert('Codes: {{ $order->delivery_details }}')">
                                        View Codes
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
                                            <button type="submit" class="btn btn-success px-4" style="border-radius: 10px; background-color: #6fc2a6; border: none;">Deliver Now</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">No orders found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Pagination Footer -->
        <div class="card-footer bg-white border-0 py-4 px-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} results
                </div>
                <div class="pagination-container">
                    {{ $orders->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-soft-success {
        background-color: rgba(40, 167, 69, 0.1);
    }

    .bg-soft-info {
        background-color: rgba(13, 202, 240, 0.1);
    }

    .extra-small {
        font-size: 0.75rem;
    }

    .table thead th {
        border-bottom: none;
    }

    .table td {
        vertical-align: middle;
    }

    .pagination {
        margin-bottom: 0;
    }

    .page-link {
        border: 1px solid #f1f3f5;
        color: #666;
        padding: 0.5rem 1rem;
        margin: 0 2px;
        border-radius: 8px !important;
    }

    .page-item.active .page-link {
        background-color: #5e5ce6;
        border-color: #5e5ce6;
    }

    .btn-link:hover {
        opacity: 0.8;
    }
</style>
@endsection