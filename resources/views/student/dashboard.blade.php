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
