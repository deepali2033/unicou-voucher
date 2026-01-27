@extends('admin.layout.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="fw-bold text-dark">Business Reports & Analytics</h2>
            <p class="text-muted">Overview of sales, inventory, and user performance.</p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-primary text-white h-100">
                <div class="card-body">
                    <h6 class="text-uppercase small fw-bold opacity-75">Total Sales</h6>
                    <h2 class="mb-0 fw-bold">${{ number_format($stats['total_sales'], 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-success text-white h-100">
                <div class="card-body">
                    <h6 class="text-uppercase small fw-bold opacity-75">Completed Orders</h6>
                    <h2 class="mb-0 fw-bold">{{ $stats['total_orders'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-warning text-dark h-100">
                <div class="card-body">
                    <h6 class="text-uppercase small fw-bold opacity-75">Pending Orders</h6>
                    <h2 class="mb-0 fw-bold">{{ $stats['pending_orders'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-info text-white h-100">
                <div class="card-body">
                    <h6 class="text-uppercase small fw-bold opacity-75">Active Vouchers</h6>
                    <h2 class="mb-0 fw-bold">{{ $stats['total_vouchers'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Orders -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Recent Transactions</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Voucher</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recent_orders as $order)
                                <tr>
                                    <td><span class="fw-bold">#{{ $order->order_id }}</span></td>
                                    <td>
                                        <div class="small fw-bold">{{ $order->user->name ?? 'N/A' }}</div>
                                        <div class="small text-muted">{{ $order->user->email ?? '' }}</div>
                                    </td>
                                    <td>{{ $order->voucher->name ?? 'N/A' }}</td>
                                    <td>${{ number_format($order->total_amount, 2) }}</td>
                                    <td>
                                        @php
                                            $badgeClass = match($order->status) {
                                                'delivered' => 'bg-success',
                                                'pending' => 'bg-warning text-dark',
                                                'cancelled' => 'bg-danger',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ ucfirst($order->status) }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No recent orders found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 text-center">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">View All Orders</a>
                </div>
            </div>
        </div>

        <!-- System Summary -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">System Summary</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Total Users</span>
                            <span class="badge bg-primary rounded-pill">{{ $stats['total_users'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Inventory Health</span>
                            <span class="text-success fw-bold">Optimal</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>System Status</span>
                            <span class="badge bg-success">Operational</span>
                        </li>
                    </ul>
                    
                    <div class="mt-4">
                        <h6 class="fw-bold mb-3">Quick Actions</h6>
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.vouchers.export') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-download me-2"></i> Export Voucher Inventory
                            </a>
                            <a href="{{ route('admin.users.pdf') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-file-pdf me-2"></i> Download User Report
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
