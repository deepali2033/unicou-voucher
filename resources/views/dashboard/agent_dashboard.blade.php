@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Agent Dashboard</h4>
        <div class="system-status">
            <span class="badge bg-success-subtle text-success p-2">
                <i class="fas fa-circle me-1 small"></i> System Running
            </span>
        </div>
    </div>

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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if(session('shufti_response'))
        @php $response = session('shufti_response'); @endphp
        @if(isset($response['event']) && $response['event'] == 'verification.accepted')
            Swal.fire({
                title: 'Congratulations!',
                text: 'Your identity has been successfully verified. Your application is now pending admin approval. You will receive an email once it is approved.',
                icon: 'success',
                confirmButtonColor: '#23AAE2'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('home') }}";
                }
            });
        @elseif(isset($response['error']))
            Swal.fire({
                title: 'Verification Error',
                text: '{{ $response["error"] }}',
                icon: 'error',
                confirmButtonColor: '#d33'
            });
        @else
            Swal.fire({
                title: 'Registration Submitted',
                text: 'Your details have been sent for verification. Please wait for admin approval.',
                icon: 'info',
                confirmButtonColor: '#23AAE2'
            });
        @endif
    @endif
</script>
@endpush
@endsection
