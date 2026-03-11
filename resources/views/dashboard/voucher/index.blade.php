@extends('layouts.master')
@section('content')
<div class="container-fluid">

    <div class="container-fluid mb-4">

        {{-- Offcanvas Filter --}}
        <div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">
            <div class="offcanvas-header border-bottom">
                <h5 class="offcanvas-title fw-bold" id="filterOffcanvasLabel">
                    <i class="fas fa-filter me-2"></i>Filter Pricing Rules
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <form id="filter-form" action="{{ route('vouchers') }}" method="GET">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Brand Name</label>
                        <select name="brand_name" class="form-select select2-filter">
                            <option value="">All Brands</option>
                            @foreach($filterOptions['brands'] as $brand)
                            <option value="{{ $brand }}" {{ request('brand_name') == $brand ? 'selected' : '' }}>
                                {{ $brand }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Voucher Variant</label>
                        <select name="voucher_variant" class="form-select select2-filter">
                            <option value="">All Variants</option>
                            @foreach($filterOptions['variants'] as $variant)
                            <option value="{{ $variant }}" {{ request('voucher_variant') == $variant ? 'selected' : '' }}>
                                {{ $variant }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Voucher Type</label>
                        <select name="voucher_type" class="form-select select2-filter">
                            <option value="">All Types</option>
                            @foreach($filterOptions['types'] as $type)
                            <option value="{{ $type }}" {{ request('voucher_type') == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-grid gap-2 pt-3 border-top">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="{{ route('vouchers') }}" class="btn btn-light">Reset All</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 fw-bold">Vouchers Table</h5>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('vouchers.export', request()->all()) }}" id="csv-export-link" class="btn btn-success btn-sm px-3 shadow-sm">
                        <i class="fas fa-file-csv me-1"></i> CSV
                    </a>
                    <button class="btn btn-outline-primary btn-sm px-3 shadow-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                </div>
            </div>

        </div>
    </div>
    <!-- Vouchers Grid -->
    <div id="table-container">
        @include('dashboard.voucher.voucher-grid')
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Handle Filter Form
            $(document).on('submit', '#filter-form', function(e) {
                handleAjaxFilter(this, e);
            });
        });
    </script>
    @endpush

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Great+Vibes&family=Montserrat:wght@400;700;800&display=swap');

        .bg-white-20 {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .tiny-text {
            font-size: 0.65rem;
            letter-spacing: 0.5px;
        }

        /* Gift Voucher Card Design */
        .gift-voucher-card {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            height: 220px;
            position: relative;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.03);
        }

        .gift-voucher-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .voucher-body {
            display: flex;
            height: 100%;
            background-image: radial-gradient(#e1e1e1 0.5px, transparent 0.5px);
            background-size: 10px 10px;
            /* Small dot pattern like in image */
        }

        .voucher-left {
            flex: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .company-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-logo-mini {
            width: 35px;
            height: 35px;
            background: #f8f9fa;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #eee;
        }

        .brand-logo-mini img {
            max-width: 80%;
            max-height: 80%;
            object-fit: contain;
        }

        .voucher-title-section {
            margin-top: 10px;
        }

        .gift-text {
            font-family: 'Great Vibes', cursive;
            color: #26b1a5;
            /* Teal from image */
            font-size: 2.5rem;
            line-height: 1;
            margin-bottom: -15px;
            margin-left: 10px;
            z-index: 2;
            position: relative;
        }

        .voucher-text {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            color: #e84c71;
            /* Pink from image */
            text-transform: uppercase;
            font-size: 2.4rem;
            letter-spacing: -1px;
            margin-bottom: 0;
        }

        .status-indicator {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 5px;
        }

        .in-stock {
            background-color: #28a745;
            box-shadow: 0 0 5px #28a745;
        }

        .out-stock {
            background-color: #dc3545;
            box-shadow: 0 0 5px #dc3545;
        }

        /* Right Tag Section */
        .voucher-right {
            width: 110px;
            background: #e84c71;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%, 15% 50%);
            /* Ribbon effect */
        }

        .voucher-right.grayscale {
            background: #6c757d;
            clip-path: none;
        }

        .value-label {
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 2px;
            opacity: 0.9;
        }

        .price-display {
            display: flex;
            align-items: flex-start;
            line-height: 1;
            margin: 5px 0;
            gap: 6px;
            flex-direction: column;
        }

        .currency-symbol {
            font-size: 1.2rem;
            font-weight: 700;
            /* margin-top: 5px; */
            width: 100%;
            text-align: center;
        }

        .price-amount {
            font-size: 2.8rem;
            font-weight: 800;
        }

        .tag-footer {
            position: absolute;
            bottom: 15px;
        }

        .order-action-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid #fff;
            color: #fff;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .order-action-btn:hover {
            background: #fff;
            color: #e84c71;
            transform: scale(1.1);
        }

        .disabled-action {
            color: rgba(255, 255, 255, 0.5);
            font-size: 1.2rem;
        }

        .price-display span {
            font-size: 16px;
        }

        .voucher-footer-info.d-flex.justify-content-between.align-items-center {
            display: grid !important;
            margin-top: 20px;
            gap: 10px;
        }

        @media (max-width: 600px) {
        .price-display {
            flex-direction: row;
        }    
        }

        @media (max-width: 576px) {
            .gift-voucher-card {
                height: auto;
            }

            .voucher-body {
                flex-direction: column;
            }

            .voucher-right {
                width: 100%;
                height: 80px;
                clip-path: none;
                flex-direction: row;
                gap: 20px;
            }

            .tag-footer {
                position: static;
            }
        }
    </style>
    @endsection