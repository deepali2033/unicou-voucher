@extends('layouts.master')

@section('content')
<div class="container-fluid">

    {{-- Offcanvas Filter --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold" id="filterOffcanvasLabel">
                <i class="fas fa-filter me-2"></i>Filter Users
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form id="filter-form" action="{{ route('users.management') }}" method="GET">
                <div class="mb-4">
                    <label class="form-label fw-bold">Search</label>
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Name, Email, ID..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Date Range</label>
                    <div class="d-flex flex-column gap-2">
                        <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}" placeholder="From">
                        <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}" placeholder="To">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Role</label>
                    <select name="role" class="form-select">
                        <option value="all" {{ request('role') == 'all' ? 'selected' : '' }}>All Roles</option>
                        <option value="manager" {{ request('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="support_team" {{ request('role') == 'support_team' ? 'selected' : '' }}>Support Team</option>
                        <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Student</option>
                        <option value="reseller_agent" {{ request('role') == 'reseller_agent' ? 'selected' : '' }}>Reseller Agent</option>
                        <option value="agent" {{ request('role') == 'agent' ? 'selected' : '' }}>Agent</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Status</label>
                    <select name="status" class="form-select">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Frozen</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Verification Status</label>
                    <select name="verification_status" class="form-select">
                        <option value="all" {{ request('verification_status') == 'all' ? 'selected' : '' }}>All</option>
                        <option value="pending" {{ request('verification_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="verified" {{ request('verification_status') == 'verified' ? 'selected' : '' }}>Approved</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Compliance Flags</label>
                    <select name="flags" class="form-select">
                        <option value="all" {{ request('flags') == 'all' ? 'selected' : '' }}>All Flags</option>
                        <option value="email_verified" {{ request('flags') == 'email_verified' ? 'selected' : '' }}>Email Verified</option>
                        <option value="doc_verified" {{ request('flags') == 'doc_verified' ? 'selected' : '' }}>Document Verified</option>
                    </select>
                </div>

                <div class="d-grid gap-2 pt-3 border-top">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="{{ route('users.management') }}" class="btn btn-light">Reset All</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 fw-bold">User Management</h5>
                <small class="text-muted total-count">{{ $users->total() }} Users Found</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('users.pdf', request()->all()) }}" id="csv-export-link" class="btn btn-success btn-sm px-3 shadow-sm">
                    <i class="fas fa-file-csv me-1"></i> CSV
                </a>
                <a href="{{ route('users.create') }}" class="btn btn-success btn-sm px-3 shadow-sm">
                    <i class="fas fa-file-csv me-1"></i> Add
                </a>
                <button class="btn btn-outline-primary btn-sm px-3 shadow-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                <!-- <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm">
                    <i class="fas fa-plus me-1"></i> Add User
                </a> -->
            </div>
        </div>
        <div class="card-body" id="table-container">
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
                            <th>Total Vouchers purchased</th>
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
                                <select class="form-select form-select-sm account-type-select"
                                    data-user-id="{{ $user->id }}"
                                    style="min-width:140px;">
                                    <option value="silver" {{ $user->account_type == 'silver' ? 'selected' : '' }}>
                                        🥈 Silver
                                    </option>
                                    <option value="gold" {{ $user->account_type == 'gold' ? 'selected' : '' }}>
                                        🥇 Gold
                                    </option>
                                    <option value="diamond" {{ $user->account_type == 'diamond' ? 'selected' : '' }}>
                                        💎 Diamond
                                    </option>
                                </select>
                            </td>
                            <td>{{ $user->first_name }}</td>
                            <td>{{ $user->last_name }}</td>
                            <td>{{ $user->country_iso }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone }}</td>
                            <td>{{ $user->orders->where('status', 'delivered')->count() }}</td>
                            <td>{{ number_format($user->orders->where('status', 'delivered')->sum('amount'), 2) }}</td>
                            <td>{{ $user->orders->where('status', 'disputed')->count() }}</td>
                            <td>{{ $user->orders->sum('referral_points') }}</td>
                            <td>{{ number_format($user->orders->sum('bonus_amount'), 2) }}</td>
                            <td>
                                <span class="badge px-3 py-2 user-status-toggle {{ $user->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}"
                                    data-id="{{ $user->id }}"
                                    style="cursor:pointer;"
                                    title="Click to {{ $user->is_active ? 'freeze' : 'unfreeze' }}">
                                    @if($user->is_active)
                                    <i class="fas fa-unlock me-1"></i> Active
                                    @else
                                    <i class="fas fa-lock me-1"></i> Frozen
                                    @endif
                                </span>
                            </td>

                            {{-- Actions (Clean) --}}
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-light" title="Access User Page">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    <a class="btn btn-sm btn-light"
                                        href="{{ route('users.show', $user->id) }}" title="View">
                                        <i class="fas fa-eye text-primary"></i>
                                    </a>

                                    <a class="btn btn-sm btn-light"
                                        href="{{ route('users.edit', $user->id) }}" title="Edit">
                                        <i class="fas fa-edit text-info"></i>
                                    </a>

                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="ajax-action">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light" title="Delete">
                                            <i class="fas fa-trash text-danger"></i>
                                        </button>
                                    </form>



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
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        function updateTable(url) {
            $.ajax({
                url: url,
                success: function(data) {
                    $('#table-container').html(data);
                    // Update URL without reload
                    window.history.pushState({}, '', url);

                    // Update CSV export link
                    let csvUrl = "{{ route('users.pdf') }}?" + (url.split('?')[1] || '');
                    $('#csv-export-link').attr('href', csvUrl);
                }
            });
        }

        // Handle Pagination
        $(document).on('click', '.ajax-pagination a', function(e) {
            e.preventDefault();
            updateTable($(this).attr('href'));
        });

        // Handle Filters & Search
        $(document).on('submit', '#filter-form', function(e) {
            e.preventDefault();
            let url = $(this).attr('action') + '?' + $(this).serialize();
            updateTable(url);

            // Close offcanvas
            var offcanvasElement = document.getElementById('filterOffcanvas');
            var offcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
            if (offcanvas) offcanvas.hide();
        });

        $('.btn-group a').on('click', function(e) {
            e.preventDefault();
            $('.btn-group a').removeClass('active');
            $(this).addClass('active');
            updateTable($(this).attr('href'));
        });

        // Handle AJAX Actions (Suspend, Delete)
        $(document).on('submit', '.ajax-action', function(e) {
            e.preventDefault();
            let form = $(this);
            let url = form.attr('action');
            let method = form.find('input[name="_method"]').val() || 'POST';

            if (method === 'DELETE' && !confirm('Are you sure you want to delete this?')) {
                return;
            }

            $.ajax({
                url: url,
                method: method,
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.success);
                        // Refresh table with current filters
                        updateTable(window.location.href);
                    }
                },
                error: function(xhr) {
                    toastr.error('Something went wrong. Please try again.');
                }
            });
        });
    });
</script>
@endpush
@endsection