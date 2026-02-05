@extends('layouts.auth')

@section('title', 'Login')

@section('content')


<div class="sat-view-container">
    <div class="sat-card" style="max-width: 500px;">
        <div class="sat-header">
            <h2>Login</h2>
            <p>Sign in to your account to continue.</p>
        </div>

        @if(session('success'))
        <div class="alert alert-success mx-4 mt-3 mb-0">
            {{ session('success') }}
        </div>
        @endif

        @if ($errors->any())
        <div class="alert alert-danger mx-4 mt-3 mb-0">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" class="mt-4">
            @csrf

            <div class="sat-field mb-3">
                <label>Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Enter email" required>
            </div>

            <div class="sat-field mb-3">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter password" required>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember" style="font-size: 13px; color: #64748b;">
                        Remember Me
                    </label>
                </div>
                <a href="javascript:void(0)" class="text-primary" style="font-size: 13px; text-decoration: none;">Forgot Password?</a>
            </div>

            <button type="submit" class="sat-btn">
                LOGIN →
            </button>

            <div class="text-center mt-4">
                <p style="font-size: 14px; color: #64748b;">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="text-primary fw-bold" style="text-decoration: none;">Create new account</a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection