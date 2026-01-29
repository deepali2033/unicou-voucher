@extends('layouts.master')

@section('content')
<style>
    .voucher-cp-header {
        background: transparent;
        margin-bottom: 2rem;
    }
    .cp-title {
        font-size: 24px;
        font-weight: 800;
        color: #1a1a1a;
    }
    .btn-cp {
        padding: 0.6rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .btn-export { background: #fff; border: 1px solid #10b981; color: #10b981; }
    .btn-import { background: #fff; border: 1px solid #3b82f6; color: #3b82f6; }
    .btn-add-v { background: #3b82f6; border: none; color: #fff; }
    
    .stat-card {
        background: #fff;
        border-radius: 15px;
        padding: 1.5rem;
        height: 100%;
        border: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
    }
    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }
    .icon-blue { background: #eff6ff; color: #3b82f6; }
    .icon-green { background: #f0fdf4; color: #10b981; }
    .icon-orange { background: #fff7ed; color: #f97316; }

    .filter-btn {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 0.6rem 1.2rem;
        font-weight: 600;
        color: #374151;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .filter-count {
        background: #3b82f6;
        color: #fff;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        font-size: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="cp-title mb-0">Voucher Control Panel</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.inventory.export') }}" class="btn btn-cp btn-export">
                <i class="fas fa-file-csv"></i> Export CSV
            </a>
            <button class="btn btn-cp btn-import" data-bs-toggle="modal" data-bs-target="#importInventoryModal">
                <i class="fas fa-file-import"></i> Import CSV
            </button>
            <a href="{{ route('admin.inventory.create') }}" class="btn btn-cp btn-add-v">
                <i class="fas fa-plus"></i> Add Voucher
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon icon-blue">
                    <i class="fas fa-box"></i>
                </div>
                <small class="text-muted fw-bold text-uppercase" style="font-size: 11px;">Total Stock</small>
                <h3 class="fw-bold mb-0 mt-1">{{ number_format($stats['total_stock']) }}</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon icon-green">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <small class="text-muted fw-bold text-uppercase" style="font-size: 11px;">Total Valuation</small>
                <h3 class="fw-bold mb-0 mt-1">${{ number_format($stats['total_valuation'], 0) }}</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon icon-orange">
                    <i class="fas fa-tag"></i>
                </div>
                <small class="text-muted fw-bold text-uppercase" style="font-size: 11px;">Active Brands</small>
                <h3 class="fw-bold mb-0 mt-1">{{ $stats['active_brands'] }}</h3>
            </div>
        </div>
    </div>

    <!-- Filters Area -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex gap-3 align-items-center">
            <div class="dropdown">
                <button class="filter-btn dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                    <i class="fas fa-filter text-primary"></i>
                    Filters
                    <span class="filter-count" id="active-filters-count">0</span>
                </button>
                <form id="inventory-filter-form" class="dropdown-menu p-4 shadow-lg border-0" style="width: 300px; border-radius: 15px;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">Filter By</h6>
                        <button type="button" class="btn btn-link btn-sm text-decoration-none p-0" id="clear-filters">Clear All</button>
                    </div>
                    
                    <div class="mb-3">
                        <label class="small fw-bold text-uppercase text-muted mb-2">Country</label>
                        <div style="max-height: 150px; overflow-y: auto;">
                            @foreach($countries as $country)
                            <div class="form-check mb-1">
                                <input class="form-check-input filter-input" type="checkbox" name="countries[]" value="{{ $country }}" id="country-{{ Str::slug($country) }}">
                                <label class="form-check-label small" for="country-{{ Str::slug($country) }}">{{ $country }}</label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold text-uppercase text-muted mb-2">Type</label>
                        <div class="d-flex gap-2">
                            <div class="form-check">
                                <input class="form-check-input filter-input" type="checkbox" name="types[]" value="Digital" id="type-digital">
                                <label class="form-check-label small" for="type-digital">Digital</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input filter-input" type="checkbox" name="types[]" value="Physical" id="type-physical">
                                <label class="form-check-label small" for="type-physical">Physical</label>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="small fw-bold text-uppercase text-muted mb-2">Status</label>
                        @foreach(['IN STOCK', 'LOW STOCK', 'OUT OF STOCK'] as $st)
                        <div class="form-check mb-1">
                            <input class="form-check-input filter-input" type="checkbox" name="status[]" value="{{ $st }}" id="status-{{ Str::slug($st) }}">
                            <label class="form-check-label small" for="status-{{ Str::slug($st) }}">{{ $st }}</label>
                        </div>
                        @endforeach
                    </div>
                </form>
            </div>

            <div class="dropdown">
                <button class="filter-btn dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-sort-amount-down text-primary"></i>
                    Sort by: <span id="current-sort-label">Latest</span>
                </button>
                <ul class="dropdown-menu border-0 shadow" style="border-radius: 10px;">
                    <li><a class="dropdown-item sort-option active" href="#" data-sort="latest">Latest</a></li>
                    <li><a class="dropdown-item sort-option" href="#" data-sort="oldest">Oldest</a></li>
                </ul>
                <input type="hidden" name="sort" id="sort-input" form="inventory-filter-form" value="latest">
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="button" id="reset-filters" class="btn btn-link text-danger text-decoration-none fw-bold p-0">
                <i class="fas fa-times me-1"></i> RESET
            </button>
            <span class="text-muted fw-bold small text-uppercase" style="letter-spacing: 0.5px;">
                Showing <span id="vouchers-count">{{ $vouchers->total() }}</span> Vouchers
            </span>
        </div>
    </div>

    <!-- List -->
    <div id="voucher-list-container">
        @include('admin.inventory.partials.voucher-list')
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importInventoryModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.inventory.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content" style="border-radius: 20px;">
                <div class="modal-header border-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold">Import Inventory CSV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Select CSV File</label>
                        <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                        <div class="form-text mt-2">
                            <small>Please use the format provided by the Export CSV feature.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Import Now</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        function fetchVouchers() {
            let formData = $('#inventory-filter-form').serialize();
            let url = "{{ route('admin.inventory.index') }}?" + formData;
            
            $.ajax({
                url: url,
                beforeSend: function() { $('#voucher-list-container').css('opacity', '0.5'); },
                success: function(data) {
                    $('#voucher-list-container').html(data);
                    $('#voucher-list-container').css('opacity', '1');
                    $('#active-filters-count').text($('.filter-input:checked').length);
                    window.history.pushState({}, '', url);
                }
            });
        }

        $('.filter-input').on('change', fetchVouchers);

        $('.sort-option').on('click', function(e) {
            e.preventDefault();
            $('.sort-option').removeClass('active');
            $(this).addClass('active');
            $('#sort-input').val($(this).data('sort'));
            $('#current-sort-label').text($(this).text());
            fetchVouchers();
        });

        $(document).on('click', '.delete-voucher-btn', function() {
            if (!confirm('Are you sure you want to delete this voucher?')) return;
            let id = $(this).data('id');
            let url = "{{ route('admin.inventory.destroy', ':id') }}".replace(':id', id);
            
            $.ajax({
                url: url,
                method: 'DELETE',
                data: { _token: "{{ csrf_token() }}" },
                success: function(response) {
                    toastr.success(response.success);
                    fetchVouchers();
                }
            });
        });

        $('#clear-filters, #reset-filters').on('click', function() {
            $('.filter-input').prop('checked', false);
            $('#sort-input').val('latest');
            $('#current-sort-label').text('Latest');
            fetchVouchers();
        });
    });
</script>
@endpush
@endsection
