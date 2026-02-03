<div class="table-responsive">
    <table class="table table-hover align-middle" style="white-space: nowrap;">
        <thead class="table-light">
            <tr>
                <th>User Info</th>
                <th>Role</th>
                <th>Account Status</th>
                <th>Verified Status</th>
                <th>Risk Level</th>
                <th>Compliance Flags</th>

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
                            class="rounded-circle me-3" width="40" height="40"
                            style="object-fit: cover; border: 1px solid #eee;">
                        <div>
                            <div class="fw-bold text-dark">
                                {{ $user->first_name }} {{ $user->last_name }}
                            </div>
                            <div class="text-muted small">
                                <div class="user_id">{{ $user->user_id }}</div>
                                <div class="user_id_email">{{ $user->email }}</div>
                            </div>
                        </div>
                    </div>
                </td>

                <td>
                    <span class="badge rounded-pill bg-light text-dark border px-3">
                        {{ ucfirst(str_replace('_', ' ', $user->account_type)) }}
                    </span>
                </td>



                <td>
                    <div class="d-flex align-items-center gap-2">
                        <span
                            class="badge px-3 py-2 user-status-toggle
                    {{ $user->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}"
                            data-id="{{ $user->id }}"
                            style="cursor:pointer;"
                            title="Click to {{ $user->is_active ? 'freeze' : 'unfreeze' }}">
                            @if($user->is_active)
                            <i class="fas fa-unlock me-1"></i> Active
                            @else
                            <i class="fas fa-lock me-1"></i> Frozen
                            @endif
                        </span>

                    </div>
                </td>

                <td>
                    <span
                        class="badge px-3 py-2 verification-status-toggle
                        {{ in_array($user->profile_verification_status, ['verified', 'approved']) ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }}"
                        data-id="{{ $user->id }}"
                        style="cursor:pointer;"
                        title="Click to {{ in_array($user->profile_verification_status, ['verified', 'approved']) ? 'mark as pending' : 'verify' }}">
                        @if(in_array($user->profile_verification_status, ['verified', 'approved']))
                        <i class="fas fa-check-circle me-1"></i> Approved
                        @else
                        <i class="fas fa-clock me-1"></i> Pending
                        @endif
                    </span>
                </td>
                <td>
                    @php
                    $risk = strtolower($user->countryRisk->risk_level ?? 'low');
                    @endphp

                    @if($risk === 'high')
                    <span class="badge bg-danger-subtle text-danger">High</span>

                    @elseif($risk === 'medium')
                    <span class="badge bg-warning-subtle text-warning">Medium</span>

                    @else
                    <span class="badge bg-info-subtle text-info">Low</span>
                    @endif
                </td>
                <td>
                    <div class="d-flex flex-wrap gap-1">
                        @if($user->email_verified_at)
                        <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.7rem;">Email Verified</span>
                        @else
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size: 0.7rem;">Email Unverified</span>
                        @endif

                        @if($user->aadhar_card || $user->pan_card || ($user->agentDetail && $user->agentDetail->id_doc))
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.7rem;">Docs Uploaded</span>
                        @endif
                    </div>
                </td>
                <td>
                    <div class="small text-muted">
                        {{ $user->created_at ? $user->created_at->format('d M Y') : 'N/A' }}
                    </div>
                </td>

                {{-- Actions (Clean) --}}
                <td class="text-end">
                    <div class="d-flex justify-content-end gap-1">
                        <form action="{{ route('users.impersonate', $user->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-light" title="Login as {{ $user->first_name }}">
                                <i class="fas fa-external-link-alt text-warning"></i>
                            </button>
                        </form>
                        <a class="btn btn-sm btn-light"
                            href="{{ route('users.show', $user->id) }}" title="View">
                            <i class="fas fa-eye text-primary"></i>
                        </a>

                        <a class="btn btn-sm btn-light"
                            href="{{ route('users.edit', $user->id) }}" title="Edit">
                            <i class="fas fa-edit text-info"></i>
                        </a>

                        @if(auth()->user()->account_type !== 'manager')
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="ajax-action">
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
                <td colspan="6" class="text-center py-5 text-muted">
                    No users found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>