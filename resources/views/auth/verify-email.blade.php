@extends('layouts.auth')

@section('title', 'Verify Email')

@section('content')
@include('layouts.header')

<div class="sat-view-container">
    <div class="sat-card" style="max-width: 550px;">
        <div class="sat-header text-center">
            <div class="mb-4">
                <i class="fas fa-envelope-open-text fa-3x" style="color: #23AAE2;"></i>
            </div>
            <h2>Verify Your Email</h2>
            <p>Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?</p>
        </div>

        @if (session('message'))
        <div class="alert alert-success mx-4 mt-3 mb-0" role="alert">
            {{ session('message') }}
        </div>
        @endif

        @if (session('error'))
        <div class="alert alert-danger mx-4 mt-3 mb-0" role="alert">
            {{ session('error') }}
        </div>
        @endif

        <div class="mt-4 px-4">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="sat-btn mb-3">
                    RESEND VERIFICATION EMAIL →
                </button>
            </form>

            <form method="POST" action="{{ route('auth.logout') }}" class="text-center">
                @csrf
                <button type="submit" class="btn btn-link text-muted" style="text-decoration: none; font-size: 14px;">
                    Logout
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
