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

        <ul class="nav flex-column gap-2">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('agent.dashboard') ? 'active' : '' }}" href="{{ route('agent.dashboard') }}">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('agent/vouchers*') ? 'active' : '' }}" href="{{ route('agent.vouchers') }}">
                    <i class="fas fa-ticket-alt me-2"></i> Vouchers
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('agent/bank-link*') ? 'active' : '' }}" href="{{ route('agent.banks') }}">
                    <i class="fas fa-university me-2"></i> Linked Banks
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('agent.orders.*') ? 'active' : '' }}" href="{{ route('agent.orders.history') }}">
                    <i class="fas fa-shopping-cart me-2"></i> My Orders
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-chart-pie me-2"></i> Quarterly Points
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-calendar-alt me-2"></i> Yearly Points
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-users me-2"></i> Referral Points
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-history me-2"></i> Store Credit History
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-headset me-2"></i> Customer Support Center
                </a>
            </li>
        </ul>

        <hr class="my-3" style="border-color: #495057;">

        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('home') }}" target="_blank">
                    <i class="fas fa-external-link-alt me-2"></i>
                    View Website
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('auth.logout') }}" 
                   onclick="event.preventDefault(); document.getElementById('logout-form-agent').submit();">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </a>
                <form id="logout-form-agent" action="{{ route('auth.logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>
        </ul>
    </div>
</nav>
