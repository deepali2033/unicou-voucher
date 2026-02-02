@extends('layouts.master')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 border-start border-primary border-4 rounded-4">
                <div class="card-body">
                    <div class="text-muted small mb-1">Total Queries</div>
                    <div class="d-flex align-items-center">
                        <h3 class="fw-bold mb-0">{{ $stats['total'] }}</h3>
                        <span class="ms-auto text-primary small fw-bold"><i class="fas fa-headset me-1"></i>All Time</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 border-start border-warning border-4 rounded-4">
                <div class="card-body">
                    <div class="text-muted small mb-1">Pending Queries</div>
                    <div class="d-flex align-items-center">
                        <h3 class="fw-bold mb-0 text-warning">{{ $stats['pending'] }}</h3>
                        <span class="ms-auto text-warning small fw-bold"><i class="fas fa-clock me-1"></i>Awaiting</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 border-start border-success border-4 rounded-4">
                <div class="card-body">
                    <div class="text-muted small mb-1">Closed Queries</div>
                    <div class="d-flex align-items-center">
                        <h3 class="fw-bold mb-0 text-success">{{ $stats['closed'] }}</h3>
                        <span class="ms-auto text-success small fw-bold"><i class="fas fa-check-circle me-1"></i>Resolved</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Management Section -->
    <div class="row g-4 mb-4">
        <!-- Add Topic -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">Manage Topics</h5>
                </div>
                <div class="card-body px-4">
                    <form id="addTopicForm" class="mb-3">
                        @csrf
                        <input type="hidden" name="type" value="topic">
                        <div class="input-group">
                            <input type="text" name="name" class="form-control border-0 bg-light shadow-none" placeholder="New Topic Name..." required style="border-radius: 10px 0 0 10px;">
                            <button class="btn btn-primary px-4" type="submit" style="border-radius: 0 10px 10px 0;">Add</button>
                        </div>
                    </form>
                    <div class="table-responsive" style="max-height: 200px;">
                        <table class="table table-sm table-hover">
                            <tbody>
                                @foreach($topics as $topic)
                                <tr id="option-row-{{ $topic->id }}">
                                    <td>{{ $topic->name }}</td>
                                    <td class="text-end">
                                        <button class="btn btn-link text-danger p-0 delete-option" data-id="{{ $topic->id }}"><i class="fas fa-trash-alt"></i></button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Issue -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">Manage Issues</h5>
                </div>
                <div class="card-body px-4">
                    <form id="addIssueForm" class="mb-3">
                        @csrf
                        <input type="hidden" name="type" value="issue">
                        <div class="mb-2">
                            <select name="parent_id" class="form-select border-0 bg-light shadow-none" required style="border-radius: 10px;">
                                <option value="" selected disabled>Select Parent Topic</option>
                                @foreach($topics as $topic)
                                <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="input-group">
                            <input type="text" name="name" class="form-control border-0 bg-light shadow-none" placeholder="New Issue Name..." required style="border-radius: 10px 0 0 10px;">
                            <button class="btn btn-primary px-4" type="submit" style="border-radius: 0 10px 10px 0;">Add</button>
                        </div>
                    </form>
                    <div class="table-responsive" style="max-height: 200px;">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr class="small text-muted">
                                    <th>Topic</th>
                                    <th>Issue</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($issues as $issue)
                                <tr id="option-row-{{ $issue->id }}">
                                    <td class="small text-primary">{{ $issue->parent->name ?? 'N/A' }}</td>
                                    <td>{{ $issue->name }}</td>
                                    <td class="text-end">
                                        <button class="btn btn-link text-danger p-0 delete-option" data-id="{{ $issue->id }}"><i class="fas fa-trash-alt"></i></button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Offcanvas Filter --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold" id="filterOffcanvasLabel">
                <i class="fas fa-filter me-2"></i>Filter Queries
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form id="filter-form" action="{{ route('customer.query') }}" method="GET">
                <div class="mb-4">
                    <label class="form-label fw-bold">Topic</label>
                    <select name="topic" class="form-select select2-filter">
                        <option value="">All Topics</option>
                        @foreach($topics as $t)
                        <option value="{{ $t->name }}" {{ request('topic') == $t->name ? 'selected' : '' }}>
                            {{ $t->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="d-grid gap-2 pt-3 border-top">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="{{ route('customer.query') }}" class="btn btn-light">Reset All</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white py-4 px-4 d-flex justify-content-between align-items-center border-0">
            <div>
                <h5 class="mb-0 fw-bold">Customer Support Queries</h5>
                <small class="text-muted">Manage and respond to customer support requests.</small>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary btn-sm px-3 shadow-sm rounded-pill" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-uppercase small fw-bold text-muted">
                        <tr>
                            <th class="px-4 py-3 border-0">User Info</th>
                            <th class="py-3 border-0">Role</th>
                            <th class="py-3 border-0">Topic</th>
                            <th class="py-3 border-0">Issue</th>
                            <th class="py-3 border-0">Message</th>
                            <th class="py-3 border-0 text-center">Status</th>
                            <th class="px-4 py-3 border-0 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($queries as $query)
                        <tr id="query-row-{{ $query->id }}">
                            <td class="px-4 py-4">
                                <div class="fw-bold">{{ $query->user->name ?? 'Guest' }}</div>
                                <small class="text-muted">{{ $query->user->email ?? '' }}</small>
                            </td>
                            <td><span class="badge bg-light text-dark">{{ ucfirst($query->user->account_type ?? 'user') }}</span></td>
                            <td>{{ $query->topic }}</td>
                            <td>{{ $query->issue }}</td>
                            <td>
                                <div class="text-truncate" style="max-width: 200px;" title="{{ $query->description }}">
                                    {{ $query->description }}
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill px-3 py-2 bg-{{ $query->status == 'pending' ? 'soft-warning text-warning' : ($query->status == 'closed' ? 'soft-success text-success' : 'soft-secondary text-secondary') }}">
                                    {{ ucfirst($query->status) }}
                                </span>
                            </td>
                            <td class="px-4 text-end">
                                <button class="btn btn-link text-danger p-0 delete-query" data-id="{{ $query->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                No Support Queries Found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($queries->hasPages())
        <div class="card-footer bg-white border-0 py-4 px-4">
            {{ $queries->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<style>
    .bg-soft-warning { background-color: rgba(255, 193, 7, 0.1); }
    .bg-soft-success { background-color: rgba(40, 167, 69, 0.1); }
    .bg-soft-secondary { background-color: rgba(108, 117, 125, 0.1); }
    .table thead th { border-bottom: none; }
    .input-group .form-control:focus { box-shadow: none; border: 1px solid #5e5ce6 !important; }
</style>
<script>
$(document).ready(function() {
    // Handle Add Topic/Issue
    $('#addTopicForm, #addIssueForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const btn = form.find('button');
        const originalText = btn.text();
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: "{{ route('support.options.store') }}",
            method: "POST",
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    location.reload();
                }
            },
            error: function() {
                toastr.error('Failed to add option');
                btn.prop('disabled', false).text(originalText);
            }
        });
    });

    // Handle Delete Option
    $('.delete-option').on('click', function() {
        if(!confirm('Are you sure?')) return;
        const id = $(this).data('id');
        const row = $(`#option-row-${id}`);
        
        $.ajax({
            url: "{{ route('support.options.destroy', ':id') }}".replace(':id', id),
            method: "DELETE",
            data: { _token: "{{ csrf_token() }}" },
            success: function(response) {
                if (response.success) {
                    row.fadeOut(300, function() { $(this).remove(); });
                    toastr.success(response.message);
                }
            }
        });
    });
});
</script>
@endpush
@endsection
