@extends('layouts.master')
@section('content')
<div class="container-fluid py-4">

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-primary border-4">
                <div class="card-body">
                    <div class="text-muted small mb-1">Available Store Credit </div>
                    <div class="d-flex align-items-center">
                        <h3 class="fw-bold mb-0">500</h3>
                        <span class="ms-auto text-success small fw-bold"><i class="fas fa-arrow-up me-1"></i>12%</span>
                    </div>
                </div>
            </div>
        </div>


    </div>

</div>

<style>
    .brand-logo-container {
        width: 60px;
        height: 60px;
        background: #f8f9fa;
        border-radius: 12px;
        padding: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .icon-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .bg-light-warning {
        background-color: #fff9e6;
    }

    .tiny-text {
        font-size: 0.7rem;
        letter-spacing: 0.5px;
    }

    .quantity-selector input::-webkit-outer-spin-button,
    .quantity-selector input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .progress-ring-bar {
        transition: stroke-dashoffset 0.35s;
        transform: rotate(-90deg);
        transform-origin: 50% 50%;
    }

    .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .point-toggle-card {
        transition: border-color 0.2s;
    }

    .point-toggle-card:has(.form-check-input:checked) {
        border-color: #e84c71 !important;
        background-color: #fffafb;
    }
</style>


@endsection