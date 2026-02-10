@extends('layouts.master')
@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">My Refund Requests</h4>
            <p class="text-muted small mb-0">Track your refund submissions and their status.</p>
        </div>
        <button class="btn btn-primary px-4 shadow-sm" style="border-radius: 10px;" data-bs-toggle="modal" data-bs-target="#requestRefundModal">
            <i class="fas fa-plus me-2"></i> Request Refund
        </button>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">Refund History</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Date</th>
                            <th>Order ID</th>
                            <th>Amount</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th class="pe-4">Admin Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($refunds as $refund)
                        <tr>
                            <td class="ps-4">
                                <div class="text-dark small">{{ $refund->created_at->format('d M Y') }}</div>
                            </td>
                            <td><span class="fw-bold">{{ $refund->order_id }}</span></td>
                            <td><span class="fw-bold text-primary">RS {{ number_format($refund->amount) }}</span></td>
                            <td><div class="text-muted small">{{ Str::limit($refund->reason, 40) }}</div></td>
                            <td>
                                @if($refund->status == 'approved')
                                <span class="badge bg-success-subtle text-success px-3 py-2">Approved</span>
                                @elseif($refund->status == 'rejected')
                                <span class="badge bg-danger-subtle text-danger px-3 py-2">Rejected</span>
                                @else
                                <span class="badge bg-warning-subtle text-warning px-3 py-2">Pending</span>
                                @endif
                            </td>
                            <td class="pe-4">
                                <div class="text-muted small">{{ $refund->admin_note ?: 'N/A' }}</div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">You haven't requested any refunds yet.</td>
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

<!-- Request Refund Modal -->
<div class="modal fade" id="requestRefundModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('refunds.store') }}" method="POST">
            @csrf
            <div class="modal-content border-0 shadow" style="border-radius: 20px;">
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="fw-bold">Submit Refund Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Order ID</label>
                        <select name="order_id" id="refund_order_id" class="form-select border-0 bg-light p-3" style="border-radius: 12px;" required>
                            <option value="">Select an eligible order...</option>
                            @foreach($eligibleOrders as $order)
                            <option value="{{ $order->order_id }}" data-amount="{{ $order->amount }}">
                                {{ $order->order_id }} - RS {{ number_format($order->amount) }} ({{ ucfirst($order->status) }})
                            </option>
                            @endforeach
                        </select>
                        <div class="form-text extra-small mt-1">Only orders not yet delivered are shown.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Refund Amount (RS)</label>
                        <input type="number" name="amount" id="refund_amount" class="form-control border-0 bg-light p-3" placeholder="Amount to be refunded..." style="border-radius: 12px;" required readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Reason for Refund</label>
                        <textarea name="reason" class="form-control border-0 bg-light p-3" rows="3" placeholder="Why are you requesting a refund?" style="border-radius: 12px;" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Your Bank Details (For Refund)</label>
                        <textarea name="bank_details" class="form-control border-0 bg-light p-3" rows="3" placeholder="Bank Name, Account Number, IFSC/SWIFT..." style="border-radius: 12px;" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal" style="border-radius: 12px;">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4" style="border-radius: 12px;">Submit Request</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#refund_order_id').on('change', function() {
            let amount = $(this).find(':selected').data('amount');
            if(amount) {
                $('#refund_amount').val(amount);
            } else {
                $('#refund_amount').val('');
            }
        });
    });
</script>
@endpush