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
                <div class="d-flex flex-wrap justify-content-center gap-2">
                    @foreach(['manager', 'reseller_agent', 'support_team', 'student'] as $type)
                    <button type="button" class="btn-type {{ $defaultType === $type ? 'active' : '' }}"
                        onclick="setActive('{{ $type }}')" id="btn-{{ $type }}">
                        {{ ucfirst(str_replace('_', ' ', explode('_', $type)[0])) }}
                    </button>
                    @endforeach
                </div>
                @else
                <div class="text-center p-2 bg-light rounded border">
                    <strong>Registering as {{ ucfirst(str_replace('_', ' ', $defaultType)) }}</strong>
                </div>
                @endif
            </div>

            <form id="registerForm" method="POST" action="{{ route('auth.register') }}">
                @csrf
                <input type="hidden" name="account_type" id="accountType" value="{{ $defaultType }}">
                <input type="hidden" id="regifeetaken" name="regifeetaken" value="">

                <div class="row g-3 mb-3">
                    <div class="col-md-6 field">
                        <label for="firstName">First Name</label>
                        <input name="first_name" type="text" id="firstName" placeholder="First Name" required>
                    </div>
                    <div class="col-md-6 field">
                        <label for="lastName">Last Name</label>
                        <input name="last_name" type="text" id="lastName" placeholder="Last Name" required>
                    </div>
                </div>

                <div class="field mb-3">
                    <label for="email">Email Address</label>
                    <input name="email" type="email" id="email" placeholder="Email" required>
                </div>

                <div class="field mb-3">
                    <label for="phone_input">Phone Number</label>
                    <input type="tel" id="phone_input" name="phone_dummy" required style="width: 100%;">
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
<style>
    .iti {
        width: 100%;
    }

    .btn-type {
        background: #fff;
        border: 1px solid #ddd;
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        color: #666;
        transition: all 0.3s;
        cursor: pointer;
    }

    .btn-type.active {
        background: #23AAE2;
        color: #fff;
        border-color: #23AAE2;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
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
            document.getElementById('full_phone').value = iti.getNumber();
            document.getElementById('country_code').value = iti.getSelectedCountryData().dialCode;

            Swal.fire({
                title: "Registration Fee",
                text: "You must pay a €25 registration fee to become a verified user.",
                icon: "info",
                showCancelButton: true,
                confirmButtonText: "Pay Now!",
                cancelButtonText: "Pay Later!",
                confirmButtonColor: '#23AAE2',
                reverseButtons: true
            }).then((result) => {
                document.getElementById('regifeetaken').value = result.isConfirmed ? 'yes' : 'no';
                const btn = document.getElementById('submitBtns');
                if (btn) {
                    btn.disabled = true;
                    btn.textContent = 'Processing...';
                }
                document.getElementById('realSubmitBtn').click();
            });
        });
    });

    function setActive(type) {
        document.getElementById('accountType').value = type;
        document.querySelectorAll('.btn-type').forEach(btn => btn.classList.remove('active'));
        document.getElementById('btn-' + type).classList.add('active');
    }
</script>
@endpush
@endsection