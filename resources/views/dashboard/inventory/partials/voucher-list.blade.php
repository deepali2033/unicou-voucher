<style>
    .v-card {
        background: #fff;
        border-radius: 20px;
        padding: 1.5rem;
        border: none;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .v-card:hover {
        transform: translateY(-5px);
    }

    .v-logo {
        width: 80px;
        height: 80px;
        object-fit: contain;
        margin-right: 1.5rem;
    }

    .v-title {
        font-size: 16px;
        font-weight: 700;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .v-price {
        font-size: 20px;
        font-weight: 800;
        color: #1a1a1a;
    }

    .v-old-price {
        font-size: 14px;
        color: #ef4444;
        text-decoration: line-through;
        margin-left: 0.5rem;
    }

    .v-btn-edit {
        background: #3b82f6;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 0.5rem 1.5rem;
        font-weight: 600;
        flex: 1;
    }

    .v-btn-edit:hover {
        color: #fff;
        background: #2563eb;
    }

    .v-btn-del {
        background: transparent;
        border: 1px solid #ef4444;
        color: #ef4444;
        border-radius: 8px;
        width: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .v-btn-del:hover {
        background: #fee2e2;
        color: #ef4444;
    }

    .v-footer {
        margin-top: auto;
        padding-top: 1rem;
        border-top: 1px solid #f3f4f6;
        display: flex;
        justify-content: space-between;
        font-size: 13px;
        font-weight: 600;
        color: #6b7280;
    }
</style>

<input type="hidden" id="total-vouchers-count" value="{{ $inventory->total() }}">
<div class="row g-4">
    @forelse($inventory as $v)
    <div class="col-md-6 col-lg-4">
        <div class="v-card">
            <div class="d-flex mb-3">
                @if($v->logo)
                <img src="{{ $v->logo }}" class="v-logo" alt="Logo">
                @else
                <div class="v-logo d-flex flex-column align-items-center justify-content-center bg-light text-muted" style="border-radius: 10px;">
                    <i class="fas fa-image fa-2x mb-1"></i>
                    <small style="font-size: 10px;">No Logo</small>
                </div>
                @endif
                <div class="flex-grow-1">
                    <h5 class="v-title">{{ $v->brand_name }}</h5>
                    <div class="d-flex align-items-baseline mb-3">
                        <span class="v-price">{{ $v->currency }} {{ number_format($v->agent_sale_price) }}</span>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('inventory.edit', $v->id) }}" class="v-btn-edit text-center text-decoration-none">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <button type="button" class="v-btn-del delete-voucher-btn" data-id="{{ $v->id }}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="v-footer">
                <div class="d-flex align-items-center">
                    <span class="me-1">📦</span>
                    Stock: {{ number_format($v->quantity) }}
                </div>
                <div class="d-flex align-items-center">
                    <span class="me-1">🌍</span>
                    {{ $v->country_region }}
                </div>
            </div>
            <div class="v-footer" style="border-top: none; padding-top: 5px;">
                <div class="d-flex align-items-center">
                    <span class="me-1">🎟️</span>
                    Type: {{ $v->voucher_type }}
                </div>
                <div class="d-flex align-items-center">
                    <span class="me-1">🏷️</span>
                    SKU: {{ $v->sku_id }}
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <h5 class="text-muted">No vouchers found</h5>
    </div>
    @endforelse
</div>

<div class="mt-4 ajax-pagination">
    {{ $inventory->links() }}
</div>