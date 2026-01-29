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
                <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}" href="{{ route('student.dashboard') }}">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('student.vouchers.*') ? 'active' : '' }}" href="#">
                    <i class="fas fa-ticket-alt me-2"></i> My Vouchers
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-plus-circle me-2"></i> Redeem Voucher
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-shopping-cart me-2"></i> My Orders
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-headset me-2"></i> Support Center
                </a>
            </li>
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
                <a class="nav-link" href="{{ route('auth.logout') }}" 
                   onclick="event.preventDefault(); document.getElementById('logout-form-student').submit();">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </a>
                <form id="logout-form-student" action="{{ route('auth.logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>
        </ul>
    </div>
</nav>
