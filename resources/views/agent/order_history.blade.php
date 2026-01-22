@extends('agent.layout.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold">Order History</h2>
            <p class="text-muted">Track and manage your recent orders.</p>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Filters & Reports</h5>
            <form action="{{ route('agent.orders.history') }}" method="GET">
                <div class="row g-3">
                    <!-- Date Range -->
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">From Date</label>
                        <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">To Date</label>
                        <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                    </div>

                    <!-- Order ID Range -->
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">From Order ID</label>
                        <input type="text" name="from_order_id" class="form-control" placeholder="e.g. ORD-001" value="{{ request('from_order_id') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">To Order ID</label>
                        <input type="text" name="to_order_id" class="form-control" placeholder="e.g. ORD-100" value="{{ request('to_order_id') }}">
                    </div>

                    <!-- Voucher Type -->
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Voucher Type</label>
                        <select name="voucher_type" class="form-select">
                            <option value="">All Types</option>
                            <option value="exam" {{ request('voucher_type') == 'exam' ? 'selected' : '' }}>Exam Voucher</option>
                            <option value="discount" {{ request('voucher_type') == 'discount' ? 'selected' : '' }}>Discount Coupon</option>
                        </select>
                    </div>

                    <!-- Bank Wise -->
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Bank Wise</label>
                        <input type="text" name="bank" class="form-control" placeholder="Search Bank" value="{{ request('bank') }}">
                    </div>

                    <!-- Additional Reports for Reseller Agent -->
                    @if(Auth::user()->account_type == 'reseller_agent')
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Sub Agent</label>
                        <select name="sub_agent" class="form-select">
                            <option value="">All Sub Agents</option>
                            @foreach($subAgents as $sa)
                            <option value="{{ $sa->id }}" {{ request('sub_agent') == $sa->id ? 'selected' : '' }}>{{ $sa->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                    </div>
                </div>

                <div class="row mt-3 g-2">
                    <div class="col-auto">
                        <a href="{{ route('agent.orders.history') }}" class="btn btn-outline-secondary btn-sm">Clear All</a>
                    </div>
                    <div class="col-auto">
                        <button type="submit" name="report" value="payments" class="btn btn-info btn-sm text-white">Payments History</button>
                    </div>
                    <div class="col-auto">
                        <button type="submit" name="report" value="points" class="btn btn-warning btn-sm">Points Report</button>
                    </div>
                    <div class="col-auto">
                        <button type="submit" name="report" value="bonuses" class="btn btn-success btn-sm">Bonuses Report</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Stats (Points/Bonuses) -->
    <!-- <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3" style="border-radius: 12px; border-left: 4px solid #ffc107;">
                <p class="small text-muted mb-1">Points Earned</p>
                <h4 class="fw-bold mb-0">{{ number_format($stats['points_earned']) }}</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3" style="border-radius: 12px; border-left: 4px solid #198754;">
                <p class="small text-muted mb-1">Points Redeemed</p>
                <h4 class="fw-bold mb-0">{{ number_format($stats['points_redeemed']) }}</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3" style="border-radius: 12px; border-left: 4px solid #0dcaf0;">
                <p class="small text-muted mb-1">Current Bonus</p>
                <h4 class="fw-bold mb-0">PKR {{ number_format($stats['current_bonus']) }}</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-primary text-white" style="border-radius: 12px;">
                <p class="small mb-1 opacity-75">Next Bonus Alert</p>
                <p class="small mb-0">
                    @php
                        $totalOrders = $orders->total();
                        $target = 100; // Example target
                        $remaining = $target - ($totalOrders % $target);
                    @endphp
                    Target: {{ $remaining }} orders more
                </p>
            </div>
        </div>
    </div> -->

    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">Order ID</th>
                            <th class="py-3">Date</th>
                            <th class="py-3">Voucher Type</th>
                            <th class="py-3">Client Detail</th>
                            <th class="py-3">Amount</th>
                            <th class="py-3">Status</th>
                            <th class="px-4 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td class="px-4 py-3 text-muted">#{{ $order->order_id }}</td>
                            <td class="py-3">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="py-3">{{ $order->voucher_type }}</td>
                            <td class="py-3">
                                <div class="small fw-bold">{{ $order->client_name }}</div>
                                <div class="small text-muted">{{ $order->client_email }}</div>
                            </td>
                            <td class="py-3 fw-bold">PKR {{ number_format($order->amount) }}</td>
                            <td class="py-3">
                                @if($order->status == 'completed')
                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">Completed</span>
                                @elseif($order->status == 'pending')
                                <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2">Pending</span>
                                @else
                                <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2">{{ ucfirst($order->status) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#orderModal{{ $order->id }}">Invoice</button>

                                <!-- Invoice Modal -->
                                <div class="modal fade" id="orderModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header border-0">
                                                <h5 class="modal-title fw-bold">Order Invoice #{{ $order->order_id }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3 d-flex justify-content-between">
                                                    <span class="text-muted">Order Date:</span>
                                                    <span class="fw-bold">{{ $order->created_at->format('d M Y, h:i A') }}</span>
                                                </div>
                                                <hr>
                                                <div class="mb-3">
                                                    <h6 class="fw-bold">Client Information</h6>
                                                    <p class="mb-1">Name: {{ $order->client_name }}</p>
                                                    <p class="mb-1">Email: {{ $order->client_email }}</p>
                                                </div>
                                                <div class="mb-3">
                                                    <h6 class="fw-bold">Order Summary</h6>
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span>Voucher Type:</span>
                                                        <span>{{ $order->voucher_type }}</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span>Payment Method:</span>
                                                        <span>{{ $order->payment_method ?? 'N/A' }}</span>
                                                    </div>
                                                    @if($order->bank_name)
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span>Bank:</span>
                                                        <span>{{ $order->bank_name }}</span>
                                                    </div>
                                                    @endif
                                                </div>
                                                <div class="bg-light p-3 rounded d-flex justify-content-between">
                                                    <span class="fw-bold">Total Amount:</span>
                                                    <span class="fw-bold text-primary">PKR {{ number_format($order->amount) }}</span>
                                                </div>
                                                <div class="mt-3 small text-muted">
                                                    Points Earned: {{ $order->points_earned }}
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="button" class="btn btn-primary" onclick="window.print()">Print Invoice</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No orders found matching your criteria.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($orders->hasPages())
            <div class="p-4 border-top">
                {{ $orders->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection