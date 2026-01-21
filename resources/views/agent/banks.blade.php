@extends('agent.layout.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold">Linked Banks</h2>
            <p class="text-muted">Manage your bank accounts for withdrawals and deposits.</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
                <h5 class="fw-bold mb-3">Your Bank Accounts</h5>
                <p>No bank accounts linked yet.</p>
                <button class="btn btn-outline-primary">Link New Bank Account</button>
            </div>
        </div>
    </div>
</div>
@endsection
