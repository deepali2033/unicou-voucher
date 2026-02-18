<header class="main-header mb-4 d-flex justify-content-between align-items-center py-3 border-bottom">
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
        @if(session()->has('impersonator_id'))
        <div class="impersonation-notice me-2">
            <a href="{{ route('users.stop-impersonating') }}" class="btn btn-danger btn-sm fw-bold">
                <i class="fas fa-user-shield me-1"></i> Back to Admin
            </a>
        </div>
        @endif

        @if(session('api_error'))
        <div class="alert alert-warning p-1 px-2 m-0 small" style="font-size: 0.7rem;">
            <i class="fas fa-exclamation-triangle"></i> {{ session('api_error') }}
        </div>
        @endif

        <div class="notification-bell position-relative">
            <a href="{{ route('notifications.index') }}" class="text-decoration-none">
                <i class="far fa-bell" style="font-size: 1.2rem; color: #666; cursor: pointer;"></i>
                @php
                $unreadCount = auth()->user()->unreadNotifications->count();
                @endphp
                @if($unreadCount > 0)
                <span id="notification-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem; padding: 0.25em 0.4em;">
                    {{ $unreadCount }}
                    <span class="visually-hidden">unread notifications</span>
                </span>
                @endif
            </a>
        </div>
        @php
        use App\Helpers\LocationHelper;

        if (!session()->has('geo')) {
        LocationHelper::geo();
        }
        @endphp

        @php
        $countryCode = session('geo.country_code', 'US');
        $countryName = session('geo.country_name', 'United States');
        $flagUrl = "https://flagcdn.com/w40/".strtolower($countryCode).".png";
        @endphp



        <div class="d-flex align-items-center gap-2" title="{{ $countryName }}" id="header-country-container">
            <img id="header-country-flag" src="{{ $flagUrl }}" alt="{{ $countryName }}" style="width:30px;border-radius:2px;border:1px solid #eee;">
            <span id="header-country-code" class="d-none d-md-inline text-muted small fw-bold">{{ $countryCode }}</span>
        </div>


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

                    <a class="dropdown-item" href="{{ route('account.manage') }}">Manage Account</a>

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
</header>

<script>
let audioContext;
let lastCount = {{ $unreadCount ?? 0 }};

function getAudioContext() {
    if (!audioContext) {
        audioContext = new (window.AudioContext || window.webkitAudioContext)();
    }
    return audioContext;
}

document.addEventListener('click', function initAudio() {
    getAudioContext().resume();
    document.removeEventListener('click', initAudio);
});

function playEmailNotificationSound() {
    const ctx = getAudioContext();

    for (let i = 0; i < 3; i++) {
        setTimeout(() => {
            const oscillator = ctx.createOscillator();
            const gainNode = ctx.createGain();

            oscillator.type = 'sine';
            oscillator.frequency.value = 880 + (i * 220);

            oscillator.connect(gainNode);
            gainNode.connect(ctx.destination);

            const now = ctx.currentTime;

            gainNode.gain.setValueAtTime(0, now);
            gainNode.gain.linearRampToValueAtTime(0.4, now + 0.01);
            gainNode.gain.linearRampToValueAtTime(0, now + 0.1);

            oscillator.start(now);
            oscillator.stop(now + 0.11);
        }, i * 100);
    }
}

setInterval(() => {
    fetch("{{ route('notifications.unreadCount') }}")
        .then(res => res.json())
        .then(data => {

            if (data.count > lastCount) {
                playEmailNotificationSound();
            }

            lastCount = data.count;

            let badge = document.getElementById('notification-count');
            if (badge) {
                badge.innerText = data.count;
                badge.style.display = data.count > 0 ? 'inline-block' : 'none';
            }
        });
}, 5000);
</script>
