@extends('admin.layouts.app')

@section('title', 'Dispute Management')
@section('page-title', 'Customer Trust & Dispute Resolution')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-warning shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Open Disputes</h6>
                    <h2 class="text-warning">14</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Resolved Today</h6>
                    <h2 class="text-success">8</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-info shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Avg. Resolution Time</h6>
                    <h2 class="text-info">4.5h</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="koa-tb-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-black">Active Disputes & Refund Requests</h5>
            <div>
                <button class="btn btn-sm btn-outline-primary me-2">Export CSV</button>
                <button class="btn btn-sm btn-primary">Filter <i class="fas fa-filter"></i></button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Ticket ID</th>
                            <th>User</th>
                            <th>Amount</th>
                            <th>Reason</th>
                            <th>Raised Date</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#DSP-8821</td>
                            <td>
                                <div class="fw-bold">Alice Wonderland</div>
                                <small class="text-muted">Student</small>
                            </td>
                            <td>$45.00</td>
                            <td>Voucher already redeemed</td>
                            <td>2026-01-19 14:20</td>
                            <td><span class="badge bg-warning">Pending Review</span></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-success">Approve Refund</button>
                                <button class="btn btn-sm btn-danger">Reject</button>
                                <button class="btn btn-sm btn-outline-secondary">View Details</button>
                            </td>
                        </tr>
                        <tr>
                            <td>#DSP-8815</td>
                            <td>
                                <div class="fw-bold">Agent Zero</div>
                                <small class="text-muted">Agent</small>
                            </td>
                            <td>$120.00</td>
                            <td>Wrong product delivered</td>
                            <td>2026-01-18 09:15</td>
                            <td><span class="badge bg-info">Under Investigation</span></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-success">Approve Refund</button>
                                <button class="btn btn-sm btn-danger">Reject</button>
                                <button class="btn btn-sm btn-outline-secondary">View Details</button>
                            </td>
                        </tr>
                        <tr>
                            <td>#DSP-8799</td>
                            <td>
                                <div class="fw-bold">Bob Builder</div>
                                <small class="text-muted">Reseller</small>
                            </td>
                            <td>$250.00</td>
                            <td>System error during generation</td>
                            <td>2026-01-15 16:45</td>
                            <td><span class="badge bg-success">Resolved</span></td>
                            <td class="text-end">
                                <span class="text-success small fw-bold">Refunded <i class="fas fa-check-circle"></i></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
