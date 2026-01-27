@extends('admin.layout.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold">Wallet / Store Credit Management</h4>
            <p class="text-muted">Manage user balances, manual adjustments, and view transaction history.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger border-0 shadow-sm mb-4">{{ session('error') }}</div>
    @endif

    <!-- Quick Stats & Actions -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 text-center h-100" style="border-radius: 15px;">
                <h6 class="text-muted mb-2">Total System Balance</h6>
                <h2 class="fw-bold mb-0 text-primary">PKR {{ number_format($users->sum('wallet_balance')) }}</h2>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 15px;">
                <div class="d-flex flex-wrap gap-3 justify-content-center align-items-center h-100">
                    <button class="btn btn-success px-4" data-bs-toggle="modal" data-bs-target="#creditModal">
                        <i class="fas fa-plus-circle me-2"></i> Credit Wallet (Manual)
                    </button>
                    <button class="btn btn-danger px-4" data-bs-toggle="modal" data-bs-target="#debitModal">
                        <i class="fas fa-minus-circle me-2"></i> Debit Wallet (Manual)
                    </button>
                    <button class="btn btn-outline-primary px-4">
                        <i class="fas fa-plug me-2"></i> Auto Credit (Webhook)
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Ledger & User Table -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">Recent Wallet Ledger</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Date</th>
                                    <th>User</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ledger as $entry)
                                <tr>
                                    <td class="ps-4 small text-muted">{{ \Carbon\Carbon::parse($entry->created_at)->format('d M Y, h:i A') }}</td>
                                    <td>
                                        @php $ledgerUser = $users->firstWhere('id', $entry->user_id); @endphp
                                        <div class="fw-bold">{{ $ledgerUser ? $ledgerUser->name : 'Unknown' }}</div>
                                        <div class="small text-muted">{{ $ledgerUser ? $ledgerUser->user_id : 'ID N/A' }}</div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $entry->type == 'credit' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} px-3">
                                            {{ strtoupper($entry->type) }}
                                        </span>
                                    </td>
                                    <td class="fw-bold {{ $entry->type == 'credit' ? 'text-success' : 'text-danger' }}">
                                        {{ $entry->type == 'credit' ? '+' : '-' }} PKR {{ number_format($entry->amount) }}
                                    </td>
                                    <td class="small">{{ $entry->description }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">No recent ledger entries found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- 
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">User Balances</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($users->take(10) as $user)
                        <div class="list-group-item border-0 px-4 py-3 d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold small">{{ $user->name }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">{{ $user->user_id }}</div>
                            </div>
                            <div class="fw-bold text-dark">PKR {{ number_format($user->wallet_balance) }}</div>
                        </div>
                        @endforeach
                    </div>
                    <div class="p-3 text-center border-top">
                        <a href="{{ route('admin.users.management') }}" class="btn btn-sm btn-link text-primary text-decoration-none">View All Users</a>
                    </div>
                </div>
            </div>
        </div> -->
    </div>
</div>

<!-- Credit Modal -->
<div class="modal fade" id="creditModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.wallet.credit') }}" method="POST">
            @csrf
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Manual Wallet Credit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select User</label>
                        <select name="user_id" class="form-select" required>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->user_id }} - Balance: {{ number_format($user->wallet_balance) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount (PKR)</label>
                        <input type="number" step="0.01" name="amount" class="form-control" placeholder="0.00" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Note/Reason</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="e.g. Refund, Bonus, etc."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success px-4">Credit Wallet</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Debit Modal -->
<div class="modal fade" id="debitModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.wallet.debit') }}" method="POST">
            @csrf
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Manual Wallet Debit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select User</label>
                        <select name="user_id" class="form-select" required>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->user_id }} - Balance: {{ number_format($user->wallet_balance) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount (PKR)</label>
                        <input type="number" step="0.01" name="amount" class="form-control" placeholder="0.00" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Note/Reason</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="e.g. Penalty, Adjustment, etc."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger px-4">Debit Wallet</button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .bg-success-subtle {
        background-color: #e8f5e9;
    }

    .bg-danger-subtle {
        background-color: #ffebee;
    }
</style>
@endsection