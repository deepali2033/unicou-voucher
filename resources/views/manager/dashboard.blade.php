@extends('manager.layout.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Manager Dashboard</h4>
        <div class="system-status">
            <span class="badge bg-success-subtle text-success p-2">
                <i class="fas fa-circle me-1 small"></i> System Operational
            </span>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-primary border-4 h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Total Users</div>
                    <div class="d-flex align-items-center">
                        <h3 class="fw-bold mb-0">{{ $stats['total_users'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-warning border-4 h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Pending Approvals</div>
                    <div class="d-flex align-items-center">
                        <h3 class="fw-bold mb-0">{{ $stats['pending_approvals'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-success border-4 h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Active Vouchers</div>
                    <div class="d-flex align-items-center">
                        <h3 class="fw-bold mb-0">{{ $stats['active_vouchers'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-danger border-4 h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Low Stock Alerts</div>
                    <div class="d-flex align-items-center">
                        <h3 class="fw-bold mb-0">{{ $stats['low_stock_alerts'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Management Quick Actions -->
    <div class="row g-4">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Management Overview</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light">
                                <h6 class="fw-bold mb-2"><i class="fas fa-search-dollar text-primary me-2"></i> Audit Logs</h6>
                                <p class="small text-muted mb-3">Monitor all system transactions and activities.</p>
                                <a href="{{ route('manager.audit') }}" class="btn btn-sm btn-outline-primary">View Audit Logs</a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light">
                                <h6 class="fw-bold mb-2"><i class="fas fa-users-cog text-success me-2"></i> User Operations</h6>
                                <p class="small text-muted mb-3">Add, edit, or freeze system users and accounts.</p>
                                <a href="{{ route('manager.users') }}" class="btn btn-sm btn-outline-success">Manage Users</a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light">
                                <h6 class="fw-bold mb-2"><i class="fas fa-cubes text-info me-2"></i> Stock Control</h6>
                                <p class="small text-muted mb-3">Update voucher stocks and monitor alerts.</p>
                                <a href="{{ route('manager.vouchers.stock') }}" class="btn btn-sm btn-outline-info">Voucher Stocks</a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light">
                                <h6 class="fw-bold mb-2"><i class="fas fa-gavel text-warning me-2"></i> Disputes</h6>
                                <p class="small text-muted mb-3">Review refund requests and resolve disputes.</p>
                                <a href="{{ route('manager.disputes') }}" class="btn btn-sm btn-outline-warning text-dark">Review Disputes</a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light">
                                <h6 class="fw-bold mb-2"><i class="fas fa-stop-circle text-danger me-2"></i> Emergency Stop</h6>
                                <p class="small text-muted mb-3">Halt specific system components if needed.</p>
                                <a href="{{ route('manager.system.stop') }}" class="btn btn-sm btn-outline-danger">System Control</a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light">
                                <h6 class="fw-bold mb-2"><i class="fas fa-chart-bar text-secondary me-2"></i> Sales Reports</h6>
                                <p class="small text-muted mb-3">Generate item-wise and agent-wise reports.</p>
                                <a href="{{ route('manager.reports') }}" class="btn btn-sm btn-outline-secondary">View Reports</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Credit Management Info -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-info border-0 shadow-sm">
                <h6 class="fw-bold mb-1"><i class="fas fa-info-circle me-2"></i> Manager Notice:</h6>
                <p class="small mb-0">Managers can add credit to customers up to a maximum of <strong>USD 300</strong> per transaction. Any amount above this requires Admin approval.</p>
            </div>
        </div>
    </div>
</div>
@endsection
