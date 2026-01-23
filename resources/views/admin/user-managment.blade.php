@extends('admin.layout.app')

@section('content')
<div class="container-fluid">

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form action="{{ route('admin.users.management') }}" method="GET" class="row g-3 align-items-center">
                        <div class="col-auto">
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.users.management', ['role' => 'all', 'search' => request('search')]) }}" 
                                   class="btn btn-outline-primary {{ request('role', 'all') == 'all' ? 'active' : '' }}">All</a>
                                <a href="{{ route('admin.users.management', ['role' => 'student', 'search' => request('search')]) }}" 
                                   class="btn btn-outline-primary {{ request('role') == 'student' ? 'active' : '' }}">Students</a>
                                <a href="{{ route('admin.users.management', ['role' => 'manager', 'search' => request('search')]) }}" 
                                   class="btn btn-outline-primary {{ request('role') == 'manager' ? 'active' : '' }}">Managers</a>
                                <a href="{{ route('admin.users.management', ['role' => 'reseller_agent', 'search' => request('search')]) }}" 
                                   class="btn btn-outline-primary {{ request('role') == 'reseller_agent' ? 'active' : '' }}">Agents</a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Search by name, email, ID or phone..." value="{{ request('search') }}">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        @if(request('search') || request('role'))
                        <div class="col-auto">
                            <a href="{{ route('admin.users.management') }}" class="btn btn-light">Clear</a>
                        </div>
                        @endif
                        <div class="col text-end">
                            <button type="button" class="btn btn-danger">
                                <i class="fas fa-file-pdf me-1"></i> Download PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

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
                            <th>Contact</th>
                            <th>Location</th>
                            <th>Created By</th>
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
                                         class="rounded-circle me-3" width="45" height="45" alt="User Image" style="object-fit: cover;">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $user->first_name }} {{ $user->last_name }}</div>
                                        <div class="text-muted small">{{ $user->user_id }}</div>
                                        @if($user->email_verified_at)
                                            <span class="badge bg-success-subtle text-success px-2 py-1" style="font-size: 0.7rem;">Verified</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger px-2 py-1" style="font-size: 0.7rem;">Unverified</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge rounded-pill bg-light text-dark border px-3">
                                    {{ ucfirst(str_replace('_', ' ', $user->account_type)) }}
                                </span>
                            </td>
                            <td>
                                <div class="small"><i class="fas fa-envelope me-1 text-muted"></i> {{ $user->email }}</div>
                                <div class="small"><i class="fas fa-phone me-1 text-muted"></i> {{ $user->phone ?? 'N/A' }}</div>
                            </td>
                            <td>
                                <span class="text-uppercase fw-semibold text-muted">{{ $user->country_iso ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <span class="small">{{ $user->verifiedBy->name ?? 'System' }}</span>
                            </td>
                            <td>
                                <div class="small text-muted">{{ $user->created_at ? $user->created_at->format('d M Y') : 'N/A' }}</div>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete User">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-users fa-3x mb-3"></i>
                                    <p>No users found matching your criteria.</p>
                                </div>
                            </td>
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