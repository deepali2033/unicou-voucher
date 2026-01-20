@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Overall Business Analysis')

@section('content')
<div class="dash-card-bg">
    <div class="row">
        <!-- Stats Cards -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-stats card-stats-r text-white">
                <div class="card-body">
                    <div class="row pb-3">
                        <div class="icon icon-shape dash-icon-bg">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-9">
                            <h5 class="card-title mb-0 text-black text-muted">Total Sales</h5>
                        </div>
                        <div class="col-3 text-center">
                            <span class="h2 text-black mb-0 fs-3 font-weight-bold">$24,500</span>
                        </div>
                    </div>
                    <div class="mt-2 text-success small">
                        <i class="fas fa-arrow-up"></i> 12% Since last month
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-stats card-stats-r text-white">
                <div class="card-body">
                    <div class="row pb-3">
                        <div class="icon icon-shape dash-icon-bg">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-9">
                            <h5 class="card-title mb-0 text-black text-muted">Total Revenue</h5>
                        </div>
                        <div class="col-3 text-center">
                            <span class="h2 text-black mb-0 fs-3 font-weight-bold">$18,200</span>
                        </div>
                    </div>
                    <div class="mt-2 text-success small">
                        <i class="fas fa-arrow-up"></i> 8.5% Since last week
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-stats card-stats-l text-white">
                <div class="card-body">
                    <div class="row pb-3">
                        <div class="icon icon-shape dash-icon-bg">
                            <i class="fas fa-coins"></i>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-9">
                            <h5 class="card-title mb-0 text-black text-muted">Total Profit</h5>
                        </div>
                        <div class="col-3 text-center">
                            <span class="h2 text-black mb-0 fs-3 font-weight-bold">$6,300</span>
                        </div>
                    </div>
                    <div class="mt-2 text-danger small">
                        <i class="fas fa-arrow-down"></i> 3% Since last month
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-stats card-stats-l text-white">
                <div class="card-body">
                    <div class="row pb-3">
                        <div class="icon icon-shape dash-icon-bg">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-9">
                            <h5 class="card-title mb-0 text-black text-muted">Voucher Trends</h5>
                        </div>
                        <div class="col-3 text-center">
                            <span class="h2 text-black mb-0 fs-3 font-weight-bold">1,250</span>
                        </div>
                    </div>
                    <div class="mt-2 text-success small">
                        <i class="fas fa-arrow-up"></i> 15% Increase
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Revenue Comparison Chart Mockup -->
        <div class="col-lg-8 mb-4">
            <div class="koa-tb-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-black">Revenue Comparison (Up/Down Analysis)</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Monthly
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Daily</a></li>
                            <li><a class="dropdown-item" href="#">Weekly</a></li>
                            <li><a class="dropdown-item" href="#">Monthly</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div style="height: 300px; display: flex; align-items: flex-end; justify-content: space-around; padding-bottom: 20px;">
                        <!-- Mocking a bar chart with CSS -->
                        <div class="text-center">
                            <div class="bg-primary" style="width: 40px; height: 150px; border-radius: 5px 5px 0 0;"></div>
                            <span class="small">Jan</span>
                        </div>
                        <div class="text-center">
                            <div class="bg-primary" style="width: 40px; height: 180px; border-radius: 5px 5px 0 0;"></div>
                            <span class="small">Feb</span>
                        </div>
                        <div class="text-center">
                            <div class="bg-primary" style="width: 40px; height: 120px; border-radius: 5px 5px 0 0;"></div>
                            <span class="small">Mar</span>
                        </div>
                        <div class="text-center">
                            <div class="bg-primary" style="width: 40px; height: 210px; border-radius: 5px 5px 0 0;"></div>
                            <span class="small">Apr</span>
                        </div>
                        <div class="text-center">
                            <div class="bg-primary" style="width: 40px; height: 190px; border-radius: 5px 5px 0 0;"></div>
                            <span class="small">May</span>
                        </div>
                        <div class="text-center">
                            <div class="bg-primary" style="width: 40px; height: 240px; border-radius: 5px 5px 0 0;"></div>
                            <span class="small">Jun</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Overview -->
        <div class="col-lg-4 mb-4">
            <div class="koa-tb-card">
                <div class="card-header">
                    <h5 class="mb-0 text-black">Performance Overview</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small font-weight-bold">Daily Target</span>
                            <span class="small">75%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: 75%"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small font-weight-bold">Weekly Target</span>
                            <span class="small">60%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-info" style="width: 60%"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small font-weight-bold">Monthly Target</span>
                            <span class="small">85%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-warning" style="width: 85%"></div>
                        </div>
                    </div>
                    <hr>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary btn-sm">View Full Analytics</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
