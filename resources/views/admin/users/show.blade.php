@extends('admin.layout.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold">User Details</h4>
        <div class="d-flex gap-2">
            @if($user->profile_verification_status === 'pending')
                <form action="{{ route('admin.approvals.approve', $user->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success" onclick="return confirm('Approve this user?')">
                        <i class="fas fa-check me-2"></i> Approve User
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
            </a>
        </div>
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
                        @if($user->profile_verification_status === 'verified')
                            <span class="badge bg-success">Verified</span>
                        @elseif($user->profile_verification_status === 'pending')
                            <span class="badge bg-warning text-dark">Pending Approval</span>
                        @else
                            <span class="badge bg-secondary">Unknown</span>
                        @endif
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

            @if($user->account_type === 'reseller_agent' && $user->agentDetail)
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Agent Business Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Business Name</div>
                        <div class="col-sm-8 fw-bold">{{ $user->agentDetail->business_name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Business Type</div>
                        <div class="col-sm-8 fw-bold">{{ $user->agentDetail->business_type }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Registration Number</div>
                        <div class="col-sm-8 fw-bold">{{ $user->agentDetail->registration_number }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Contact / Email</div>
                        <div class="col-sm-8 fw-bold">{{ $user->agentDetail->business_contact }} / {{ $user->agentDetail->business_email }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Address</div>
                        <div class="col-sm-8 fw-bold">{{ $user->agentDetail->address }}, {{ $user->agentDetail->city }}, {{ $user->agentDetail->state }}, {{ $user->agentDetail->country }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Representative</div>
                        <div class="col-sm-8 fw-bold">{{ $user->agentDetail->representative_name }} ({{ $user->agentDetail->designation }})</div>
                    </div>
                    <hr>
                    <h6 class="fw-bold mb-3">Documents</h6>
                    <div class="d-flex gap-2">
                        @if($user->agentDetail->registration_doc)
                            <a href="{{ asset('storage/' . $user->agentDetail->registration_doc) }}" target="_blank" class="btn btn-sm btn-outline-info">Business Reg Doc</a>
                        @endif
                        @if($user->agentDetail->id_doc)
                            <a href="{{ asset('storage/' . $user->agentDetail->id_doc) }}" target="_blank" class="btn btn-sm btn-outline-info">Representative ID</a>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            @if($user->account_type === 'student' && $user->studentDetail)
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Student Admission Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Full Name</div>
                        <div class="col-sm-8 fw-bold">{{ $user->studentDetail->full_name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">DOB / ID</div>
                        <div class="col-sm-8 fw-bold">{{ $user->studentDetail->dob }} ({{ $user->studentDetail->id_type }}: {{ $user->studentDetail->id_number }})</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Contact / WhatsApp</div>
                        <div class="col-sm-8 fw-bold">{{ $user->studentDetail->primary_contact }} / {{ $user->studentDetail->whatsapp_number }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Education</div>
                        <div class="col-sm-8 fw-bold">{{ $user->studentDetail->highest_education }} ({{ $user->studentDetail->passing_year }})</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Preferred Countries</div>
                        <div class="col-sm-8 fw-bold">
                            @php
                                $countries = json_decode($user->studentDetail->preferred_countries, true);
                            @endphp
                            {{ is_array($countries) ? implode(', ', $countries) : $user->studentDetail->preferred_countries }}
                        </div>
                    </div>
                    <hr>
                    <h6 class="fw-bold mb-3">Documents</h6>
                    @if($user->studentDetail->id_doc)
                        <a href="{{ asset('storage/' . $user->studentDetail->id_doc) }}" target="_blank" class="btn btn-sm btn-outline-info">ID Document</a>
                    @endif
                </div>
            </div>
            @endif

            @if($user->account_type !== 'student')
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Change Password</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.password.update', $user->id) }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label small text-muted">New Password</label>
                                <input type="password" name="password" class="form-control" required minlength="8">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label small text-muted">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control" required minlength="8">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @else
            <div class="alert alert-info border-0 shadow-sm">
                <i class="fas fa-info-circle me-2"></i> Password change is disabled for student accounts.
            </div>
            @endif

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
