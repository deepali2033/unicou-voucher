@extends('layouts.master')

@section('content')
<div class="container-fluid">

    {{-- Offcanvas Filter --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold" id="filterOffcanvasLabel">
                <i class="fas fa-filter me-2"></i>Filter Managers
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form id="filter-form" action="{{ route('manager.page') }}" method="GET">
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
                    <label class="form-label fw-bold">Status</label>
                    <select name="status" class="form-select">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Frozen</option>
                    </select>
                </div>

                <div class="d-grid gap-2 pt-3 border-top">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="{{ route('manager.page') }}" class="btn btn-light">Reset All</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 fw-bold text-dark">Manager Management</h5>
                <small class="text-muted total-count">{{ $users->total() }} Managers Found</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('users.pdf', ['role' => 'manager']) }}" id="csv-export-link" class="btn btn-success btn-sm px-3 shadow-sm">
                    <i class="fas fa-file-csv me-1"></i> Export CSV
                </a>
                @if(auth()->user()->account_type === 'admin')
                <a href="{{ route('users.create') }}?role=manager" class="btn btn-primary btn-sm px-3 shadow-sm">
                    <i class="fas fa-plus me-1"></i> Add Manager
                </a>
                @endif
                <button class="btn btn-outline-primary btn-sm px-3 shadow-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
            </div>
        </div>
        <div class="card-body" id="table-container">
            @include('dashboard.partials.manager-table')
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleUserStatus(userId) {
        if (!confirm('Are you sure you want to change this user\'s status?')) return;

        fetch(`/dashboard/users/${userId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'active' || data.status === 'frozen') {
                    const container = document.querySelector(`.status-toggle[data-user-id="${userId}"]`);
                    const badge = container.querySelector('.status-badge');

                    if (data.status === 'active') {
                        badge.className = 'badge px-3 py-2 cursor-pointer status-badge bg-success-subtle text-success';
                        badge.innerHTML = '<i class="fas fa-check-circle me-1"></i> Active';
                    } else {
                        badge.className = 'badge px-3 py-2 cursor-pointer status-badge bg-danger-subtle text-danger';
                        badge.innerHTML = '<i class="fas fa-times-circle me-1"></i> Frozen';
                    }

                    toastr.success(data.message);
                } else {
                    toastr.error(data.message || 'Error updating status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toastr.error('Something went wrong');
            });
    }
</script>
@endpush

<style>
    .cursor-pointer {
        cursor: pointer;
    }

    .bg-primary-soft {
        background-color: rgba(13, 110, 253, 0.1);
    }

    .bg-success-soft {
        background-color: rgba(25, 135, 84, 0.1);
    }

    .bg-info-soft {
        background-color: rgba(13, 202, 240, 0.1);
    }

    .bg-warning-soft {
        background-color: rgba(255, 193, 7, 0.1);
    }

    .bg-danger-soft {
        background-color: rgba(220, 53, 69, 0.1);
    }

    .bg-dark-soft {
        background-color: rgba(33, 37, 41, 0.1);
    }

    .btn-xs {
        padding: 1px 5px;
        font-size: 10px;
    }
</style>
@endsection