@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Support Team Dashboard</h4>
        <div class="system-status">
            <span class="badge bg-success-subtle text-success p-2">
                <i class="fas fa-circle me-1 small"></i> System Operational
            </span>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 border-start border-primary border-4 h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Total Users</div>
                    <div class="d-flex align-items-center">
                        <h3 class="fw-bold mb-0">{{ $stats['total_users'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 border-start border-warning border-4 h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Pending Approvals</div>
                    <div class="d-flex align-items-center">
                        <h3 class="fw-bold mb-0">{{ $stats['pending_approvals'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 border-start border-info border-4 h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Active Students</div>
                    <div class="d-flex align-items-center">
                        <h3 class="fw-bold mb-0">{{ $stats['students'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Support Quick Actions -->
    <div class="row g-4">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Support Overview</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light">
                                <h6 class="fw-bold mb-2"><i class="fas fa-handshake text-primary me-2"></i> Reseller Agents</h6>
                                <p class="small text-muted mb-3">View and manage B2B reseller accounts.</p>
                                <a href="{{ route('reseller.agent') }}" class="btn btn-sm btn-outline-primary">View Resellers</a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light">
                                <h6 class="fw-bold mb-2"><i class="fas fa-user text-success me-2"></i> Regular Agents</h6>
                                <p class="small text-muted mb-3">Manage regular agent accounts and details.</p>
                                <a href="{{ route('regular.agent') }}" class="btn btn-sm btn-outline-success">View Agents</a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light">
                                <h6 class="fw-bold mb-2"><i class="fas fa-user-graduate text-info me-2"></i> Student Management</h6>
                                <p class="small text-muted mb-3">View student profiles and admission details.</p>
                                <a href="{{ route('student.page') }}" class="btn btn-sm btn-outline-info">View Students</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
