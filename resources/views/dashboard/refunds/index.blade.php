@extends('layouts.master')
@section('content')
<div class="container-fluid">

    {{-- Offcanvas Filter --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold" id="filterOffcanvasLabel">
                <i class="fas fa-filter me-2"></i>Filter Refunds
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form id="filter-form" action="{{ route('refunds.index') }}" method="GET">
                <div class="mb-4">
                    <label class="form-label fw-bold">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Name, Email, Order ID..." value="{{ request('search') }}">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div class="d-grid gap-2 pt-3 border-top">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="{{ route('refunds.index') }}" class="btn btn-light">Reset All</a>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 fw-bold">Refund Requests</h5>
                <small class="text-muted">{{ $refunds->total() }} Requests Found</small>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary btn-sm px-3 shadow-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">User Info</th>
                            <th>Order ID</th>
                            <th>Amount</th>
                            <th>Reason</th>
                            <th>Bank Details</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($refunds as $refund)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $refund->user->name ?? 'Unknown' }}</div>
                                <div class="text-muted small">{{ $refund->user->email ?? 'N/A' }}</div>
                            </td>
                            <td><span class="fw-bold">{{ $refund->order_id }}</span></td>
                            <td><span class="fw-bold text-primary">RS {{ number_format($refund->amount) }}</span></td>
                            <td>
                                <div class="text-muted small" title="{{ $refund->reason }}">
                                    {{ Str::limit($refund->reason, 30) }}
                                </div>
                            </td>
                            <td>
                                <div class="text-muted small" title="{{ $refund->bank_details }}">
                                    {{ Str::limit($refund->bank_details, 30) }}
                                </div>
                            </td>
                            <td>
                                @if($refund->status == 'approved')
                                <span class="badge bg-success-subtle text-success px-3 py-2">Approved</span>
                                @elseif($refund->status == 'rejected')
                                <span class="badge bg-danger-subtle text-danger px-3 py-2">Rejected</span>
                                @else
                                <span class="badge bg-warning-subtle text-warning px-3 py-2">Pending</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                @if($refund->status == 'pending')
                                <div class="d-flex justify-content-end gap-1">
                                    <form action="{{ route('refunds.approve', $refund->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-light" title="Approve" onclick="return confirm('Approve this refund?')">
                                            <i class="fas fa-check-circle text-success"></i>
                                        </button>
                                    </form>
                                    <button class="btn btn-sm btn-light" title="Reject" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $refund->id }}">
                                        <i class="fas fa-times-circle text-danger"></i>
                                    </button>
                                </div>

                                <!-- Reject Modal -->
                                <div class="modal fade" id="rejectModal{{ $refund->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <form action="{{ route('refunds.reject', $refund->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-content border-0 shadow" style="border-radius: 15px;">
                                                <div class="modal-header border-0 pt-4 px-4">
                                                    <h5 class="fw-bold text-danger">Reject Refund: {{ $refund->order_id }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body px-4">
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-muted text-uppercase">Reason for Rejection</label>
                                                        <textarea name="note" class="form-control border-0 bg-light p-3" rows="3" placeholder="Explain why the refund is being rejected..." style="border-radius: 10px;" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 pb-4 px-4">
                                                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal" style="border-radius: 10px;">Cancel</button>
                                                    <button type="submit" class="btn btn-danger px-4" style="border-radius: 10px;">Confirm Rejection</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No refund requests found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0 py-4">
                <div class="d-flex justify-content-center">
                    {{ $refunds->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection