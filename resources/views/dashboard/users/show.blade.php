@extends('layouts.master')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold text-dark">User Command Center</h4>
        <div class="d-flex gap-2">
            @if((auth()->user()->account_type !== 'manager' || auth()->user()->can_approve_user) && $user->profile_verification_status === 'pending')
            <form action="{{ route('approvals.approve', $user->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success shadow-sm" onclick="return confirm('Approve this user?')">
                    <i class="fas fa-check me-2"></i> Approve User
                </button>
            </form>
            @endif

            @if(auth()->user()->account_type !== 'manager' || auth()->user()->can_freeze_user)
            <form action="{{ route('users.toggle', $user->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn {{ $user->is_active ? 'btn-danger' : 'btn-success' }} shadow-sm">
                    <i class="fas {{ $user->is_active ? 'fa-user-slash' : 'fa-user-check' }} me-2"></i>
                    {{ $user->is_active ? 'Freeze Login' : 'Unfreeze Login' }}
                </button>
            </form>

            <form action="{{ route('users.suspend', $user->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn {{ $user->profile_verification_status === 'suspended' ? 'btn-success' : 'btn-warning' }} shadow-sm">
                    <i class="fas {{ $user->profile_verification_status === 'suspended' ? 'fa-id-badge' : 'fa-id-card-clip' }} me-2"></i>
                    {{ $user->profile_verification_status === 'suspended' ? 'Verify KYC' : 'Suspend KYC' }}
                </button>
            </form>
            @endif

            <a href="{{ route('users.management') }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-left me-2"></i> Back to List
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i> <strong>Validation Errors:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <!-- Sidebar Summary -->
        <div class="col-xl-3 col-lg-4">
            <div class="card shadow-sm border-0 mb-4 overflow-hidden">
                <div class="card-body text-center pt-5 pb-4">
                    <div class="position-relative d-inline-block mb-3">
                        <img src="{{ $user->profile_photo ? asset('storage/' . $user->profile_photo) : asset('images/user.png') }}"
                            class="rounded-circle border border-4 border-light shadow-sm" width="120" height="120" alt="Avatar" style="object-fit: cover;">
                        <span class="position-absolute bottom-0 end-0 p-2 {{ $user->profile_verification_status === 'verified' ? 'bg-success' : ($user->profile_verification_status === 'suspended' ? 'bg-danger' : 'bg-warning') }} border border-2 border-white rounded-circle"></span>
                    </div>
                    <h5 class="fw-bold mb-1">{{ $user->first_name }} {{ $user->last_name }}</h5>
                    <p class="text-muted small mb-3">{{ $user->email }}</p>
                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <span class="badge bg-primary-soft text-primary px-3 py-2">
                            {{ strtoupper(str_replace('_', ' ', $user->account_type)) }}
                        </span>
                    </div>
                </div>
                <div class="bg-light p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted">User ID</small>
                        <small class="fw-bold">{{ $user->user_id }}</small>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted">Status</small>
                        @if($user->profile_verification_status === 'verified')
                        <span class="badge bg-success-soft text-success">Verified</span>
                        @elseif($user->profile_verification_status === 'pending')
                        <span class="badge bg-warning-soft text-warning">Pending</span>
                        @elseif($user->profile_verification_status === 'suspended')
                        <span class="badge bg-danger-soft text-danger">Suspended</span>
                        @else
                        <span class="badge bg-secondary-soft text-secondary">{{ ucfirst($user->profile_verification_status) }}</span>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted">Last Seen</small>
                        <small class="fw-bold">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</small>
                    </div>
                </div>
                <ul class="list-group list-group-flush border-top">
                    <li class="list-group-item p-3">
                        <h6 class="fw-bold mb-3 small text-uppercase text-muted">Quick Metrics</h6>
                        <div class="row g-2 text-center">
                            <div class="col-6">
                                <div class="p-2 border rounded bg-white shadow-none">
                                    <div class="small text-muted">Wallet</div>
                                    <div class="fw-bold text-primary">₹{{ number_format($user->wallet_balance, 2) }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 border rounded bg-white shadow-none">
                                    <div class="small text-muted">Points</div>
                                    <div class="fw-bold text-success">{{ number_format(\App\Models\Order::where('user_id', $user->id)->sum('points_earned')) }}</div>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Risk & Compliance Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-shield-alt me-2 text-danger"></i>Risk & Compliance</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="small text-muted d-block mb-1">Risk Level</label>
                        @php
                        $risk = \App\Models\CountryRiskLevel::where('country_name', $user->country_iso)->first();
                        @endphp
                        <span class="badge {{ ($risk->risk_level ?? 'Low') === 'High' ? 'bg-danger' : (($risk->risk_level ?? 'Low') === 'Medium' ? 'bg-warning' : 'bg-success') }} w-100 py-2">
                            {{ strtoupper($risk->risk_level ?? 'Low') }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted">Complaints</small>
                        <small class="badge bg-light text-dark">0</small>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">Kyc Status</small>
                        <small class="text-{{ $user->profile_verification_status === 'verified' ? 'success' : 'warning' }} fw-bold">
                            {{ ucfirst($user->profile_verification_status) }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-xl-9 col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white p-0 border-bottom">
                    <ul class="nav nav-tabs border-0 px-3" id="userTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active py-3 px-4 fw-bold" id="overview-tab" data-bs-toggle="tab" href="#overview" role="tab">Overview</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link py-3 px-4 fw-bold" id="profile-tab" data-bs-toggle="tab" href="#profile" role="tab">Edit Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link py-3 px-4 fw-bold" id="kyc-tab" data-bs-toggle="tab" href="#kyc" role="tab">KYC & Documents</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link py-3 px-4 fw-bold" id="security-tab" data-bs-toggle="tab" href="#security" role="tab">Security</a>
                        </li>
                        @if($user->account_type === 'manager')
                        <li class="nav-item">
                            <a class="nav-link py-3 px-4 fw-bold" id="permissions-tab" data-bs-toggle="tab" href="#permissions" role="tab">Permissions</a>
                        </li>
                        @endif
                    </ul>
                </div>
                <div class="card-body p-4">
                    <div class="tab-content" id="userTabsContent">
                        <!-- Overview Tab -->
                        <div class="tab-pane fade show active" id="overview" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-3 border-bottom pb-2">Personal Information</h6>
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <td class="text-muted" width="40%">Full Name</td>
                                            <td class="fw-bold">{{ $user->first_name }} {{ $user->last_name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Email</td>
                                            <td class="fw-bold">{{ $user->email }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Primary Phone</td>
                                            <td class="fw-bold">{{ $user->phone ?? $user->primary_contact ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">WhatsApp</td>
                                            <td class="fw-bold">{{ $user->whatsapp_number ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Date of Birth</td>
                                            <td class="fw-bold">{{ $user->dob ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">ID Type / No.</td>
                                            <td class="fw-bold">{{ $user->id_type ?? 'N/A' }} ({{ $user->id_number ?? 'N/A' }})</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Address</td>
                                            <td class="fw-bold">
                                                {{ $user->address ?? 'N/A' }}<br>
                                                {{ $user->city ?? '' }}, {{ $user->state ?? '' }} {{ $user->post_code ?? $user->zip_code ?? '' }}<br>
                                                {{ $user->country ?? '' }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-3 border-bottom pb-2">Account Activity</h6>
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <td class="text-muted" width="40%">Joined Date</td>
                                            <td class="fw-bold">{{ $user->created_at->format('d M Y, h:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Last Login</td>
                                            <td class="fw-bold">{{ $user->last_login_at ? $user->last_login_at->format('d M Y, h:i A') : 'Never' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Email Verified</td>
                                            <td class="fw-bold">
                                                @if($user->email_verified_at)
                                                <span class="text-success"><i class="fas fa-check-circle me-1"></i> Verified</span>
                                                @else
                                                <span class="text-danger"><i class="fas fa-times-circle me-1"></i> Pending</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Total Orders</td>
                                            <td class="fw-bold">{{ \App\Models\Order::where('user_id', $user->id)->count() }}</td>
                                        </tr>
                                    </table>
                                </div>

                                @if($user->account_type === 'reseller_agent' || $user->account_type === 'agent')
                                <div class="col-12 mt-4">
                                    <h6 class="fw-bold mb-3 border-bottom pb-2">Business Information</h6>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="p-3 bg-light rounded h-100 shadow-none border">
                                                <small class="text-muted d-block">Business Name</small>
                                                <span class="fw-bold">{{ $user->business_name ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="p-3 bg-light rounded h-100 shadow-none border">
                                                <small class="text-muted d-block">Registration No.</small>
                                                <span class="fw-bold">{{ $user->registration_number ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="p-3 bg-light rounded h-100 shadow-none border">
                                                <small class="text-muted d-block">Website</small>
                                                <span class="fw-bold">{{ $user->website ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($user->account_type === 'student')
                                <div class="col-12 mt-4">
                                    <h6 class="fw-bold mb-3 border-bottom pb-2">Education & Admission Details</h6>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="p-3 bg-light rounded h-100 shadow-none border">
                                                <small class="text-muted d-block">Exam Purpose</small>
                                                <span class="fw-bold">{{ $user->exam_purpose ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="p-3 bg-light rounded h-100 shadow-none border">
                                                <small class="text-muted d-block">Highest Education</small>
                                                <span class="fw-bold">{{ $user->highest_education ?? 'N/A' }} ({{ $user->passing_year ?? 'N/A' }})</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="p-3 bg-light rounded h-100 shadow-none border">
                                                <small class="text-muted d-block">Preferred Countries</small>
                                                <span class="fw-bold">
                                                    @if(is_array($user->preferred_countries))
                                                    {{ implode(', ', $user->preferred_countries) }}
                                                    @else
                                                    {{ $user->preferred_countries ?? 'N/A' }}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="p-3 bg-light rounded h-100 shadow-none border">
                                                <small class="text-muted d-block">Bank Account</small>
                                                <span class="fw-bold">{{ $user->bank_name ?? 'N/A' }}</span><br>
                                                <small class="text-muted">{{ $user->account_number ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="p-3 bg-light rounded h-100 shadow-none border">
                                                <small class="text-muted d-block">Bank Country</small>
                                                <span class="fw-bold">{{ $user->bank_country ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Edit Profile Tab -->
                        <div class="tab-pane fade" id="profile" role="tabpanel">
                            @if(auth()->user()->account_type !== 'manager' || auth()->user()->can_edit_user)
                            <form action="{{ route('users.update', $user->id) }}#profile" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">First Name</label>
                                        <input type="text" name="first_name" class="form-control" value="{{ $user->first_name }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Last Name</label>
                                        <input type="text" name="last_name" class="form-control" value="{{ $user->last_name }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Email Address</label>
                                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Phone Number</label>
                                        <input type="text" name="phone" class="form-control" value="{{ $user->phone ?? $user->primary_contact }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">WhatsApp Number</label>
                                        <input type="text" name="whatsapp_number" class="form-control" value="{{ $user->whatsapp_number }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Date of Birth</label>
                                        <input type="date" name="dob" class="form-control" value="{{ $user->dob }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">ID Type</label>
                                        <select name="id_type" class="form-select">
                                            <option value="">Select document type</option>
                                            <option {{ $user->id_type == 'National ID Card' ? 'selected' : '' }}>National ID Card</option>
                                            <option {{ $user->id_type == 'Passport' ? 'selected' : '' }}>Passport</option>
                                            <option {{ $user->id_type == 'Driving License' ? 'selected' : '' }}>Driving License</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">ID Number</label>
                                        <input type="text" name="id_number" class="form-control" value="{{ $user->id_number }}">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label fw-bold small">Full Address</label>
                                        <textarea name="address" class="form-control" rows="2">{{ $user->address }}</textarea>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small">City</label>
                                        <input type="text" name="city" class="form-control" value="{{ $user->city }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small">State</label>
                                        <input type="text" name="state" class="form-control" value="{{ $user->state }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fw-bold small">Zip/Post Code</label>
                                        <input type="text" name="post_code" class="form-control" value="{{ $user->post_code ?? $user->zip_code }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fw-bold small">Country</label>
                                        <input type="text" name="country" class="form-control" value="{{ $user->country }}">
                                    </div>

                                    @if($user->account_type === 'reseller_agent' || $user->account_type === 'agent')
                                    <div class="col-12 mt-4">
                                        <h6 class="fw-bold border-bottom pb-2">Agent Business Data</h6>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Business Name</label>
                                        <input type="text" name="business_name" class="form-control" value="{{ $user->business_name }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Registration Number</label>
                                        <input type="text" name="registration_number" class="form-control" value="{{ $user->registration_number }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Website</label>
                                        <input type="url" name="website" class="form-control" value="{{ $user->website }}">
                                    </div>
                                    @endif

                                    @if($user->account_type === 'student')
                                    <div class="col-12 mt-4">
                                        <h6 class="fw-bold border-bottom pb-2">Student Education & Bank Data</h6>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Exam Purpose</label>
                                        <select name="exam_purpose" class="form-select">
                                            <option value="">Select purpose</option>
                                            <option {{ $user->exam_purpose == 'Education' ? 'selected' : '' }}>Education</option>
                                            <option {{ $user->exam_purpose == 'Migration' ? 'selected' : '' }}>Migration</option>
                                            <option {{ $user->exam_purpose == 'Other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold small">Highest Edu.</label>
                                        <input type="text" name="highest_education" class="form-control" value="{{ $user->highest_education }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold small">Passing Year</label>
                                        <input type="text" name="passing_year" class="form-control" value="{{ $user->passing_year }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small">Bank Name</label>
                                        <input type="text" name="bank_name" class="form-control" value="{{ $user->bank_name }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small">Bank Country</label>
                                        <input type="text" name="bank_country" class="form-control" value="{{ $user->bank_country }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small">IBAN/Account No</label>
                                        <input type="text" name="account_number" class="form-control" value="{{ $user->account_number }}">
                                    </div>
                                    <div class="col-12 mt-3">
                                        <label class="form-label fw-bold small d-block">Preferred Countries</label>
                                        <div class="d-flex flex-wrap gap-3 p-3 bg-light rounded border">
                                            @php
                                            $selectedCountries = is_array($user->preferred_countries) ? $user->preferred_countries : explode(', ', $user->preferred_countries ?? '');
                                            $options = ['United Kingdom', 'United States', 'Canada', 'Australia'];
                                            @endphp
                                            @foreach($options as $option)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="preferred_countries[]" value="{{ $option }}" id="pc_{{ $loop->index }}" {{ in_array($option, $selectedCountries) ? 'checked' : '' }}>
                                                <label class="form-check-label small" for="pc_{{ $loop->index }}">{{ $option }}</label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif

                                    <div class="col-12 mt-4 text-end">
                                        <button type="submit" class="btn btn-primary px-5 btn-lg shadow-sm">
                                            <i class="fas fa-save me-2"></i> Update User Profile
                                        </button>
                                    </div>
                                </div>
                            </form>
                            @else
                            <div class="alert alert-info border-0 shadow-sm">
                                <i class="fas fa-info-circle me-2 text-primary"></i> You do not have permission to edit user profiles.
                            </div>
                            @endif
                        </div>

                        <!-- KYC Tab -->
                        <div class="tab-pane fade" id="kyc" role="tabpanel">
                            <div class="row g-4">
                                @php
                                $docs = [
                                'Profile Photo' => ['path' => $user->profile_photo, 'field' => 'profile_photo'],
                                'Aadhar Card' => ['path' => $user->aadhar_card, 'field' => 'aadhar_card'],
                                'PAN Card' => ['path' => $user->pan_card, 'field' => 'pan_card'],
                                'Business Reg' => ['path' => $user->registration_doc, 'field' => 'registration_doc'],
                                'ID Document' => ['path' => $user->id_doc, 'field' => 'id_doc'],
                                'Final ID Doc' => ['path' => $user->id_doc_final, 'field' => 'id_doc_final'],
                                ];
                                @endphp

                                @foreach($docs as $label => $data)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card border border-dashed p-3 h-100 shadow-sm hover-shadow transition">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="fw-bold mb-0 text-dark small text-uppercase">{{ $label }}</h6>
                                            @if($data['path'])
                                            <span class="badge bg-success-soft text-success shadow-none border">Uploaded</span>
                                            @else
                                            <span class="badge bg-danger-soft text-danger shadow-none border">Missing</span>
                                            @endif
                                        </div>

                                        <div class="mb-3 text-center bg-light rounded p-2 d-flex align-items-center justify-content-center border" style="height: 180px;">
                                            @if($data['path'])
                                            @php
                                            $extension = strtolower(pathinfo($data['path'], PATHINFO_EXTENSION));
                                            $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                            @endphp

                                            @if($isImage)
                                            <a href="{{ asset('storage/' . $data['path']) }}" target="_blank" class="d-block w-100 h-100 d-flex align-items-center justify-content-center">
                                                <img src="{{ asset('storage/' . $data['path']) }}" class="img-fluid rounded shadow-sm" style="max-height: 160px; cursor: pointer;" alt="{{ $label }}">
                                            </a>
                                            @elseif($extension === 'pdf')
                                            <div class="h-100 d-flex flex-column align-items-center justify-content-center">
                                                <i class="fas fa-file-pdf fa-4x text-danger mb-2"></i>
                                                <span class="small text-muted text-truncate w-100 px-2">{{ basename($data['path']) }}</span>
                                                <a href="{{ asset('storage/' . $data['path']) }}" target="_blank" class="btn btn-xs btn-outline-danger mt-2">
                                                    Open PDF
                                                </a>
                                            </div>
                                            @else
                                            <div class="h-100 d-flex flex-column align-items-center justify-content-center">
                                                <i class="fas fa-file-alt fa-4x text-primary mb-2"></i>
                                                <span class="small text-muted text-truncate w-100 px-2">{{ basename($data['path']) }}</span>
                                            </div>
                                            @endif
                                            @else
                                            <div class="text-muted small">No file uploaded</div>
                                            @endif
                                        </div>

                                        @if($data['path'])
                                        <div class="d-flex gap-2 mb-3">
                                            <a href="{{ asset('storage/' . $data['path']) }}" target="_blank" class="btn btn-sm btn-primary flex-grow-1 shadow-sm">
                                                <i class="fas fa-eye me-1"></i> View Full
                                            </a>
                                            <a href="{{ asset('storage/' . $data['path']) }}" download class="btn btn-sm btn-outline-secondary shadow-sm">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                        @endif

                                        @if(auth()->user()->account_type !== 'manager' || auth()->user()->can_edit_user)
                                        <form action="{{ route('users.update', $user->id) }}#kyc" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="input-group input-group-sm shadow-none">
                                                <input type="file" name="{{ $data['field'] }}" class="form-control" required>
                                                <button class="btn btn-dark" type="submit">Upload</button>
                                            </div>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Security Tab -->
                        <div class="tab-pane fade" id="security" role="tabpanel">
                            @if(auth()->user()->account_type !== 'manager' || auth()->user()->can_reset_password)
                            <div class="card border-0 bg-light mb-4 shadow-sm border">
                                <div class="card-body p-4">
                                    <h6 class="fw-bold mb-3 text-dark">Reset User Password</h6>
                                    <form action="{{ route('users.password.update', $user->id) }}#security" method="POST">
                                        @csrf
                                        <div class="row g-3">
                                            <div class="col-md-5">
                                                <label class="form-label small text-muted fw-bold">New Password</label>
                                                <input type="password" name="password" class="form-control shadow-sm" placeholder="Min 8 characters" required>
                                            </div>
                                            <div class="col-md-5">
                                                <label class="form-label small text-muted fw-bold">Confirm Password</label>
                                                <input type="password" name="password_confirmation" class="form-control shadow-sm" placeholder="Repeat password" required>
                                            </div>
                                            <div class="col-md-2 d-flex align-items-end">
                                                <button type="submit" class="btn btn-primary w-100 shadow-sm">Update</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @endif

                            @if(auth()->user()->account_type === 'admin')
                            <div class="card border-0 bg-danger-soft shadow-sm border border-danger border-opacity-10">
                                <div class="card-body p-4 text-center">
                                    <h6 class="fw-bold text-danger mb-2">Danger Zone</h6>
                                    <p class="small text-muted mb-3">Deleting this account will permanently remove all associated data, including orders and transaction history. This action cannot be undone.</p>
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('ARE YOU ABSOLUTELY SURE? This cannot be undone!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger px-4 shadow-sm">
                                            <i class="fas fa-trash-alt me-2"></i> Delete Permanently
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Permissions Tab -->
                        @if($user->account_type === 'manager')
                        <div class="tab-pane fade" id="permissions" role="tabpanel">
                            <h6 class="fw-bold mb-3 text-dark">Managerial Capabilities</h6>
                            <p class="text-muted small mb-4">Define what administrative actions this manager is authorized to perform across the platform.</p>

                            <form action="{{ route('users.permissions.update', $user->id) }}#permissions" method="POST">
                                @csrf
                                <div class="row g-4">
                                    @php
                                    $perms = [
                                    ['name' => 'can_view_users', 'label' => 'View User Management', 'desc' => 'Access the user lists and view user details'],
                                    ['name' => 'can_create_user', 'label' => 'Create New Users', 'desc' => 'Register new agents, resellers, or students'],
                                    ['name' => 'can_edit_user', 'label' => 'Edit User Profiles', 'desc' => 'Modify user information and documents'],
                                    ['name' => 'can_approve_user', 'label' => 'Approve Verifications', 'desc' => 'Approve pending KYC and profile requests'],
                                    ['name' => 'can_freeze_user', 'label' => 'Freeze Accounts', 'desc' => 'Suspend or reactivate user access'],
                                    ['name' => 'can_reset_password', 'label' => 'Reset Passwords', 'desc' => 'Change passwords for managed users'],
                                    ['name' => 'can_impersonate_user', 'label' => 'Access User Dashboards', 'desc' => 'Login as any student or agent to assist them'],
                                    ];
                                    @endphp

                                    @foreach($perms as $p)
                                    <div class="col-md-6">
                                        <div class="form-check form-switch p-3 border rounded h-100 bg-white shadow-sm transition hover-shadow">
                                            <input class="form-check-input ms-0 me-3 mt-1" type="checkbox" name="{{ $p['name'] }}" id="{{ $p['name'] }}" {{ $user->{$p['name']} ? 'checked' : '' }}>
                                            <label class="form-check-label w-100" for="{{ $p['name'] }}">
                                                <div class="fw-bold text-dark">{{ $p['label'] }}</div>
                                                <div class="small text-muted">{{ $p['desc'] }}</div>
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="mt-4 text-end">
                                    <button type="submit" class="btn btn-primary px-5 btn-lg shadow-sm">Apply Permissions</button>
                                </div>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-primary-soft {
        background-color: rgba(13, 110, 253, 0.1);
    }

    .bg-success-soft {
        background-color: rgba(25, 135, 84, 0.1);
    }

    .bg-warning-soft {
        background-color: rgba(255, 193, 7, 0.1);
    }

    .bg-danger-soft {
        background-color: rgba(220, 53, 69, 0.1);
    }

    .bg-secondary-soft {
        background-color: rgba(108, 117, 125, 0.1);
    }

    .nav-tabs .nav-link {
        color: #6c757d;
        border-radius: 0;
        border: none;
        font-size: 0.9rem;
        letter-spacing: 0.5px;
    }

    .nav-tabs .nav-link.active {
        color: #0d6efd;
        border-bottom: 3px solid #0d6efd !important;
        background: none;
    }

    .border-dashed {
        border-style: dashed !important;
        border-width: 2px !important;
    }

    .hover-shadow:hover {
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .08) !important;
    }

    .transition {
        transition: all 0.3s ease;
    }

    .btn-xs {
        padding: .125rem .25rem;
        font-size: .75rem;
        border-radius: .2rem;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab activation based on hash
        var hash = window.location.hash;
        if (hash) {
            var triggerEl = document.querySelector('ul.nav-tabs a[href="' + hash + '"]');
            if (triggerEl) {
                var tab = new bootstrap.Tab(triggerEl);
                tab.show();
            }
        }

        // Update hash on tab change
        var tabLinks = document.querySelectorAll('ul.nav-tabs a');
        tabLinks.forEach(function(link) {
            link.addEventListener('shown.bs.tab', function(e) {
                // Remove the hash if it exists to avoid scrolling
                history.replaceState(null, null, ' ' + e.target.hash);
            });
        });
    });
</script>
@endsection