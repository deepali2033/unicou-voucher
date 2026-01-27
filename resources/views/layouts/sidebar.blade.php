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

            {{-- KYC & Compliance (Admin, Support) --}}
            @if(in_array($role, ['admin','support_team']))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.kyc.compliance') ? 'active' : '' }}" 
                   href="{{ route('admin.kyc.compliance') }}">
                    <i class="fas fa-id-card me-2"></i> KYC & Compliance
                </a>
            </li>
            @endif

            {{-- Wallet / Store Credit (Admin, Agent, Reseller) --}}
            @if(in_array($role, ['admin','agent','reseller_agent']))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.wallet.*') ? 'active' : '' }}" 
                   href="{{ route('admin.wallet.index') }}">
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
                <a class="nav-link" href="#">
                    <i class="fas fa-ticket-alt me-2"></i> Voucher Management
                </a>
            </li>
            @endif

            {{-- Orders & Delivery (Admin, Agent, Support) --}}
            @if(in_array($role, ['admin','agent','support_team']))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" 
                   href="{{ route('admin.orders.index') }}">
                    <i class="fas fa-truck me-2"></i> Orders & Delivery
                </a>
            </li>
            @endif

            {{-- Pricing & Discounts (Admin only) --}}
            @if($role === 'admin')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.pricing.*') ? 'active' : '' }}" 
                   href="{{ route('admin.pricing.index') }}">
                    <i class="fas fa-tags me-2"></i> Pricing & Discounts
                </a>
            </li>
            @endif

            {{-- Stock & Inventory (Admin, Manager) --}}
            @if(in_array($role, ['admin','manager','support_team']))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}" 
                   href="{{ route('admin.inventory.index') }}">
                    <i class="fas fa-boxes me-2"></i> Stock & Inventory
                </a>
            </li>
            @endif

            {{-- Disputes & Refunds (Admin, Support) --}}
            @if(in_array($role, ['agent,manager,reseller_agent,support_team,student,admin']))
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-gavel me-2"></i> Disputes & Refunds
                </a>
            </li>
            @endif

            {{-- Reports & Analytics (Admin only) --}}
            @if(in_array($role, ['agent,manager,reseller_agent,support_team,student,admin']))
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-chart-line me-2"></i> Reports & Analytics
                </a>
            </li>
            @endif

            {{-- System Control (Admin only) --}}
            @if(in_array($role, ['admin','manager']))
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-cogs me-2"></i> System Control
                </a>
            </li>
            @endif

            {{-- Audit & Logs (Admin only) --}}
            @if(in_array($role, ['admin','manager']))

            <li class="nav-item">
                <a class="nav-link" href="#">
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