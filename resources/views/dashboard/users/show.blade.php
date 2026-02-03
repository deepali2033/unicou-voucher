@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold">User Details</h4>
        <div class="d-flex gap-2">
            @if($user->profile_verification_status === 'pending')
            <form action="{{ route('approvals.approve', $user->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success" onclick="return confirm('Approve this user?')">
                    <i class="fas fa-check me-2"></i> Approve User
                </button>
            </form>
            @endif

            <form action="{{ route('users.suspend', $user->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn {{ $user->profile_verification_status === 'suspended' ? 'btn-outline-success' : 'btn-outline-warning' }}">
                    <i class="fas {{ $user->profile_verification_status === 'suspended' ? 'fa-play' : 'fa-pause' }} me-2"></i>
                    {{ $user->profile_verification_status === 'suspended' ? 'Unsuspend' : 'Suspend' }}
                </button>
            </form>

            <a href="{{ route('users.management') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body text-center py-4">
                    <img src="{{ $user->profile_photo ? asset('storage/' . $user->profile_photo) : asset('images/user.png') }}"
                        class="rounded-circle mb-3" width="100" height="100" alt="Avatar" style="object-fit: cover;">
                    <h5 class="fw-bold mb-1">{{ $user->first_name }} {{ $user->last_name }}</h5>
                    <p class="text-muted mb-3">{{ $user->email }}</p>
                    <span class="badge bg-primary px-3 py-2">{{ strtoupper(str_replace('_', ' ', $user->account_type)) }}</span>
                </div>
                <ul class="list-group list-group-flush border-top">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="text-muted">User ID</span>
                        <span class="fw-bold">{{ $user->user_id }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="text-muted">Status</span>
                        @if($user->profile_verification_status === 'verified')
                        <span class="badge bg-success">Verified</span>
                        @elseif($user->profile_verification_status === 'pending')
                        <span class="badge bg-warning text-dark">Pending</span>
                        @elseif($user->profile_verification_status === 'suspended')
                        <span class="badge bg-danger">Suspended</span>
                        @else
                        <span class="badge bg-secondary">{{ ucfirst($user->profile_verification_status) }}</span>
                        @endif
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="text-muted">Joined</span>
                        <span class="fw-bold">{{ $user->created_at->format('d M Y') }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Information</h5>
                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit me-1"></i> Edit Info
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-sm-4 text-muted">Full Name</div>
                        <div class="col-sm-8 fw-bold">{{ $user->first_name }} {{ $user->last_name }}</div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-sm-4 text-muted">Email</div>
                        <div class="col-sm-8 fw-bold">{{ $user->email }}</div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-sm-4 text-muted">Phone</div>
                        <div class="col-sm-8 fw-bold">{{ $user->phone ?? 'N/A' }}</div>
                    </div>
                    @if($user->company_name)
                    <div class="row mb-4">
                        <div class="col-sm-4 text-muted">Company</div>
                        <div class="col-sm-8 fw-bold">{{ $user->company_name }}</div>
                    </div>
                    @endif
                </div>
            </div>

            @if($user->account_type === 'reseller_agent' && $user->business_name)
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Agent Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Business Name</div>
                        <div class="col-sm-8 fw-bold">{{ $user->business_name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Business Type</div>
                        <div class="col-sm-8 fw-bold">{{ $user->business_type }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Registration No.</div>
                        <div class="col-sm-8 fw-bold">{{ $user->registration_number }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Website</div>
                        <div class="col-sm-8 fw-bold">{{ $user->website ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
            @endif

            @if($user->account_type === 'student' && $user->exam_purpose)
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Student Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Exam Purpose</div>
                        <div class="col-sm-8 fw-bold">{{ $user->exam_purpose }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Highest Education</div>
                        <div class="col-sm-8 fw-bold">{{ $user->highest_education }} ({{ $user->passing_year }})</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Preferred Countries</div>
                        <div class="col-sm-8 fw-bold">{{ $user->preferred_countries }}</div>
                    </div>
                </div>
            </div>
            @endif

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Security</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.password.update', $user->id) }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label small text-muted">Reset Password</label>
                                <input type="password" name="password" class="form-control form-control-sm" placeholder="New password" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label small text-muted">Confirm</label>
                                <input type="password" name="password_confirmation" class="form-control form-control-sm" placeholder="Confirm" required>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary btn-sm w-100">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @if(auth()->user()->account_type !== 'manager')
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-danger">Delete User</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Deleting this user will permanently remove all associated data.</p>
                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Delete permanently?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Delete Account</button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection