@extends('layouts.auth')

@section('title', 'Login - UniCou')

@section('content')
<section class="login_page_sec">
    <div class="login-wrapper">
        <div class="login-image">
            <img src="https://static.vecteezy.com/system/resources/thumbnails/003/689/228/small/online-registration-or-sign-up-login-for-account-on-smartphone-app-user-interface-with-secure-password-mobile-application-for-ui-web-banner-access-cartoon-people-illustration-vector.jpg" alt="Login Image">
        </div>
        <div class="login-form">
            <div class="auth-header mb-4">
                <h2>Welcome Back</h2>
                <p>Login to manage your vouchers and services</p>
            </div>

            @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form id="loginForm" method="POST" action="{{ route('auth.login') }}">
                @csrf
                <div class="field">
                    <label for="email">Email</label>
                    <input name="email" type="email" id="email" placeholder="Enter your email" required>
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <input name="password" type="password" id="password" placeholder="Enter your password" required>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input type="checkbox" name="remember" id="remember" value="1">
                        <label for="remember" style="display:inline; font-size: 13px;">Remember me</label>
                    </div>
                    <a href="{{ route('password.request') }}" class="link" style="margin-top: 0;">Forgot password?</a>
                </div>

                <button type="submit" class="login_btn" id="submitBtn">Login</button>

                <div class="signin-link text-center mt-4">
                    Don't have an account? <a href="{{ route('register') }}" class="link" style="display: inline;">Register</a>
                </div>
            </form>
        </div>
    </div>
</section>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<style>
    .login_page_sec {
        padding: 50px 0;
    }
</style>
@endpush

@push('scripts')
<script>
    document.getElementById('loginForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        if(btn) {
            btn.disabled = true;
            btn.textContent = 'Logging in...';
        }
    });
</script>
@endpush
@endsection
