@extends('layouts.master')

@section('content')
<div class="container-fluid py-4">
    @if( auth()->user()->isAgent() || auth()->user()->isStudent() || auth()->user()->isResellerAgent())
    {{-- Offcanvas Filter --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold" id="filterOffcanvasLabel">
                <i class="fas fa-filter me-2"></i>Filter Referral Points
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form id="filter-form" action="{{ route('referral') }}" method="GET">
                <div class="mb-4">
                    <label class="form-label fw-bold">Order ID</label>
                    <input type="text" name="order_id" class="form-control" placeholder="Search order ID..." value="{{ request('order_id') }}">
                </div>

                <div class="d-grid gap-2 pt-3 border-top">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="{{ route('referral') }}" class="btn btn-light">Reset All</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-primary border-4">
                <div class="card-body">
                    <div class="text-muted small mb-1">Total Referral Points</div>
                    <div class="d-flex align-items-center">
                        <h3 class="fw-bold mb-0">{{ number_format($totalPoints) }}</h3>
                        <span class="ms-auto text-success small fw-bold"><i class="fas fa-coins me-1"></i>Earned</span>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 fw-bold">Referral Points History</h5>
                <small class="text-muted">Track your referral earnings from voucher purchases.</small>
            </div>
            <div class="d-flex gap-2">
                <a href="#" id="csv-export-link" class="btn btn-success btn-sm px-3 shadow-sm">
                    <i class="fas fa-file-csv me-1"></i> CSV
                </a>
                <button class="btn btn-outline-primary btn-sm px-3 shadow-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 border-0">S.NO</th>
                            <th class="py-3 border-0">ORDER ID</th>
                            <th class="py-3 border-0">USER</th>
                            <th class="py-3 border-0">VOUCHER</th>
                            <th class="py-3 border-0 text-end">AMOUNT</th>
                            <th class="py-3 border-0 text-center">DATE</th>
                            <th class="py-3 border-0 text-center">REFERRAL POINTS</th>
                            <th class="py-3 border-0 text-center">STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($referralHistory as $index => $order)
                        <tr>
                            <td class="px-4">{{ $referralHistory->firstItem() + $index }}</td>
                            <td class="fw-bold text-primary">{{ $order->order_id }}</td>
                            <td>{{ auth()->user()->name }}</td>
                            <td>{{ $order->voucher_type }}</td>
                            <td class="text-end fw-bold">RS {{ number_format($order->amount) }}</td>
                            <td class="text-center small text-muted">
                                {{ $order->created_at->format('Y-m-d') }}<br>
                                {{ $order->created_at->format('H:i') }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-soft-success text-success">+{{ $order->referral_points }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success">Credited</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">No referral history found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($referralHistory->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $referralHistory->links() }}
        </div>
        @endif
    </div>
    @endif
</div>

@push('scripts')
<style>
    .bg-soft-success {
        background-color: rgba(40, 167, 69, 0.1);
    }
</style>
@endpush
@endsection