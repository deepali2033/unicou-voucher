@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <h4 class="mb-0 fw-bold">Add New User</h4>
            <a href="{{ route('users.management') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Full Name</label>
                                <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" required>
                                @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email Address</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <!-- <div class="col-md-6">
                                <label class="form-label fw-semibold">Last Name</label>
                                <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" required>
                                @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div> -->
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone Number</label>
                                <input type="text" id="phone" name="phone_dummy" class="form-control intl-phone @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                                <input type="hidden" name="phone" id="full_phone">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Country</label>
                                <select name="country" id="country" class="form-select @error('country') is-invalid @enderror" required>
                                    <option value="" selected disabled>Select Country</option>
                                    @foreach(\App\Helpers\CountryHelper::getAllCountries() as $code => $name)
                                    <option value="{{ $name }}" {{ old('country') == $name ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Account Type</label>
                                <select name="account_type" class="form-select @error('account_type') is-invalid @enderror" required>
                                    @if(auth()->user()->account_type === 'reseller_agent')
                                    <option value="agent" selected>Agent</option>
                                    @else
                                    <option value="" selected disabled>Select Role</option>
                                    <option value="manager" {{ (old('account_type') == 'manager' || request('role') == 'manager') ? 'selected' : '' }}>Manager</option>
                                    <option value="reseller_agent" {{ (old('account_type') == 'reseller_agent' || request('role') == 'reseller_agent') ? 'selected' : '' }}>Reseller Agent</option>
                                    <option value="support_team" {{ (old('account_type') == 'support_team' || request('role') == 'support_team') ? 'selected' : '' }}>Support Team</option>
                                    <option value="student" {{ (old('account_type') == 'student' || request('role') == 'student') ? 'selected' : '' }}>Student</option>
                                    <option value="agent" {{ (old('account_type') == 'agent' || request('role') == 'agent') ? 'selected' : '' }}>Agent</option>
                                    @endif
                                </select>
                                @error('account_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>



                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Password</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-1"></i> Create User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection