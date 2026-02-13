@extends('layouts.auth')

@section('title', 'Register - UniCou')

@section('content')
@php
$requestedType = request()->get('type');
$lockRequested = filter_var(request()->get('locked'), FILTER_VALIDATE_BOOLEAN);
$allowedTypes = ['manager', 'reseller_agent', 'support_team', 'student'];

if ($requestedType === 'freelancer' && $lockRequested) {
$allowedTypes[] = 'freelancer';
}
$defaultType = in_array($requestedType, $allowedTypes, true) ? $requestedType : 'student';
$accountLocked = $lockRequested && $defaultType === $requestedType;
@endphp
<style>
    .role-switch {
        background: #eaf3ff;
        padding: 6px;
        border-radius: 50px;
    }

    .role-btn {
        border: none;
        padding: 10px 28px;
        border-radius: 40px;
        font-weight: 600;
        cursor: pointer;
        background: transparent;
        color: #1d4ed8;
        /* blue text */
        transition: all 0.3s ease;
    }

    .role-btn.active {
        background: #23aae2;
        /* dark blue */
        color: #fff;
        box-shadow: 0 4px 10px rgba(29, 78, 216, 0.3);
    }

    .role-btn:hover:not(.active) {
        background: #dbeafe;
        /* light blue hover */
    }
</style>
<section class="register_page_sec">
    <div class="auth-wrapper">
        <div class="auth-image">
            <img src="https://media.istockphoto.com/id/2205696704/photo/online-registration-form-identity-verification-personal-information-verification-concept.jpg?s=612x612&w=0&k=20&c=2X_45rxke9VrEez-D7JPchhSQwM_Od2jR_vyS1O5MTY=" alt="Register Illustration">
        </div>
        <div class="auth-form">
            <div class="text-center mb-4">
                <h2 class="title" id="formTitle">Create Account</h2>
                <p class="subtitle">Join UniCou and start managing your vouchers</p>
            </div>

            @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="type-selector-wrapper mb-4 text-center">
                @if(!$accountLocked)
                <div class="d-flex justify-content-center gap-2 role-switch">

                    <button type="button"
                        class="role-btn {{ $defaultType === 'agent' ? 'active' : '' }}"
                        onclick="setActive('reseller_agent')"
                        id="btn-reseller_agent">
                        Agent
                    </button>

                    <button type="button"
                        class="role-btn {{ $defaultType === 'student' ? 'active' : '' }}"
                        onclick="setActive('student')"
                        id="btn-student">
                        Student
                    </button>
                </div>

                @else
                <div class="text-center p-2 bg-light rounded border">
                    <strong>Registering as {{ ucfirst(str_replace('_', ' ', $defaultType)) }}</strong>
                </div>
                @endif
            </div>

            <form id="registerForm" method="POST" action="{{ route('register.post') }}">
                @csrf
                <input type="hidden" name="account_type" id="accountType" value="{{ $defaultType }}">
                <input type="hidden" id="regifeetaken" name="regifeetaken" value="">
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">

                <div class="row g-3 mb-3">
                    <div class="col-md-6 field">
                        <label for="firstName">Full Name</label>
                        <input name="first_name" type="text" id="firstName" placeholder="First Name" required>
                    </div>

                </div>

                <div class="field mb-3">
                    <label for="email">Email Address</label>
                    <input name="email" type="email" id="email" placeholder="Email" required>
                </div>

                <div class="field mb-3">
                    <label for="phone_input">Phone Number</label>
                    <input type="tel" id="phone_input" name="phone_dummy" class="intl-phone" data-full-field="#full_phone" required style="width: 100%;">
                    <input type="hidden" name="phone" id="full_phone">
                    <input type="hidden" name="country_code" id="country_code">
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6 field">
                        <label for="password">Password</label>
                        <input name="password" type="password" id="password" placeholder="Password" required>
                    </div>
                    <div class="col-md-6 field">
                        <label for="confirmPassword">Confirm Password</label>
                        <input name="password_confirmation" type="password" id="confirmPassword" placeholder="Confirm" required>
                    </div>
                </div>

                <div class="field mb-3">
                    <div class="captcha-wrapper p-3 border rounded bg-light d-flex align-items-center">

                        <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE') }}"></div>

                        <!-- <div class="ms-auto">
                            <img src="https://www.gstatic.com/recaptcha/api2/logo_48.png" alt="reCAPTCHA" width="30">
                        </div> -->
                    </div>
                </div>

                <button type="button" class="btn-primary w-100" id="submitBtns">
                    Register Now
                </button>
                <button hidden type="submit" id="realSubmitBtn"></button>

                <div class="bottom-text mt-4">
                    Already have an account? <a href="{{ route('login') }}">Login here</a>
                </div>
            </form>
        </div>
    </div>
</section>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />

@endpush

@push('scripts')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Geolocation tracking
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                document.getElementById('latitude').value = position.coords.latitude;
                document.getElementById('longitude').value = position.coords.longitude;
            }, function(error) {
                console.error("Error obtaining location", error);
            });
        }

        document.getElementById('submitBtns').addEventListener('click', function() {
            if (grecaptcha.getResponse() === "") {
                Swal.fire({
                    title: "Verification Required",
                    text: "Please complete the reCAPTCHA.",
                    icon: "warning"
                });
                return;
            }

            // Get phone from intl-tel-input instance attached by dashboard.js
            const phoneInput = document.querySelector("#phone_input");
            if (phoneInput && phoneInput.iti) {
                document.getElementById('full_phone').value = phoneInput.iti.getNumber();
                document.getElementById('country_code').value = phoneInput.iti.getSelectedCountryData().iso2.toUpperCase();
            }

            document.getElementById('regifeetaken').value = 'no';
            const btn = document.getElementById('submitBtns');
            if (btn) {
                btn.disabled = true;
                btn.textContent = 'Processing...';
            }
            document.getElementById('realSubmitBtn').click();
        });
    });

    function setActive(type) {
        document.getElementById('accountType').value = type;
        document.querySelectorAll('.role-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        const activeBtn = document.getElementById('btn-' + type);
        if (activeBtn) {
            activeBtn.classList.add('active');
        }
    }
</script>
@endpush
@endsection