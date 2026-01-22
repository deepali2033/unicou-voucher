@extends('agent.layout.app')

@section('content')
<div class="container-fluid">
    <!-- Store Credit Card -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card bg-primary text-white p-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 opacity-75">Available Store Credit</p>
                        <h2 class="mb-0 fw-bold">{{ session('user_currency', 'PKR') }} 0</h2>
                    </div>
                    <a href="{{route('agent.deposit.store.credit')}}" class="btn btn-light px-4 py-2"
                        style="border-radius: 10px; color: #764ba2; font-weight: 600;">
                        Deposit
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Points Section -->
    <h5 class="mb-3 fw-bold">Current Points</h5>
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card p-4 border-0 shadow-sm" style="border-radius: 15px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="mb-0 text-muted">Quarterly Points</p>
                    <img src="https://cdn-icons-png.flaticon.com/512/179/179249.png" width="40" alt="Silver Medal">
                </div>
                <h3 class="fw-bold mb-1">0</h3>
                <p class="text-success small mb-0"><i class="fas fa-chart-line"></i> 0% <span class="text-muted">No change from past 7 days</span></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 border-0 shadow-sm" style="border-radius: 15px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="mb-0 text-muted">Yearly Points</p>
                    <img src="https://cdn-icons-png.flaticon.com/512/179/179250.png" width="40" alt="Gold Medal">
                </div>
                <h3 class="fw-bold mb-1">0</h3>
                <p class="text-success small mb-0"><i class="fas fa-chart-line"></i> 0% <span class="text-muted">No change from past 7 days</span></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 border-0 shadow-sm" style="border-radius: 15px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="mb-0 text-muted">Referral Points</p>
                    <div class="text-warning">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1">0</h3>
                <p class="text-success small mb-0"><i class="fas fa-chart-line"></i> 0% <span class="text-muted">No change from past 7 days</span></p>
            </div>
        </div>
    </div>

    <!-- Progress Section -->
    <div class="row">
        <div class="col-12">
            <h5 class="fw-bold">Your Progress ( Q1 )</h5>
            <p class="text-muted">Date Today : {{ $currentTime }}</p>
        </div>
    </div>
</div>
@endsection