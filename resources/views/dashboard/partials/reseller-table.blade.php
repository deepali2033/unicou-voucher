<div class="table-responsive">
    <table class="table table-hover align-middle" style="white-space: nowrap;">
        <thead class="table-light">
            <tr>
                <th>Sr. No.</th>
                <th>User ID</th>
                <th>Date of Reg.</th>
                <th>Last Active date and time</th>
                <th>Category</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Country</th>
                <th>email ID(official)</th>
                <th>contact No.</th>
                <th>Total Agent</th>
                <th>Total Vouchers Purchased</th>
                <th>Total Revenue Paid</th>
                <th>Disputed Payments</th>
                <th>Available Referral Points</th>
                <th>Available Bonus Points</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td>{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                <td>{{ $user->user_id }}</td>
                <td>{{ $user->created_at ? $user->created_at->format('d M Y') : 'N/A' }}</td>
                <td>{{ $user->last_login_at ? $user->last_login_at->format('d M Y H:i') : 'Never' }}</td>
                <td>
                    @if(auth()->user()->account_type !== 'manager' || auth()->user()->can_edit_user)
                    <select class="form-select form-select-sm category-select" data-user-id="{{ $user->id }}" style="min-width:140px;">
                        <option value="silver" {{ $user->category == 'silver' ? 'selected' : '' }}>🥈 Silver</option>
                        <option value="gold" {{ $user->category == 'gold' ? 'selected' : '' }}>🥇 Gold</option>
                        <option value="diamond" {{ $user->category == 'diamond' ? 'selected' : '' }}>💎 Diamond</option>
                    </select>
                    @else
                    <span class="badge bg-secondary">{{ ucfirst($user->category) }}</span>
                    @endif
                </td>
                <td>{{ $user->first_name }}</td>
                <td>{{ $user->last_name }}</td>
                <td>{{ $user->country_iso }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->phone }}</td>
                <td>0</td>
                <td>{{ $user->orders->where('status', 'delivered')->count() }}</td>
                <td>{{ number_format($user->orders->where('status', 'delivered')->sum('amount'), 2) }}</td>
                <td>{{ $user->orders->where('status', 'disputed')->count() }}</td>
                <td>{{ $user->orders->sum('referral_points') }}</td>
                <td>{{ number_format($user->orders->sum('bonus_amount'), 2) }}</td>
                <td>
                    @php $canFreeze = auth()->user()->account_type !== 'manager' || auth()->user()->can_freeze_user; @endphp
                    <span class="badge px-3 py-2 {{ $canFreeze ? 'user-status-toggle' : '' }} {{ $user->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}"
                        @if($canFreeze) data-id="{{ $user->id }}" style="cursor:pointer;" title="Click to {{ $user->is_active ? 'freeze' : 'unfreeze' }}" @endif>
                        @if($user->is_active) <i class="fas fa-unlock me-1"></i> Active @else <i class="fas fa-lock me-1"></i> Frozen @endif
                    </span>
                </td>
                <td class="text-end">
                    <div class="d-flex justify-content-end gap-1">
                        @if(auth()->user()->account_type === 'admin')
                        <form action="{{ route('users.impersonate', $user->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-light" title="Login as {{ $user->first_name }}">
                                <i class="fas fa-external-link-alt text-warning"></i>
                            </button>
                        </form>
                        @endif
                        <a class="btn btn-sm btn-light" href="{{ route('users.show', $user->id) }}" title="View"><i class="fas fa-eye text-primary"></i></a>
                        @if(auth()->user()->account_type !== 'manager' || auth()->user()->can_edit_user)
                        <a class="btn btn-sm btn-light" href="{{ route('users.edit', $user->id) }}" title="Edit"><i class="fas fa-edit text-info"></i></a>
                        @endif
                        @if(auth()->user()->account_type === 'admin')
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="ajax-action">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light" title="Delete"><i class="fas fa-trash text-danger"></i></button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="18" class="text-center py-5 text-muted">No users found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4 d-flex justify-content-center ajax-pagination">
    {{ $users->links('pagination::bootstrap-5') }}
</div>