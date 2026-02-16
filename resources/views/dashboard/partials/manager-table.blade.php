<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Sr. No.</th>
                <th>Manager ID</th>
                <th>Date of Reg.</th>
                <th>Last Active</th>
                <th>Full Name</th>
                <th>Country</th>
                <th>Email</th>
                <th>Task/Performance</th>
                <th>Contact</th>
                <th>Status</th>
                <!-- <th class="text-end">Actions</th> -->
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td>{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                <td><span class="fw-bold text-primary">{{ $user->user_id }}</span></td>
                <td>
                    <div class="small">{{ $user->created_at->format('d M Y') }}</div>
                    <div class="text-muted extra-small">{{ $user->created_at->format('H:i') }}</div>
                </td>
                <td>
                    @if($user->last_login_at)
                    <div class="small">{{ $user->last_login_at->format('d M Y') }}</div>
                    <div class="text-muted extra-small">{{ $user->last_login_at->format('H:i') }}</div>
                    @else
                    <span class="text-muted small">Never</span>
                    @endif
                </td>
                <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                <td><span class="badge bg-light text-dark border">{{ $user->country ?: 'N/A' }}</span></td>
                <td>
                    <div class="small">{{ $user->email }}</div>
                </td>
                <td>
                    <div class="small"></div>
                </td>
                <td>
                    <div class="small">{{ $user->phone }}</div>
                </td>
                <td>
                    <div class="status-toggle" data-user-id="{{ $user->id }}">
                        <span class="badge px-3 py-2 cursor-pointer status-badge {{ $user->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}"
                            onclick="toggleUserStatus({{ $user->id }})">
                            @if($user->is_active)
                            <i class="fas fa-check-circle me-1"></i> Active
                            @else
                            <i class="fas fa-lock me-1"></i> Frozen
                            @endif
                        </span>
                    </div>
                </td>
                <!-- <td class="text-end">
                    <div class="d-flex justify-content-end gap-1">
                        <a class="btn btn-sm btn-light" href="{{ route('users.show', $user->id) }}" title="View Profile">
                            <i class="fas fa-eye text-primary"></i>
                        </a>
                        @if(auth()->user()->isAdmin())
                        <a class="btn btn-sm btn-light" href="{{ route('users.edit', $user->id) }}" title="Edit">
                            <i class="fas fa-edit text-info"></i>
                        </a>
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="ajax-action d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light" title="Delete">
                                <i class="fas fa-trash text-danger"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </td> -->
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-5 text-muted">No managers found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4 d-flex justify-content-center ajax-pagination">
    {{ $users->links('pagination::bootstrap-5') }}
</div>