@extends('student.layout.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold">Student Dashboard</h2>
            <p class="text-muted">Welcome to your personalized learning dashboard.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card p-4 border-0 shadow-sm" style="border-radius: 15px; background: #f8f9fa;">
                <div class="dash-icon-bg mb-3" style="font-size: 2rem; color: #198754;">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h4 class="fw-bold">Welcome, {{ Auth::user()->name }}</h4>
                <p class="text-muted mb-0">User ID: {{ Auth::user()->user_id }}</p>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card p-4 border-0 shadow-sm" style="border-radius: 15px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="mb-0 text-muted">Active Vouchers</p>
                    <i class="fas fa-ticket-alt fa-2x text-primary"></i>
                </div>
                <h3 class="fw-bold mb-1">0</h3>
                <p class="text-muted small mb-0">Vouchers available for use</p>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card p-4 border-0 shadow-sm" style="border-radius: 15px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="mb-0 text-muted">Completed Exams</p>
                    <i class="fas fa-check-circle fa-2x text-success"></i>
                </div>
                <h3 class="fw-bold mb-1">0</h3>
                <p class="text-muted small mb-0">Exams passed successfully</p>
            </div>
        </div>
    </div>
</div>
@endsection
