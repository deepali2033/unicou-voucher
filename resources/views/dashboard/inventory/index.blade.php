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

    .btn-export {
        background: #fff;
        border: 1px solid #10b981;
        color: #10b981;
    }

    .btn-import {
        background: #fff;
        border: 1px solid #3b82f6;
        color: #3b82f6;
    }

    .btn-add-v {
        background: #3b82f6;
        border: none;
        color: #fff;
    }

    .stat-card {
        background: #fff;
        border-radius: 15px;
        padding: 1.5rem;
        height: 100%;
        border: none;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
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

    .icon-blue {
        background: #eff6ff;
        color: #3b82f6;
    }

    .icon-green {
        background: #f0fdf4;
        color: #10b981;
    }

    .icon-orange {
        background: #fff7ed;
        color: #f97316;
    }

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
            <a href="{{ route('inventory.export') }}" id="inventory-export-btn" class="btn btn-success btn-sm px-3 shadow-sm d-flex align-items-center">
                <i class="fas fa-file-csv me-1"></i> CSV
            </a>
            <button class="btn btn-outline-primary btn-sm px-3 shadow-sm d-flex align-items-center" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas">
                <i class="fas fa-filter me-1"></i> Filter
            </button>
            <button class="btn btn-primary btn-sm px-3 shadow-sm d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#importInventoryModal">
                <i class="fas fa-file-import me-1"></i> Import
            </button>
            <a href="{{ route('inventory.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm d-flex align-items-center">
                <i class="fas fa-plus me-1"></i> Add Voucher
            </a>
        </div>
    </div>

    <!-- Filters Status Area -->
    <div class="d-flex justify-content-end align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <button type="button" id="reset-filters-btn" class="btn btn-link text-danger text-decoration-none fw-bold p-0">
                <i class="fas fa-times me-1"></i> RESET
            </button>
            <span class="text-muted fw-bold small text-uppercase" style="letter-spacing: 0.5px;">
                Showing <span id="vouchers-count">{{ $inventory->total() }}</span> Vouchers
            </span>
        </div>
    </div>

    {{-- Offcanvas Filter --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold" id="filterOffcanvasLabel">
                <i class="fas fa-filter me-2"></i>Filter Inventory
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form id="inventory-filter-form">
                <div class="mb-4">
                    <label class="form-label fw-bold">Country</label>
                    <div style="max-height: 200px; overflow-y: auto;" class="border rounded p-2 bg-light">
                        @foreach($countries as $country)
                        <div class="form-check mb-1">
                            <input class="form-check-input filter-input" type="checkbox" name="countries[]" value="{{ $country }}" id="country-{{ Str::slug($country) }}">
                            <label class="form-check-label small" for="country-{{ Str::slug($country) }}">{{ $country }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Voucher Type</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input filter-input" type="checkbox" name="types[]" value="Digital" id="type-digital">
                            <label class="form-check-label" for="type-digital">Digital</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input filter-input" type="checkbox" name="types[]" value="Physical" id="type-physical">
                            <label class="form-check-label" for="type-physical">Physical</label>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Status</label>
                    @foreach(['IN STOCK', 'LOW STOCK', 'OUT OF STOCK'] as $st)
                    <div class="form-check mb-1">
                        <input class="form-check-input filter-input" type="checkbox" name="status[]" value="{{ $st }}" id="status-{{ Str::slug($st) }}">
                        <label class="form-check-label" for="status-{{ Str::slug($st) }}">{{ $st }}</label>
                    </div>
                    @endforeach
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Sort By</label>
                    <select name="sort" class="form-select filter-input" id="sort-input">
                        <option value="latest">Latest First</option>
                        <option value="oldest">Oldest First</option>
                    </select>
                </div>

                <div class="d-grid gap-2 pt-3 border-top">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <button type="button" id="clear-filters" class="btn btn-light">Reset All</button>
                </div>
            </form>
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

    <!-- List -->
    <div id="voucher-list-container">
        @include('dashboard.inventory.partials.voucher-list')
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importInventoryModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('inventory.import') }}" method="POST" enctype="multipart/form-data">
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
        function fetchVouchers(url = null) {
            let formData = $('#inventory-filter-form').serialize();
            let targetUrl = url ? url : "{{ route('inventory.index') }}?" + formData;

            $.ajax({
                url: targetUrl,
                beforeSend: function() {
                    $('#voucher-list-container').css('opacity', '0.5');
                },
                success: function(data) {
                    $('#voucher-list-container').html(data);
                    $('#voucher-list-container').css('opacity', '1');
                    
                    // Update count display
                    const totalCount = $(data).find('#total-vouchers-count').val() || $(data).filter('#total-vouchers-count').val();
                    if(totalCount) $('#vouchers-count').text(totalCount);
                    
                    window.history.pushState({}, '', targetUrl);
                }
            });
        }

        // Handle Filter Form Submit
        $('#inventory-filter-form').on('submit', function(e) {
            e.preventDefault();
            fetchVouchers();
            
            // Close offcanvas
            var offcanvasElement = document.getElementById('filterOffcanvas');
            var offcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
            if (offcanvas) offcanvas.hide();
        });

        // Trigger fetch on input change (optional, for live updates)
        $('.filter-input').on('change', function() {
            // fetchVouchers(); // Uncomment if you want live updates
        });

        $(document).on('click', '.delete-voucher-btn', function() {
            if (!confirm('Are you sure you want to delete this voucher?')) return;
            let id = $(this).data('id');
            let url = "{{ route('inventory.destroy', ':id') }}".replace(':id', id);

            $.ajax({
                url: url,
                method: 'DELETE',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    toastr.success(response.success);
                    fetchVouchers(window.location.href);
                }
            });
        });

        // Handle CSV Export with Filters
        $('#inventory-export-btn').on('click', function(e) {
            e.preventDefault();
            let formData = $('#inventory-filter-form').serialize();
            let baseUrl = "{{ route('inventory.export') }}";
            window.location.href = baseUrl + "?" + formData;
        });

        $('#clear-filters, #reset-filters-btn').on('click', function() {
            $('#inventory-filter-form')[0].reset();
            fetchVouchers("{{ route('inventory.index') }}");
            
            // Close offcanvas if it was open
            var offcanvasElement = document.getElementById('filterOffcanvas');
            var offcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
            if (offcanvas) offcanvas.hide();
        });
    });
</script>
@endpush
@endsection