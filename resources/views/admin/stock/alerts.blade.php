@extends('admin.layouts.app')

@section('title', 'Low Stock Alert')
@section('page-title', 'Inventory Planning & Continuity')

@section('content')
<div class="container-fluid">
    <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
        <i class="fas fa-exclamation-triangle me-3 fs-4"></i>
        <div>
            <strong>Attention!</strong> There are 8 items currently below their minimum stock level.
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="koa-tb-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-black">Product Stock Monitoring</h5>
                    <button class="btn btn-primary btn-sm"><i class="fas fa-cog me-2"></i>Global Settings</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>Product ID</th>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Current Stock</th>
                                    <th>Minimum Level</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="table-danger">
                                    <td>#PRD-001</td>
                                    <td><strong>Amazon Gift Card USD 50</strong></td>
                                    <td>Gift Cards</td>
                                    <td>5</td>
                                    <td>20</td>
                                    <td><span class="badge bg-danger">Critical</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">Restock</button>
                                        <button class="btn btn-sm btn-outline-secondary">Edit Limit</button>
                                    </td>
                                </tr>
                                <tr class="table-warning">
                                    <td>#PRD-042</td>
                                    <td><strong>Steam Wallet Code USD 20</strong></td>
                                    <td>Gaming</td>
                                    <td>18</td>
                                    <td>25</td>
                                    <td><span class="badge bg-warning">Low Stock</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">Restock</button>
                                        <button class="btn btn-sm btn-outline-secondary">Edit Limit</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>#PRD-089</td>
                                    <td><strong>Netflix Subscription 1 Month</strong></td>
                                    <td>Subscriptions</td>
                                    <td>150</td>
                                    <td>50</td>
                                    <td><span class="badge bg-success">Healthy</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">Restock</button>
                                        <button class="btn btn-sm btn-outline-secondary">Edit Limit</button>
                                    </td>
                                </tr>
                                <tr class="table-danger">
                                    <td>#PRD-102</td>
                                    <td><strong>iTunes Gift Card USD 10</strong></td>
                                    <td>Gift Cards</td>
                                    <td>2</td>
                                    <td>15</td>
                                    <td><span class="badge bg-danger">Critical</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">Restock</button>
                                        <button class="btn btn-sm btn-outline-secondary">Edit Limit</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
