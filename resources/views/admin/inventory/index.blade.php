@extends('admin.layout.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold"><i class="fas fa-boxes me-2"></i> Stock & Inventory</h4>
            <p class="text-muted">Monitor stock levels, upload new voucher codes, and set alerts.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 15px;">
                <h5 class="fw-bold mb-4">Upload New Stock</h5>
                <form action="{{ route('admin.inventory.upload') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Voucher</label>
                        <select name="voucher_id" class="form-select" required>
                            @foreach($vouchers as $v)
                            <option value="{{ $v->voucher_id }}">{{ $v->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Voucher Codes (one per line)</label>
                        <textarea name="codes" class="form-control" rows="8" placeholder="Enter codes here..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Upload Codes</button>
                </form>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">Stock Levels & Alerts</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Voucher</th>
                                    <th>Total Uploaded</th>
                                    <th>Used</th>
                                    <th>Available</th>
                                    <th class="text-end pe-4">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($inventory as $item)
                                @php 
                                    $available = $item->total - $item->used;
                                    $statusClass = $available < 10 ? 'bg-danger' : ($available < 50 ? 'bg-warning' : 'bg-success');
                                @endphp
                                <tr>
                                    <td class="ps-4 fw-bold">
                                        @php $v = $vouchers->firstWhere('voucher_id', $item->voucher_id); @endphp
                                        {{ $v ? $v->name : $item->voucher_id }}
                                    </td>
                                    <td>{{ $item->total }}</td>
                                    <td class="text-muted">{{ $item->used }}</td>
                                    <td class="fw-bold fs-5">{{ $available }}</td>
                                    <td class="text-end pe-4">
                                        <span class="badge {{ $statusClass }}">
                                            {{ $available < 10 ? 'LOW STOCK' : 'IN STOCK' }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">No inventory data available.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
