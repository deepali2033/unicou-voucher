@extends('admin.layout.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="fas fa-shield-alt me-2 text-primary"></i> KYC & Compliance Management</h4>
        <div class="d-flex gap-2">
            <span class="badge bg-warning text-dark px-3 py-2">Pending: {{ $pendingUsers->count() }}</span>
            <span class="badge bg-success px-3 py-2">Verified: {{ \App\Models\User::where('profile_verification_status', 'verified')->count() }}</span>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form action="{{ route('admin.approvals.index') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Risk Level</label>
                    <select name="risk_level" class="form-select form-select-sm">
                        <option value="">All Risk Levels</option>
                        <option value="low">Low Risk</option>
                        <option value="medium">Medium Risk</option>
                        <option value="high">High Risk</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Verification Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="pending">Pending Approval</option>
                        <option value="verified">Verified</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Search User</label>
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="Search by name or ID...">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="fas fa-filter me-1"></i> More Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">User Details</th>
                            <th>Role</th>
                            <th>KYC Documents</th>
                            <th>Compliance Flags</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingUsers as $user)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-3 bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                        {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $user->first_name }} {{ $user->last_name }}</div>
                                        <div class="text-muted small">{{ $user->user_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2">{{ ucfirst(str_replace('_', ' ', $user->account_type)) }}</span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    @if($user->account_type === 'reseller_agent' && $user->agentDetail)
                                    @if($user->agentDetail->registration_doc)
                                    <a href="{{ asset('storage/' . $user->agentDetail->registration_doc) }}" target="_blank" class="btn btn-xs btn-outline-info" title="Business Registration">
                                        <i class="fas fa-file-invoice"></i> Reg
                                    </a>
                                    @endif
                                    @if($user->agentDetail->id_doc)
                                    <a href="{{ asset('storage/' . $user->agentDetail->id_doc) }}" target="_blank" class="btn btn-xs btn-outline-info" title="Identity Document">
                                        <i class="fas fa-id-card"></i> ID
                                    </a>
                                    @endif
                                    @elseif($user->account_type === 'student' && $user->studentDetail)
                                    @if($user->studentDetail->id_doc)
                                    <a href="{{ asset('storage/' . $user->studentDetail->id_doc) }}" target="_blank" class="btn btn-xs btn-outline-info" title="Student ID">
                                        <i class="fas fa-id-card"></i> ID
                                    </a>
                                    @endif
                                    @endif

                                    @if($user->aadhar_card)
                                    <a href="{{ asset('storage/' . $user->aadhar_card) }}" target="_blank" class="btn btn-xs btn-outline-primary" title="Aadhar Card">
                                        <i class="fas fa-address-card"></i> Aadhar
                                    </a>
                                    @endif
                                    @if($user->pan_card)
                                    <a href="{{ asset('storage/' . $user->pan_card) }}" target="_blank" class="btn btn-xs btn-outline-primary" title="PAN Card">
                                        <i class="fas fa-credit-card"></i> PAN
                                    </a>
                                    @endif

                                    @if(!$user->agentDetail && !$user->studentDetail && !$user->aadhar_card && !$user->pan_card)
                                    <span class="text-muted small">No Docs</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.7rem;">Identity Verified</span>
                                    <span class="badge bg-info-subtle text-info border border-info-subtle" style="font-size: 0.7rem;">Email Verified</span>
                                    @if($user->account_type === 'reseller_agent')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle" style="font-size: 0.7rem;">Business Review</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-warning-subtle text-warning px-3 py-2">
                                    <i class="fas fa-clock me-1"></i> Pending
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="dropdown">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                        <li>
                                            <form action="{{ route('admin.approvals.approve', $user->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-success"><i class="fas fa-check-circle me-2"></i> Approve KYC</button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.approvals.reject', $user->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Reject this KYC application?')">
                                                    <i class="fas fa-times-circle me-2"></i> Reject KYC
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="{{ route('admin.users.show', $user->id) }}"><i class="fas fa-eye me-2 text-info"></i> View Full Profile</a></li>
                                        <li><a class="dropdown-item text-warning" href="#"><i class="fas fa-flag me-2"></i> Flag Compliance</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-clipboard-check fa-3x mb-3"></i>
                                    <p>No KYC applications waiting for review.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-xs {
        padding: 0.2rem 0.4rem;
        font-size: 0.75rem;
    }

    .bg-primary-subtle {
        background-color: #e7f1ff;
    }

    .bg-success-subtle {
        background-color: #d1e7dd;
    }

    .bg-warning-subtle {
        background-color: #fff3cd;
    }

    .bg-info-subtle {
        background-color: #cff4fc;
    }
</style>
@endsection