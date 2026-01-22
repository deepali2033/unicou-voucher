@extends('admin.layout.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold">User Details</h4>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
        </a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body text-center py-4">
                    <img src="{{ asset('images/user.png') }}" class="rounded-circle mb-3" width="100" height="100" alt="Avatar">
                    <h5 class="fw-bold mb-1">{{ $user->first_name }} {{ $user->last_name }}</h5>
                    <p class="text-muted mb-3">{{ $user->email }}</p>
                    <span class="badge bg-primary px-3 py-2">{{ strtoupper($user->account_type) }}</span>
                </div>
                <ul class="list-group list-group-flush border-top">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="text-muted">User ID</span>
                        <span class="fw-bold">{{ $user->user_id }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="text-muted">Country</span>
                        <span class="fw-bold">{{ $user->country_iso }} ({{ $user->country_code }})</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="text-muted">Status</span>
                        <span class="badge bg-success">Active</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Full Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-sm-4 text-muted">First Name</div>
                        <div class="col-sm-8 fw-bold">{{ $user->first_name ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-sm-4 text-muted">Last Name</div>
                        <div class="col-sm-8 fw-bold">{{ $user->last_name ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-sm-4 text-muted">Company Name</div>
                        <div class="col-sm-8 fw-bold">{{ $user->company_name ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-sm-4 text-muted">Email Address</div>
                        <div class="col-sm-8 fw-bold">{{ $user->email }}</div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-sm-4 text-muted">Phone Number</div>
                        <div class="col-sm-8 fw-bold">{{ $user->phone }}</div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-sm-4 text-muted">Account Created</div>
                        <div class="col-sm-8 fw-bold">{{ $user->created_at->format('d F Y, h:i A') }}</div>
                    </div>
                    <div class="row mb-0">
                        <div class="col-sm-4 text-muted">Email Verified</div>
                        <div class="col-sm-8 fw-bold">
                            @if($user->email_verified_at)
                                <span class="text-success"><i class="fas fa-check-circle me-1"></i> {{ $user->email_verified_at->format('d M Y') }}</span>
                            @else
                                <span class="text-danger"><i class="fas fa-times-circle me-1"></i> Not Verified</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-danger">Danger Zone</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Deleting a user is permanent and cannot be undone. All associated data will be removed.</p>
                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i> Delete User Account
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
