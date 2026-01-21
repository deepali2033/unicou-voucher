<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @stack('styles')
</head>

<body class="{{ session()->has('admin_id') ? 'impersonating' : '' }}">
    @include('layouts.header')

    @if(session()->has('admin_id'))
    <div class="impersonation-banner">
        <span>🕵️ You are currently logged in as <strong>{{ Auth::user()->name }}</strong> ({{ ucfirst(Auth::user()->role) }})</span>
        <form action="{{ route('admin.users.stop-impersonation') }}" method="POST" style="margin: 0;">
            @csrf
            <button type="submit" class="stop-impersonation-btn">🔙 Return to Admin</button>
        </form>
    </div>
    @endif

    <div class="main-wrapper">
        @include('layouts.sidebar')

        <div class="content-wrapper">
            <div class="container">
                @yield('content')
            </div>
        </div>
    </div>

    @include('layouts.footer')

    <script>
        @yield('custom-js')
    </script>
</body>

</html>