<div class="table-responsive">
    <table class="table table-hover align-middle" style="white-space: nowrap;">
        <thead class="table-light">
            <tr>
                <th>Sr. No.</th>
                <th>User ID</th>
                <th>Date of Reg.</th>
                <th>Last Active</th>
                <th>Full Name</th>
                <th>Country</th>
                <th>Email ID</th>
                <th>Highest Education</th>
                <th>Contact No.</th>
                <th>Vouchers Purchased</th>
                <th>Revenue Paid</th>
                <th>Disputed Payments</th>
                <th>Referral Points</th>
                <th>Bonus Points</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td>{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                <td>{{ $user->user_id }}</td>
                <td>{{ $user->created_at ? $user->created_at->format('d M Y') : 'N/A' }}</td>
                <td>{{ $user->last_login_at ? $user->last_login_at->format('d M Y H:i') : 'Never' }}</td>
                <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                <td>{{ $user->country }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->highest_education ?? 'N/A' }}</td>
                <td>{{ $user->phone }}</td>
                <td>{{ $user->orders_count ?? 0 }}</td>
                <td>{{ number_format($user->total_revenue ?? 0, 2) }}</td>
                <td>{{ $user->disputed_payments ?? 0 }}</td>
                <td>{{ $user->referral_points ?? 0 }}</td>
                <td>{{ $user->bonus_points ?? 0 }}</td>
                <td>
                    @php $canFreeze = auth()->user()->account_type !== 'manager' || auth()->user()->can_freeze_user; @endphp
                    <span class="badge px-3 py-2 {{ $canFreeze ? 'user-status-toggle' : '' }} {{ $user->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}"
                        @if($canFreeze) data-id="{{ $user->id }}" style="cursor:pointer;" title="Click to {{ $user->is_active ? 'freeze' : 'unfreeze' }}" @endif>
                        @if($user->is_active) <i class="fas fa-unlock me-1"></i> Active @else <i class="fas fa-lock me-1"></i> Frozen @endif
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="15" class="text-center py-5 text-muted">No students found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4 d-flex justify-content-center ajax-pagination">
    {{ $users->links('pagination::bootstrap-5') }}
</div>