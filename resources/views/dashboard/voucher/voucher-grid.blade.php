<div class="row">
    @forelse($vouchers as $voucher)
    @php
    $user = auth()->user();
    $finalPrice = $voucher->final_price ?? ($user->isStudent() ? $voucher->student_sale_price : $voucher->agent_sale_price);
    $stock = $voucher->quantity ?? 0;
    
    $countryDisplay = 'N/A';
    if (is_array($voucher->country_region)) {
        if (in_array('all', $voucher->country_region)) {
            $countryDisplay = 'GLB';
        } elseif (count($voucher->country_region) > 1) {
            $countryDisplay = 'MULTY';
        } elseif (count($voucher->country_region) === 1) {
            $countryDisplay = $voucher->country_region[0];
        }
    } else {
        $countryDisplay = $voucher->country_region ?? 'N/A';
    }
    @endphp
    <div class="col-xl-4 col-lg-6 mb-4">
        <div class="gift-voucher-card">
            <div class="voucher-body">
                <!-- Left Content -->
                <div class="voucher-left">
                    <div class="company-info mb-2">
                        <div class="brand-logo-mini">
                            @if($voucher->logo)
                            <img src="{{ asset($voucher->logo) }}" alt="{{ $voucher->brand_name }}">
                            @else
                            <i class="fas fa-ticket-alt text-primary"></i>
                            @endif
                        </div>
                        <div class="brand-text">
                            <h6 class="mb-0 fw-bold text-uppercase">{{ $voucher->brand_name }}</h6>
                            <span class="text-muted tiny-text">SKU: {{ $voucher->sku_id }}</span>
                        </div>
                    </div>

                    <div class="voucher-title-section">
                        <h1 class="voucher-text">Voucher</h1>
                    </div>

                    <div class="voucher-footer-info d-flex justify-content-between align-items-center mt-auto">
                        <div class="stock-status">
                            @if($stock > 0)
                            <span class="status-indicator in-stock"></span>
                            <span class="small fw-bold text-success">IN STOCK ({{ $stock }})</span>
                            @else
                            <span class="status-indicator out-stock"></span>
                            <span class="small fw-bold text-danger">OUT OF STOCK</span>
                            @endif
                        </div>
                        <div class="country-info">
                            <span class="badge bg-light text-dark border-0 small">
                                <i class="fas fa-globe-americas text-primary me-1"></i> {{ $countryDisplay }}
                            </span>
                        </div>
                        @if($voucher->remaining_limit >= 0)
                        <div class="limit-info mt-1">
                            <span class="badge {{ $voucher->is_limited ? 'bg-danger' : 'bg-info' }} text-white border-0 small">
                                <i class="fas fa-clock me-1"></i> 24h Limit: {{ $voucher->is_limited ? 'Reached' : $voucher->remaining_limit . ' left' }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Right Value Tag -->
                <div class="voucher-right {{ $stock > 0 ? '' : 'grayscale' }}">
                    <div class="value-tag-content text-center">
                        <span class="value-label text-uppercase">Value</span>
                        <div class="price-display">
                            <span class="currency-symbol">{{ $voucher->currency }}</span>
                            <span class="price-amount">{{ number_format($finalPrice, 0) }}</span>
                        </div>
                    </div>
                    <div class="tag-footer">
                        @if($stock > 0)
                        @if($voucher->is_limited)
                        <div class="disabled-action" title="24h Limit Reached">
                            <i class="fas fa-history text-white-50"></i>
                        </div>
                        @elseif(auth()->user()->isSupport())
                        <div class="disabled-action" title="Support View Only">
                            <i class="fas fa-eye text-white"></i>
                        </div>
                        @else
                        <a href="{{ route('vouchers.order', $voucher->id) }}" class="order-action-btn" title="Order Now">
                            <i class="fas fa-shopping-cart"></i>
                        </a>
                        @endif
                        @else
                        <div class="disabled-action">
                            <i class="fas fa-lock"></i>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <div class="empty-state">
            <i class="fas fa-search fa-4x text-light mb-3"></i>
            <h4 class="fw-bold">No Vouchers Found</h4>
            <p class="text-muted">Try adjusting your filters to find what you're looking for.</p>
            <a href="{{ route('vouchers') }}" class="btn btn-primary btn-sm px-4 fw-bold">SHOW ALL</a>
        </div>
    </div>
    @endforelse
</div>

<div class="mt-4">
    @include('dashboard.partials.custom-pagination', ['items' => $vouchers])
</div>
