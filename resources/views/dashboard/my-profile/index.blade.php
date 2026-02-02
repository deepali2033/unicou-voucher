@extends('layouts.master')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">My Profile</h4>
            <p class="text-muted small mb-0">Manage your personal information and documents.</p>
        </div>
        <div>
            <span class="badge {{ $user->profile_verification_status == 'verified' ? 'bg-success' : 'bg-warning' }} px-3 py-2 rounded-pill">
                <i class="fas {{ $user->profile_verification_status == 'verified' ? 'fa-check-circle' : 'fa-clock' }} me-1"></i>
                {{ ucfirst($user->profile_verification_status) }}
            </span>
        </div>
    </div>

    <div class="row g-4">
        <!-- Personal Info -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                <div class="card-body text-center py-4">
                    <div class="mb-3">
                        <img src="{{ $user->profile_photo ? asset('storage/' . $user->profile_photo) : '/images/default-avatar.png' }}" 
                             class="rounded-circle shadow-sm" style="width: 120px; height: 120px; object-fit: cover;">
                    </div>
                    <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                    <p class="text-muted small mb-3">{{ $user->email }}</p>
                    <div class="badge bg-light text-primary px-3 py-2 rounded-pill border border-primary">
                        {{ ucfirst(str_replace('_', ' ', $user->account_type)) }}
                    </div>
                </div>
                <div class="card-footer bg-white border-0 pb-4 px-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">User ID:</span>
                        <span class="fw-bold small">{{ $user->user_id }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Phone:</span>
                        <span class="fw-bold small">{{ $user->phone ?? 'N/A' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Joined:</span>
                        <span class="fw-bold small">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Wallet/Points Stats -->
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-bold mb-0">Balance & Points</h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="p-3 bg-light rounded-3 mb-3">
                        <div class="text-muted small mb-1">Wallet Balance</div>
                        <div class="h5 fw-bold mb-0 text-primary">RS {{ number_format($user->wallet_balance, 2) }}</div>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <div class="text-muted small mb-1">Referral Points</div>
                                <div class="h5 fw-bold mb-0 text-success">{{ number_format(\App\Models\Order::where('user_id', $user->id)->sum('referral_points')) }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <div class="text-muted small mb-1">Bonus Points</div>
                                <div class="h5 fw-bold mb-0 text-info">{{ number_format(\App\Models\Order::where('user_id', $user->id)->sum('bonus_amount')) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Tabs -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <ul class="nav nav-pills" id="profileTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill px-4 me-2" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab">Details</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill px-4 me-2" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab">Documents</button>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-4">
                    <div class="tab-content" id="profileTabContent">
                        <!-- Details Tab -->
                        <div class="tab-pane fade show active" id="details" role="tabpanel">
                            @if($user->isStudent())
                                @php $details = $user->studentDetail; @endphp
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="text-muted small mb-1 d-block text-uppercase fw-bold">Full Name</label>
                                        <div class="p-3 bg-light rounded-3">{{ $details->full_name ?? $user->name }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small mb-1 d-block text-uppercase fw-bold">Date of Birth</label>
                                        <div class="p-3 bg-light rounded-3">{{ $details->dob ?? 'N/A' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small mb-1 d-block text-uppercase fw-bold">ID Number ({{ $details->id_type ?? 'ID' }})</label>
                                        <div class="p-3 bg-light rounded-3">{{ $details->id_number ?? 'N/A' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small mb-1 d-block text-uppercase fw-bold">Highest Education</label>
                                        <div class="p-3 bg-light rounded-3">{{ $details->highest_education ?? 'N/A' }} ({{ $details->passing_year ?? '' }})</div>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="text-muted small mb-1 d-block text-uppercase fw-bold">Address</label>
                                        <div class="p-3 bg-light rounded-3">
                                            {{ $details->address ?? 'N/A' }}, {{ $details->city ?? '' }}, {{ $details->state ?? '' }}, {{ $details->country ?? '' }}
                                        </div>
                                    </div>
                                </div>
                            @elseif($user->isAgent())
                                @php $details = $user->agentDetail; @endphp
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="text-muted small mb-1 d-block text-uppercase fw-bold">Business Name</label>
                                        <div class="p-3 bg-light rounded-3">{{ $details->business_name ?? 'N/A' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small mb-1 d-block text-uppercase fw-bold">Registration Number</label>
                                        <div class="p-3 bg-light rounded-3">{{ $details->registration_number ?? 'N/A' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small mb-1 d-block text-uppercase fw-bold">Representative</label>
                                        <div class="p-3 bg-light rounded-3">{{ $details->representative_name ?? 'N/A' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small mb-1 d-block text-uppercase fw-bold">Website</label>
                                        <div class="p-3 bg-light rounded-3">{{ $details->website ?? 'N/A' }}</div>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="text-muted small mb-1 d-block text-uppercase fw-bold">Address</label>
                                        <div class="p-3 bg-light rounded-3">
                                            {{ $details->address ?? 'N/A' }}, {{ $details->city ?? '' }}, {{ $details->state ?? '' }}, {{ $details->country ?? '' }}
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-info-circle text-muted fa-3x mb-3"></i>
                                    <p class="text-muted">Standard profile details available in Account Settings.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Documents Tab -->
                        <div class="tab-pane fade" id="documents" role="tabpanel">
                            <div class="row g-3">
                                @php
                                    $docs = [];
                                    if($user->isStudent() && $user->studentDetail) {
                                        $docs['Identity Document'] = $user->studentDetail->id_doc;
                                    } elseif($user->isAgent() && $user->agentDetail) {
                                        $docs['Business Registration'] = $user->agentDetail->registration_doc;
                                        $docs['Identity Document'] = $user->agentDetail->id_doc;
                                    }
                                    
                                    // Additional User Documents
                                    if($user->aadhar_card) $docs['Aadhar Card'] = $user->aadhar_card;
                                    if($user->pan_card) $docs['PAN Card'] = $user->pan_card;
                                    if($user->aadhaar_document_path) $docs['Aadhaar Doc'] = $user->aadhaar_document_path;
                                    
                                    if($user->verification_documents && is_array($user->verification_documents)) {
                                        foreach($user->verification_documents as $index => $vDoc) {
                                            $docs['Verification Doc ' . ($index + 1)] = $vDoc;
                                        }
                                    }
                                @endphp

                                @forelse($docs as $name => $path)
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded-3 d-flex align-items-center justify-content-between bg-light">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-file-pdf fa-2x text-danger me-3"></i>
                                                <div>
                                                    <div class="fw-bold small">{{ $name }}</div>
                                                    <div class="text-muted extra-small">Uploaded Document</div>
                                                </div>
                                            </div>
                                            @if($path)
                                            <a href="{{ asset('storage/' . $path) }}" target="_blank" class="btn btn-sm btn-primary rounded-pill px-3">
                                                <i class="fas fa-eye me-1"></i> View
                                            </a>
                                            @else
                                            <span class="badge bg-secondary">Not Found</span>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-center py-5">
                                        <i class="fas fa-folder-open text-muted fa-3x mb-3"></i>
                                        <p class="text-muted">No uploaded documents found.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .nav-pills .nav-link.active {
        background-color: #5e5ce6;
    }
    .nav-pills .nav-link {
        color: #666;
        font-weight: 500;
        background-color: #f8f9fa;
    }
    .extra-small {
        font-size: 0.75rem;
    }
</style>
@endsection
