<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Agent Dashboard - UniCou</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom Admin CSS -->
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Parkinsans:wght@300..800&display=swap" rel="stylesheet">
</head>

<body>
    <div class="container-fluid">
        <div class="row koa-sidebar-wrapper flex-lg-nowrap">
            <!-- Overlay -->
            <div id="overlay" class="overlay"></div>

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
                            <a class="nav-link {{ request()->is('agent/banks*') ? 'active' : '' }}" href="{{ route('agent.banks') }}">
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
                            <a class="nav-link" href="{{ url('/') }}" target="_blank">
                                <i class="fas fa-external-link-alt me-2"></i>
                                View Website
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

                <header class="main-header mb-4 d-flex justify-content-between align-items-center py-3 border-bottom">
                    <div class="header-left">
                        <!-- Space for page title or breadcrumbs if needed -->
                    </div>

                    <div class="header-right d-flex align-items-center gap-3">
                        <div class="notification-bell">
                            <i class="far fa-bell" style="font-size: 1.2rem; color: #666; cursor: pointer;"></i>
                        </div>


                        <div class="d-flex align-items-center gap-2">
                            <img src="https://flagcdn.com/w40/{{ strtolower(session('user_country_code')) }}.png">
                        </div>


                        <div class="user-dropdown d-flex align-items-center gap-2">
                            <img src="{{ asset('images/user.png') }}" class="user-avatar rounded-circle" width="40" height="40">
                            <div class="user-info d-flex flex-column">
                                <span class="user-name fw-bold" style="font-size: 0.9rem; line-height: 1;">{{ Auth::user()->name }}</span>
                                <small class="user-role">{{ Auth::user()->user_id }}</small>
                            </div>

                            <div class="dropdown">
                                <a href="#" class="text-decoration-none text-dark" data-bs-toggle="dropdown">
                                    <i class="fas fa-chevron-down" style="font-size: 0.8rem; color: #999;"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                                    <a class="dropdown-item" href="#">Manage Account</a>
                                    <div class="dropdown-divider"></div>
                                    <form method="POST" action="{{ route('auth.logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                @yield('content')

            </main>

            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script>
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
</body>

</html>