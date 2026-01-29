@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Voucher Control Panel</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.vouchers.export') }}" class="btn btn-outline-success">
                <i class="fas fa-file-csv me-1"></i> Export CSV
            </a>
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fas fa-file-import me-1"></i> Import CSV
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVoucherModal">
                <i class="fas fa-plus me-1"></i> Add Voucher
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        @forelse($vouchers as $voucher)
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px; border: 1px solid #eee !important;">
                <div class="card-body p-3">
                    <div class="row align-items-start">
                        <!-- Logo Section (Left) -->
                        <div class="col-5 text-center border-end" style="min-height: 100px; display: flex; align-items: center; justify-content: center;">
                            @if($voucher->logo)
                            <img src="{{ asset('images/logos/' . $voucher->logo) }}" alt="{{ $voucher->name }}" style="max-width: 100%; max-height: 70px; object-fit: contain;">
                            @else
                            <div class="text-muted small">
                                <i class="fas fa-image fa-3x opacity-25"></i><br>
                                No Logo
                            </div>
                            @endif
                        </div>

                        <!-- Details Section (Right) -->
                        <div class="col-7 ps-3">
                            <h6 class="fw-bold mb-2 text-dark" style="font-size: 0.85rem; height: 38px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                {{ $voucher->name }}
                            </h6>

                            <div class="mb-2">
                                <span class="fw-bold text-dark" style="font-size: 1rem;">PKR {{ number_format($voucher->price) }}</span>
                                @if($voucher->original_price)
                                <span class="text-danger text-decoration-line-through ms-1" style="font-size: 0.75rem;">PKR {{ number_format($voucher->original_price) }}</span>
                                @endif
                            </div>

                            <div class="d-flex gap-1">
                                <button class="btn btn-sm text-white fw-bold px-2 py-2 flex-fill" style="background: #d63384; border-radius: 10px; font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#editVoucherModal{{ $voucher->id }}">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </button>
                                <form action="{{ route('admin.vouchers.destroy', $voucher->id) }}" method="POST" class="flex-fill d-grid">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-2" style="border-radius: 10px; font-size: 0.75rem;" onclick="return confirm('Delete this voucher?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <hr class="my-3 opacity-10">

                    <!-- Points Section -->
                    <div class="d-flex justify-content-between align-items-center px-1">
                        <div class="d-flex align-items-center gap-1">
                            <img src="https://cdn-icons-png.flaticon.com/512/2830/2830305.png" width="14" alt="silver">
                            <span style="font-size: 0.7rem; color: #888;">{{ $voucher->quarterly_points ?? 0 }} Quarterly</span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <img src="https://cdn-icons-png.flaticon.com/512/2830/2830312.png" width="14" alt="gold">
                            <span style="font-size: 0.7rem; color: #888;">{{ $voucher->yearly_points ?? 0 }} Yearly</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="editVoucherModal{{ $voucher->id }}" tabindex="-1">
            <div class="modal-dialog">
                <form action="{{ route('admin.vouchers.update', $voucher->id) }}" method="POST">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Voucher: {{ $voucher->voucher_id }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $voucher->name }}" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Category</label>
                                    <input type="text" name="category" class="form-control" value="{{ $voucher->category }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="active" {{ $voucher->status == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ $voucher->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Price (PKR)</label>
                                    <input type="number" step="0.01" name="price" class="form-control" value="{{ $voucher->price }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Original Price (PKR)</label>
                                    <input type="number" step="0.01" name="original_price" class="form-control" value="{{ $voucher->original_price }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Quarterly Points</label>
                                    <input type="number" name="quarterly_points" class="form-control" value="{{ $voucher->quarterly_points ?? 0 }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Yearly Points</label>
                                    <input type="number" name="yearly_points" class="form-control" value="{{ $voucher->yearly_points ?? 0 }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Update Stock</label>
                                    <input type="number" name="stock" class="form-control" value="{{ $voucher->stock }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="text-muted">
                <i class="fas fa-ticket-alt fa-3x mb-3"></i>
                <p>No vouchers in catalog. Add your first voucher!</p>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Add Voucher Modal -->
<div class="modal fade" id="addVoucherModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.vouchers.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Voucher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Voucher Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Premium Access" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category</label>
                            <input type="text" name="category" class="form-control" placeholder="e.g. Education" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Initial Stock</label>
                            <input type="number" name="stock" class="form-control" value="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price (₹)</label>
                        <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Voucher</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.vouchers.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Vouchers from CSV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select CSV File</label>
                        <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                        <div class="form-text mt-2 small text-muted">
                            Format: VoucherID, Name, Category, Price, Stock, Status
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Upload & Import</button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .bg-info-subtle {
        background-color: #e0f7fa;
    }

    .bg-success-subtle {
        background-color: #e8f5e9;
    }

    .bg-danger-subtle {
        background-color: #ffebee;
    }
</style>
@endsection