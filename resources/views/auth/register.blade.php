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

<div class="sat-view-container">
    <div class="sat-card" style="max-width: 650px;">
        <div class="sat-header">
            <h2 id="formTitle">Create Account</h2>
            <p>Join UniCou and start managing your vouchers.</p>
        </div>

        @if ($errors->any())
        <div class="alert alert-danger mx-4 mt-3 mb-0">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="type-selector-wrapper mt-4 mb-4 text-center">
            @if(!$accountLocked)
            <div class="d-inline-flex gap-2 role-switch p-2 bg-light rounded-pill">
                <button type="button"
                    class="role-btn {{ $defaultType === 'reseller_agent' ? 'active' : '' }}"
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
            <input type="hidden" id="regifeetaken" name="regifeetaken" value="no">

            <div class="sat-grid-2">
                <div class="sat-field">
                    <label for="firstName">Full Name *</label>
                    <input name="first_name" type="text" id="firstName" placeholder="Full Name" required>
                </div>

                <div class="sat-field">
                    <label for="email">Email Address *</label>
                    <input name="email" type="email" id="email" placeholder="Email Address" required>
                </div>

                <div class="sat-field sat-full">
                    <label for="phone_input">Phone Number *</label>
                    <div class="iti-wrapper">
                        <input type="tel" id="phone_input" name="phone_dummy" required style="width: 100%;">
                    </div>
                    <input type="hidden" name="phone" id="full_phone">
                    <input type="hidden" name="country_code" id="country_code">
                </div>

                <div class="sat-field">
                    <label for="password">Password *</label>
                    <input name="password" type="password" id="password" placeholder="Min. 8 characters" required>
                </div>

                <div class="sat-field">
                    <label for="confirmPassword">Confirm Password *</label>
                    <input name="password_confirmation" type="password" id="confirmPassword" placeholder="Repeat password" required>
                </div>
            </div>

            <div class="sat-field mt-4 mb-4">
                <div class="captcha-container d-flex justify-content-center">
                    <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE') }}"></div>
                </div>
            </div>

            <button type="button" class="sat-btn" id="submitBtns">
                REGISTER NOW →
            </button>
            <button hidden type="submit" id="realSubmitBtn"></button>

            <div class="text-center mt-4">
                <p style="font-size: 14px; color: #64748b;">
                    Already have an account?
                    <a href="{{ route('login') }}" class="text-primary fw-bold" style="text-decoration: none;">Login here</a>
                </p>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
    .role-switch {
        background: #f1f5f9;
        padding: 4px;
        border-radius: 50px;
        display: inline-flex;
    }

    .role-btn {
        border: none;
        padding: 8px 24px;
        border-radius: 40px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        background: transparent;
        color: #64748b;
        transition: all 0.2s ease;
    }

    .role-btn.active {
        background: #23AAE2;
        color: #fff;
        box-shadow: 0 2px 8px rgba(35, 170, 226, 0.3);
    }

    .iti-wrapper .iti {
        width: 100%;
    }
</style>
@endpush

@push('scripts')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let iti;
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.querySelector("#phone_input");
        iti = window.intlTelInput(input, {
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
            separateDialCode: true,
            initialCountry: "auto",
            geoIpLookup: function(success, failure) {
                fetch("https://ipapi.co/json").then(res => res.json()).then(data => success(data.country_code)).catch(() => success("in"));
            }
        });

        document.getElementById('submitBtns').addEventListener('click', function() {
            if (typeof grecaptcha !== 'undefined' && grecaptcha.getResponse() === "") {
                Swal.fire({
                    title: "Verification Required",
                    text: "Please complete the reCAPTCHA.",
                    icon: "warning"
                });
                return;
            }

            document.getElementById('full_phone').value = iti.getNumber();
            document.getElementById('country_code').value = iti.getSelectedCountryData().iso2.toUpperCase();

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