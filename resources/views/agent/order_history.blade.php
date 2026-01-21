@extends('agent.layout.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold">Order History</h2>
            <p class="text-muted">Track and manage your recent orders.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">Order ID</th>
                            <th class="py-3">Date</th>
                            <th class="py-3">Voucher Type</th>
                            <th class="py-3">Amount</th>
                            <th class="py-3">Status</th>
                            <th class="px-4 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="px-4 py-3 text-muted">#ORD-001</td>
                            <td class="py-3">Oct 12, 2023</td>
                            <td class="py-3">Exam Voucher</td>
                            <td class="py-3 fw-bold">PKR 5,000</td>
                            <td class="py-3"><span class="badge bg-success bg-opacity-10 text-success px-3 py-2">Completed</span></td>
                            <td class="px-4 py-3">
                                <button class="btn btn-sm btn-outline-primary">View Details</button>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 text-muted">#ORD-002</td>
                            <td class="py-3">Oct 15, 2023</td>
                            <td class="py-3">Discount Coupon</td>
                            <td class="py-3 fw-bold">PKR 2,500</td>
                            <td class="py-3"><span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2">Pending</span></td>
                            <td class="px-4 py-3">
                                <button class="btn btn-sm btn-outline-primary">View Details</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
