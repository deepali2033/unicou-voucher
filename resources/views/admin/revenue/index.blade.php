@extends('admin.layouts.app')

@section('title', 'Revenue Balances')
@section('page-title', 'Financial Transparency & Control')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Revenue Overview -->
        <div class="col-md-3 mb-4">
            <div class="card bg-primary text-white shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-uppercase small">Total Revenue</h6>
                    <h2 class="mb-0">$125,430</h2>
                    <div class="small mt-2"><i class="fas fa-arrow-up"></i> 5.2% from last month</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card bg-warning text-white shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-uppercase small">Pending Balances</h6>
                    <h2 class="mb-0">$12,850</h2>
                    <div class="small mt-2">Awaiting processing</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card bg-info text-white shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-uppercase small">Total Credits</h6>
                    <h2 class="mb-0">$5,200</h2>
                    <div class="small mt-2">Promotional & adjustments</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card bg-danger text-white shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-uppercase small">Refunds</h6>
                    <h2 class="mb-0">$2,450</h2>
                    <div class="small mt-2">Processed this month</div>
                </div>
            </div>
        </div>
    </div>

    <div class="koa-tb-card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-black">Earnings per User / Agent / Reseller</h5>
            <div class="input-group input-group-sm w-25">
                <input type="text" class="form-control" placeholder="Search user...">
                <button class="btn btn-outline-secondary"><i class="fas fa-search"></i></button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>User Name</th>
                            <th>Role</th>
                            <th>Total Sales</th>
                            <th>Revenue</th>
                            <th>Adjustments</th>
                            <th>Net Earnings</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>John Doe</td>
                            <td><span class="badge bg-info">Agent</span></td>
                            <td>$5,400</td>
                            <td>$4,800</td>
                            <td>-$50</td>
                            <td><strong>$4,750</strong></td>
                            <td><span class="badge bg-success">Paid</span></td>
                        </tr>
                        <tr>
                            <td>Sarah Smith</td>
                            <td><span class="badge bg-purple">Reseller</span></td>
                            <td>$12,300</td>
                            <td>$10,500</td>
                            <td>$0</td>
                            <td><strong>$10,500</strong></td>
                            <td><span class="badge bg-warning">Pending</span></td>
                        </tr>
                        <tr>
                            <td>Michael Brown</td>
                            <td><span class="badge bg-secondary">Student</span></td>
                            <td>$1,200</td>
                            <td>$1,000</td>
                            <td>-$20</td>
                            <td><strong>$980</strong></td>
                            <td><span class="badge bg-success">Paid</span></td>
                        </tr>
                        <tr>
                            <td>Tech Solutions</td>
                            <td><span class="badge bg-primary">Manager</span></td>
                            <td>$45,000</td>
                            <td>$40,000</td>
                            <td>-$500</td>
                            <td><strong>$39,500</strong></td>
                            <td><span class="badge bg-warning">Pending</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
