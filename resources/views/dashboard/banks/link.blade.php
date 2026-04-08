@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Linked Banks & Wallet</h4>
            <p class="text-muted small mb-0">Manage your linked bank accounts and wallet credit.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#addMoneyModal">
                <i class="fas fa-plus-circle me-1"></i> Add Wallet Credit
            </button>
            <button class="btn btn-outline-primary shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#linkBankModal">
                <i class="fas fa-link me-1"></i> Link New Bank
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 border-start border-primary border-4 h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1 text-uppercase fw-bold">Wallet Balance</div>
                    <div class="d-flex align-items-center">
                        <h3 class="fw-bold mb-0">{{ auth()->user()->currency }} {{ number_format(auth()->user()->wallet_balance, 2) }}</h3>
                        <span class="ms-auto text-primary fw-bold small"><i class="fas fa-wallet me-1"></i>Available</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 border-start border-info border-4 h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1 text-uppercase fw-bold">Total Bank Balance</div>
                    <div class="d-flex align-items-center">
                        <h3 class="fw-bold mb-0">{{ auth()->user()->currency }} {{ number_format($totalBalance, 2) }}</h3>
                        <span class="ms-auto text-info fw-bold small"><i class="fas fa-university me-1"></i>{{ $banks->count() }} Accounts</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 border-start border-success border-4 h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1 text-uppercase fw-bold">Total Net Worth</div>
                    <div class="d-flex align-items-center">
                        <h3 class="fw-bold mb-0">{{ auth()->user()->currency }} {{ number_format(auth()->user()->wallet_balance + $totalBalance, 2) }}</h3>
                        <span class="ms-auto text-success fw-bold small"><i class="fas fa-chart-pie me-1"></i>Combined</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Banks Grid -->
        <div class="col-lg-12">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold">My Linked Bank Accounts</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @forelse($banks as $bank)
                        <div class="col-md-4">
                            <div class="card border shadow-none h-100" style="border-radius: 12px; transition: all 0.2s;">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between mb-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-primary bg-opacity-10 p-2 rounded text-primary fw-bold" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                                {{ substr($bank->bank_name, 0, 1) }}
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold">{{ $bank->bank_name }}</h6>
                                                <small class="text-muted">{{ $bank->account_type }}</small>
                                            </div>
                                        </div>
                                        <span class="badge {{ $bank->is_verified ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} rounded-pill align-self-start">
                                            {{ $bank->is_verified ? 'Verified' : 'Pending' }}
                                        </span>
                                    </div>
                                    <div class="mb-3">
                                        <div class="text-muted small">Current Balance</div>
                                        <h5 class="fw-bold mb-0 text-dark">{{ auth()->user()->currency }} {{ number_format($bank->balance, 2) }}</h5>
                                    </div>
                                    <div class="d-flex justify-content-between border-top pt-2 mt-2">
                                        <span class="text-muted small font-monospace">•••• {{ substr($bank->account_number, -4) }}</span>
                                        <span class="text-muted small">{{ $bank->account_holder_name }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-university fa-3x mb-2 opacity-25"></i>
                                <p>No bank accounts linked yet.</p>
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#linkBankModal">Link Now</button>
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="col-lg-12">
            <div class="card shadow-sm border-0 overflow-hidden">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">Recent Wallet Transactions</h6>
                    <span class="badge bg-primary-subtle text-primary fw-bold">Ledger</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="text-muted small text-uppercase">
                                <th class="ps-4">Description</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th class="text-end pe-4">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $trx)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="p-2 rounded bg-light">
                                            @if($trx->type == 'credit')
                                            <i class="fas fa-arrow-alt-circle-down text-success"></i>
                                            @else
                                            <i class="fas fa-arrow-alt-circle-up text-danger"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark small">{{ $trx->description }}</div>
                                            <div class="text-muted" style="font-size: 0.7rem;">REF: #WLT-{{ $trx->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($trx->type == 'credit')
                                    <span class="badge bg-success-subtle text-success px-2 py-1">Credit</span>
                                    @else
                                    <span class="badge bg-danger-subtle text-danger px-2 py-1">Debit</span>
                                    @endif
                                </td>
                                <td class="text-muted small">
                                    {{ $trx->created_at->format('d M Y') }}
                                    <div class="small opacity-75">{{ $trx->created_at->format('h:i A') }}</div>
                                </td>
                                <td class="text-end pe-4 fw-bold {{ $trx->type == 'credit' ? 'text-success' : 'text-danger' }}">
                                    {{ $trx->type == 'credit' ? '+' : '-' }}{{ auth()->user()->currency }} {{ number_format($trx->amount, 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    No wallet transactions found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($transactions->hasPages())
                <div class="card-footer bg-white py-3">
                    <div class="d-flex justify-content-center">
                        @include('dashboard.partials.custom-pagination', ['items' => $transactions])
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add Money Modal -->
<div class="modal fade" id="addMoneyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold">Add Wallet Credit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addMoneyForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">SOURCE BANK ACCOUNT</label>
                        <select name="bank_id" class="form-select border shadow-none p-2" required>
                            <option value="">Select your account...</option>
                            @foreach($banks as $bank)
                            <option value="{{ $bank->id }}">{{ $bank->bank_name }} (•••• {{ substr($bank->account_number, -4) }}) - Bal: {{ auth()->user()->currency }} {{ number_format($bank->balance, 2) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">TRANSFER AMOUNT</label>
                        <div class="input-group border rounded p-1 bg-light">
                            <span class="input-group-text bg-transparent border-0 fw-bold text-primary">{{ auth()->user()->currency }}</span>
                            <input type="number" name="amount" class="form-control bg-transparent border-0 shadow-none fw-bold" placeholder="0.00" min="1" step="0.01" required>
                        </div>
                    </div>
                    <div class="alert bg-primary-subtle border-0 small text-primary p-3 mb-0">
                        <i class="fas fa-info-circle me-2"></i> Funds will be credited to your UniCou wallet instantly.
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">Process Transfer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Link Bank Modal -->
<div class="modal fade" id="linkBankModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-bottom py-3 bg-light">
                <h5 class="modal-title fw-bold">Link New Bank Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="linkBankForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">BANK NAME</label>
                        <input type="text" name="bank_name" class="form-control border p-2 shadow-none" placeholder="e.g. HDFC, SBI, Standard Chartered" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">ACCOUNT HOLDER NAME</label>
                        <input type="text" name="account_holder_name" class="form-control border p-2 shadow-none" placeholder="Exact name as per bank" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">ACCOUNT NUMBER</label>
                        <input type="text" name="account_number" class="form-control border p-2 shadow-none" placeholder="Enter complete number" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted">IFSC / ROUTING</label>
                            <input type="text" name="ifsc_code" class="form-control border p-2 shadow-none" placeholder="Optional">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted">CURRENT BAL</label>
                            <input type="number" name="balance" class="form-control border p-2 shadow-none" placeholder="0.00" min="0" step="0.01">
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold small text-muted">ACCOUNT TYPE</label>
                        <select name="account_type" class="form-select border p-2 shadow-none">
                            <option value="Savings">Savings Account</option>
                            <option value="Current">Current Account</option>
                            <option value="Business">Business Account</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-dark w-100 py-2 fw-bold shadow-sm">Confirm Account Link</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('css')
<style>
    .bg-success-subtle {
        background-color: #ecfdf5 !important;
    }

    .bg-warning-subtle {
        background-color: #fffbeb !important;
    }

    .bg-danger-subtle {
        background-color: #fef2f2 !important;
    }

    .bg-primary-subtle {
        background-color: #eff6ff !important;
    }

    .text-success {
        color: #10b981 !important;
    }

    .text-warning {
        color: #f59e0b !important;
    }

    .text-danger {
        color: #ef4444 !important;
    }

    .text-primary {
        color: #2563eb !important;
    }

    .table thead th {
        font-weight: 700;
        background-color: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }

    .card {
        border-radius: 10px;
    }

    .btn {
        border-radius: 8px;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Add Money Form Submit
        $('#addMoneyForm').on('submit', function(e) {
            e.preventDefault();
            const btn = $(this).find('button[type="submit"]');
            const originalText = btn.html();

            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Processing...');

            $.ajax({
                url: "{{ route('wallet.add-money') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        $('#addMoneyModal').modal('hide');
                        toastr.success(response.message);
                        setTimeout(() => window.location.reload(), 1500);
                    }
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Transfer failed';
                    toastr.error(msg);
                    btn.prop('disabled', false).html(originalText);
                }
            });
        });

        // Link Bank Form Submit
        $('#linkBankForm').on('submit', function(e) {
            e.preventDefault();
            const btn = $(this).find('button[type="submit"]');
            const originalText = btn.html();

            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Linking...');

            $.ajax({
                url: "{{ route('bank.store') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        $('#linkBankModal').modal('hide');
                        toastr.success(response.message);
                        setTimeout(() => window.location.reload(), 1500);
                    }
                },
                error: function(xhr) {
                    console.log(xhr); // full object
                    console.log(xhr.responseText); // real error

                    let msg = 'Linking failed';

                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON.errors) {
                            msg = Object.values(xhr.responseJSON.errors).join(', ');
                        }
                    }

                    toastr.error(msg);
                }
            });
        });
    });
</script>
@endpush

@endsection