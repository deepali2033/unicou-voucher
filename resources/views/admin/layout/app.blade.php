<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard - UniCou</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom Admin CSS -->
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Parkinsans:wght@300..800&display=swap" rel="stylesheet">
    <!-- Toastr CSS -->


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        #toast-container>.toast {
            opacity: 1 !important;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row koa-sidebar-wrapper flex-lg-nowrap">
            <!-- Overlay -->
            <div id="overlay" class="overlay"></div>

            <!-- Sidebar -->
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-3 sidebar sidebar-hidden">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4 d-flex justify-content-between align-items-center px-3">
                        <a class="text-start" href="/">
                            <img src="/images/company_logo.png" class="dashboard-logo" alt=""> </a>
                        <button id="close-sidebar" class="btn btn-link text-dark d-lg-none">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    @php
                    $role = auth()->user()->account_type ?? null;
                    @endphp

                    <ul class="nav flex-column gap-2">

                        {{-- Dashboard (sab ke liye) --}}
                        @if($role)

                        <li class="nav-item">

                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                                href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                            </a>
                        </li>
                        @endif

                        {{-- User Management (Admin, Manager) --}}
                        @if(in_array($role, ['admin','manager']))
                        <li class="nav-item">

                            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                                href="{{ route('admin.users.management') }}">
                                <i class="fas fa-users me-2"></i> User Management
                            </a>
                        </li>

                        @endif
                        @if(in_array($role, ['admin','manager']))
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('admin.kyc.compliance')}}">
                                <i class="fas fa-id-card me-2"></i> KYC & Compliance
                            </a>

                        </li>

                        @endif


                        {{-- Wallet / Store Credit (Admin, Agent, Reseller) --}}
                        @if(in_array($role, ['admin','agent','reseller_agent']))
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('admin.wallet.index')}}">
                                <i class="fas fa-wallet me-2"></i> Wallet / Store Credit
                            </a>
                        </li>
                        @endif

                        {{-- Payments & Banking (Admin only) --}}
                        @if($role === 'admin')
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-university me-2"></i> Payments & Banking
                            </a>
                        </li>
                        @endif

                        {{-- Voucher Management (Admin, Manager) --}}
                        @if(in_array($role, ['admin','manager']))
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('admin.vouchers.control')}}">
                                <i class="fas fa-ticket-alt me-2"></i> Voucher Management
                            </a>
                        </li>
                        @endif

                        {{-- Orders & Delivery (Admin, Agent, Support) --}}
                        @if(in_array($role, ['admin','agent','support_team']))
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('admin.orders.index')}}">
                                <i class="fas fa-truck me-2"></i> Orders & Delivery
                            </a>
                        </li>
                        @endif

                        {{-- Pricing & Discounts (Admin only) --}}
                        @if($role === 'admin')
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('admin.pricing.index')}}">
                                <i class="fas fa-tags me-2"></i> Pricing & Discounts
                            </a>
                        </li>
                        @endif

                        {{-- Stock & Inventory (Admin, Manager) --}}
                        @if(in_array($role, ['admin','manager','support_team']))
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('admin.inventory.index')}}">
                                <i class="fas fa-boxes me-2"></i> Stock & Inventory
                            </a>
                        </li>
                        @endif

                        {{-- Disputes & Refunds (Admin, Support) --}}
                        @if(in_array($role, ['agent', 'manager', 'reseller_agent', 'support_team', 'student', 'admin']))
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-gavel me-2"></i> Disputes & Refunds
                            </a>
                        </li>
                        @endif

                        {{-- Reports & Analytics (Admin only) --}}
                        @if($role === 'admin')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
                                <i class="fas fa-chart-line me-2"></i> Reports & Analytics
                            </a>
                        </li>
                        @endif

                        {{-- System Control (Admin only) --}}
                        @if($role === 'admin')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.system.control') ? 'active' : '' }}" href="{{ route('admin.system.control') }}">
                                <i class="fas fa-cogs me-2"></i> System Control
                            </a>
                        </li>
                        @endif

                        {{-- Audit & Logs (Admin only) --}}
                        @if($role === 'admin')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.audit.*') ? 'active' : '' }}" href="{{ route('admin.audit.index') }}">
                                <i class="fas fa-clipboard-list me-2"></i> Audit & Logs
                            </a>
                        </li>
                        @endif

                    </ul>



                    <hr class="my-3" style="border-color: #495057;">

                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/') }}" target="_blank">
                                <i class="fas fa-external-link-alt me-2"></i>
                                View Website
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}"
                                href="{{ route('admin.notifications.index') }}">
                                <i class="fas fa-bell me-2"></i>
                                Notifications
                            </a>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('auth.logout') }}" style="margin:0;">
                                @csrf
                                <button type="submit" class="nav-link"
                                    style="background:none;border:none;width:100%;text-align:left;cursor:pointer;padding:0.5rem 1rem;">
                                    <i class="fas fa-external-link-alt me-2"></i>
                                    Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="mobile-header text-end d-lg-none">
                <a class="text-start" href="/">
                    <img src="/images/company_logo.png" class="mobile-logo" alt=""> </a>
                <button id="open-sidebar" class="btn navbtn">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-9 px-md-4 main-content">

                @include('layouts.header')

                @yield('content')

            </main>
        </div>
    </div>
    <script src="{{ asset('js/dashboard.js') }}"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- Custom JS -->
    <script>
        // Toastr Configuration
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "3000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };


        const sidebar = document.getElementById('sidebar');
        const openSidebarBtn = document.getElementById('open-sidebar');
        const closeSidebarBtn = document.getElementById('close-sidebar');
        const overlay = document.getElementById('overlay');

        openSidebarBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            sidebar.classList.remove('sidebar-hidden');
            overlay.classList.add('overlay-active');
        });

        closeSidebarBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            sidebar.classList.add('sidebar-hidden');
            overlay.classList.remove('overlay-active');
        });

        overlay?.addEventListener('click', () => {
            sidebar.classList.add('sidebar-hidden');
            overlay.classList.remove('overlay-active');
        });
    </script>
    @stack('scripts')
</body>

</html>