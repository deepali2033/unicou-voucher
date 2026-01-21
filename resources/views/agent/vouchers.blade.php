@extends('agent.layout.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold">Vouchers</h2>
            <p class="text-muted">Manage and purchase vouchers for students.</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
                <h5 class="fw-bold mb-3">Available Vouchers</h5>
                <p>No vouchers available at the moment.</p>
                <button class="btn btn-primary">Purchase New Voucher</button>
            </div>
        </div>
    </div>
</div>
@endsection
