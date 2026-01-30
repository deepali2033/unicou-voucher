<!-- <header class="main-header mb-4 d-flex justify-content-between align-items-center py-3 border-bottom">
    <div class="header-left">
        @yield('header_left')
        @if(!View::hasSection('header_left'))
        <h4 class="mb-0">
            @if(Auth::user()->isAdmin())
            Admin Panel
            @elseif(Auth::user()->account_type === 'reseller_agent')
            Agent Dashboard
            @elseif(Auth::user()->account_type === 'student')
            Student Dashboard
            @else
            Dashboard
            @endif
        </h4>
        @endif
    </div>


    <div class="header-right d-flex align-items-center gap-3">
        @if(session('api_error'))
        <div class="alert alert-warning p-1 px-2 m-0 small" style="font-size: 0.7rem;">
            <i class="fas fa-exclamation-triangle"></i> {{ session('api_error') }}
        </div>
        @endif

        @if(!Auth::user()->isAdmin())
        <div class="notification-bell">
            <i class="far fa-bell" style="font-size: 1.2rem; color: #666; cursor: pointer;"></i>
        </div>

        @php
        $countryCode = session('user_country_code', 'US');
        $countryName = session('user_country_name', 'United States');
        $flagUrl = "https://flagcdn.com/w40/".strtolower($countryCode).".png";
        @endphp

        <div class="d-flex align-items-center gap-2" title="{{ $countryName }}">
            <img src="{{ $flagUrl }}" alt="{{ $countryName }}" style="width: 30px; border-radius: 2px; border: 1px solid #eee;">
            <span class="d-none d-md-inline text-muted small fw-bold">{{ strtoupper($countryCode) }}</span>
        </div>
        @endif

        <div class="user-dropdown d-flex align-items-center gap-2">
            <img src="{{ asset('images/user.png') }}" class="user-avatar rounded-circle" width="40" height="40">
            <div class="user-info d-flex flex-column">
                <span class="user-name fw-bold" style="font-size: 0.9rem; line-height: 1;">{{ Auth::user()->name }}</span>
                <small class="user-role text-muted" style="font-size: 0.75rem;">
                    @if(Auth::user()->isAdmin())
                    Administrator
                    @elseif(Auth::user()->account_type === 'reseller_agent' || Auth::user()->account_type === 'student')
                    {{ Auth::user()->user_id }}
                    @else
                    {{ ucfirst(Auth::user()->account_type) }}
                    @endif
                </small>
            </div>

            <div class="dropdown">
                <a href="#" class="text-decoration-none text-dark" data-bs-toggle="dropdown">
                    <i class="fas fa-chevron-down" style="font-size: 0.8rem; color: #999;"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                    @if(!View::hasSection('header_left'))
                    @if(Auth::user()->isAdmin())
                    <a class="dropdown-item" href="{{ route('admin.account.manage') }}">Manage Account</a>
                    @else
                    <a class="dropdown-item" href="#">Manage Account</a>
                    @endif
                    <div class="dropdown-divider"></div>
                    @endif
                    <a class="dropdown-item" href="{{ route('auth.logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form-header').submit();">
                        Logout
                    </a>
                    <form id="logout-form-header" action="{{ route('auth.logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
</header> -->