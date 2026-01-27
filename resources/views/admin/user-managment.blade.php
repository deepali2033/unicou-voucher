@extends('admin.layout.app')

@section('content')
<div class="container-fluid">

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form action="{{ route('admin.users.management') }}" method="GET" class="row g-3 align-items-center">
                        <div class="col-md-7">
                            <div class="btn-group flex-wrap" role="group">
                                <a href="{{ route('admin.users.management', ['role' => 'all', 'search' => request('search')]) }}" 
                                   class="btn btn-outline-primary {{ request('role', 'all') == 'all' ? 'active' : '' }}">All</a>
                                <a href="{{ route('admin.users.management', ['role' => 'manager', 'search' => request('search')]) }}" 
                                   class="btn btn-outline-primary {{ request('role') == 'manager' ? 'active' : '' }}">Manager</a>
                                <a href="{{ route('admin.users.management', ['role' => 'reseller_agent', 'search' => request('search')]) }}" 
                                   class="btn btn-outline-primary {{ request('role') == 'reseller_agent' ? 'active' : '' }}">Reseller Agent</a>
                                <a href="{{ route('admin.users.management', ['role' => 'support_team', 'search' => request('search')]) }}" 
                                   class="btn btn-outline-primary {{ request('role') == 'support_team' ? 'active' : '' }}">Support Team</a>
                                <a href="{{ route('admin.users.management', ['role' => 'student', 'search' => request('search')]) }}" 
                                   class="btn btn-outline-primary {{ request('role') == 'student' ? 'active' : '' }}">Student</a>
                                <a href="{{ route('admin.users.management', ['role' => 'agent', 'search' => request('search')]) }}" 
                                   class="btn btn-outline-primary {{ request('role') == 'agent' ? 'active' : '' }}">Agent</a>
                            </div>
                        </div>
                        <input type="hidden" name="role" value="{{ request('role', 'all') }}">
                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Search by name..." value="{{ request('search') }}">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2 text-end d-flex gap-2">
                            <a href="{{ route('admin.users.pdf', ['role' => request('role'), 'search' => request('search')]) }}" class="btn btn-danger flex-fill">
                                <i class="fas fa-file-pdf"></i> PDF
                            </a>
                            <a href="{{ route('admin.users.create') }}" class="btn btn-success flex-fill">
                                <i class="fas fa-plus"></i> Add
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">User Management</h5>
            <span class="badge bg-primary">{{ $users->total() }} Total Users</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>User Info</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $user->profile_photo ? asset('storage/' . $user->profile_photo) : asset('images/user.png') }}" 
                                         class="rounded-circle me-3" width="40" height="40" alt="User" style="object-fit: cover; border: 1px solid #eee;">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $user->first_name }} {{ $user->last_name }}</div>
                                        <div class="text-muted small">{{ $user->user_id }} | {{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge rounded-pill bg-light text-dark border px-3">
                                    {{ ucfirst(str_replace('_', ' ', $user->account_type)) }}
                                </span>
                            </td>
                            <td>
                                @if($user->profile_verification_status === 'verified')
                                    <span class="badge bg-success-subtle text-success px-3 py-2">Verified</span>
                                @elseif($user->profile_verification_status === 'pending')
                                    <span class="badge bg-warning-subtle text-warning px-3 py-2">Pending</span>
                                @elseif($user->profile_verification_status === 'suspended')
                                    <span class="badge bg-danger-subtle text-danger px-3 py-2">Suspended</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary px-3 py-2">{{ ucfirst($user->profile_verification_status) }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="small text-muted">{{ $user->created_at ? $user->created_at->format('d M Y') : 'N/A' }}</div>
                            </td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                        <li><a class="dropdown-item" href="{{ route('admin.users.show', $user->id) }}"><i class="fas fa-eye me-2 text-primary"></i> View Details</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.users.edit', $user->id) }}"><i class="fas fa-edit me-2 text-info"></i> Edit User</a></li>
                                        
                                        @if($user->profile_verification_status === 'pending')
                                        <li>
                                            <form action="{{ route('admin.approvals.approve', $user->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item"><i class="fas fa-check me-2 text-success"></i> Approve User</button>
                                            </form>
                                        </li>
                                        @endif

                                        <li>
                                            <form action="{{ route('admin.users.suspend', $user->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item">
                                                    @if($user->profile_verification_status === 'suspended')
                                                        <i class="fas fa-play me-2 text-success"></i> Unsuspend
                                                    @else
                                                        <i class="fas fa-pause me-2 text-warning"></i> Suspend
                                                    @endif
                                                </button>
                                            </form>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Delete this user permanently?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash me-2"></i> Delete User</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">No users found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
