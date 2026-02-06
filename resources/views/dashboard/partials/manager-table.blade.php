<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Sr. No.</th>
                <th>Manager ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td>{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                <td><span class="fw-bold">{{ $user->user_id }}</span></td>
                <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->phone }}</td>
                <td>
                    <div class="status-toggle" data-user-id="{{ $user->id }}">
                        <span class="badge px-3 py-2 cursor-pointer status-badge {{ $user->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}"
                            onclick="toggleUserStatus({{ $user->id }})">
                            @if($user->is_active)
                            <i class="fas fa-check-circle me-1"></i> Active
                            @else
                            <i class="fas fa-times-circle me-1"></i> Frozen
                            @endif
                        </span>
                    </div>
                </td>
                <td class="text-end">
                    <div class="d-flex justify-content-end gap-1">
                        <a class="btn btn-sm btn-light" href="{{ route('users.show', $user->id) }}" title="View Profile">
                            <i class="fas fa-user text-primary"></i>
                        </a>
                        @if(auth()->user()->account_type === 'admin')
                        <a class="btn btn-sm btn-light" href="{{ route('users.edit', $user->id) }}" title="Edit">
                            <i class="fas fa-edit text-info"></i>
                        </a>
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Delete this manager?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light" title="Delete">
                                <i class="fas fa-trash text-danger"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
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