@extends('admin.layouts.app')

@section('title', 'Reports Center')
@section('page-title', 'Business Intelligence & Insights')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sales & Stock Reports -->
        <div class="col-md-4 mb-4">
            <div class="koa-tb-card h-100">
                <div class="card-header">
                    <h5 class="mb-0 text-black"><i class="fas fa-shopping-cart me-2 text-primary"></i>Sales & Inventory</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Sales Report (Item-wise)
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-secondary"><i class="fas fa-file-pdf"></i></button>
                                <button class="btn btn-outline-secondary"><i class="fas fa-file-csv"></i></button>
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Current Stock Status
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-secondary"><i class="fas fa-file-pdf"></i></button>
                                <button class="btn btn-outline-secondary"><i class="fas fa-file-csv"></i></button>
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Sold Stock Analysis
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-secondary"><i class="fas fa-file-pdf"></i></button>
                                <button class="btn btn-outline-secondary"><i class="fas fa-file-csv"></i></button>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Financial Reports -->
        <div class="col-md-4 mb-4">
            <div class="koa-tb-card h-100">
                <div class="card-header">
                    <h5 class="mb-0 text-black"><i class="fas fa-file-invoice-dollar me-2 text-success"></i>Financial Reports</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Revenue & Profit Analysis
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-secondary"><i class="fas fa-file-pdf"></i></button>
                                <button class="btn btn-outline-secondary"><i class="fas fa-file-csv"></i></button>
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Loss & Adjustment Report
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-secondary"><i class="fas fa-file-pdf"></i></button>
                                <button class="btn btn-outline-secondary"><i class="fas fa-file-csv"></i></button>
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center text-danger">
                            <strong>Users & Passwords (Admin Only)</strong>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-danger"><i class="fas fa-lock"></i></button>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Performance Reports -->
        <div class="col-md-4 mb-4">
            <div class="koa-tb-card h-100">
                <div class="card-header">
                    <h5 class="mb-0 text-black"><i class="fas fa-user-chart me-2 text-info"></i>Performance Tracking</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Staff Performance (Managers/Support)
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-secondary"><i class="fas fa-eye"></i></button>
                                <button class="btn btn-outline-secondary"><i class="fas fa-download"></i></button>
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Entity Performance (Agents/Resellers)
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-secondary"><i class="fas fa-eye"></i></button>
                                <button class="btn btn-outline-secondary"><i class="fas fa-download"></i></button>
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Referral & Bonus Report
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-secondary"><i class="fas fa-eye"></i></button>
                                <button class="btn btn-outline-secondary"><i class="fas fa-download"></i></button>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Documents & Compliance -->
        <div class="col-md-6 mb-4">
            <div class="koa-tb-card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-file-signature me-2"></i>Documents & Compliance Download</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>Category</th>
                                <th>Count</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>User Agreements</td>
                                <td>1,420</td>
                                <td class="text-end"><button class="btn btn-sm btn-outline-primary"><i class="fas fa-download"></i> ZIP</button></td>
                            </tr>
                            <tr>
                                <td>KYC Documents (Approved)</td>
                                <td>850</td>
                                <td class="text-end"><button class="btn btn-sm btn-outline-primary"><i class="fas fa-download"></i> ZIP</button></td>
                            </tr>
                            <tr>
                                <td>Agent Contracts</td>
                                <td>45</td>
                                <td class="text-end"><button class="btn btn-sm btn-outline-primary"><i class="fas fa-download"></i> ZIP</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Role Based Downloads -->
        <div class="col-md-6 mb-4">
            <div class="koa-tb-card">
                <div class="card-header">
                    <h5 class="mb-0 text-black"><i class="fas fa-download me-2"></i>Bulk Record Downloads</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6"><button class="btn btn-outline-secondary w-100 text-start"><i class="fas fa-user-graduate me-2"></i>Students List</button></div>
                        <div class="col-6"><button class="btn btn-outline-secondary w-100 text-start"><i class="fas fa-user-tie me-2"></i>Agents List</button></div>
                        <div class="col-6"><button class="btn btn-outline-secondary w-100 text-start"><i class="fas fa-university me-2"></i>Academic Institutes</button></div>
                        <div class="col-6"><button class="btn btn-outline-secondary w-100 text-start"><i class="fas fa-headset me-2"></i>Support Agents</button></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
