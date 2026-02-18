@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="sat-card">

  <div class="sat-header">

    <h2>{{ isset($vacancy) ? 'Applying for: ' . $vacancy->title : 'Support Team TERMINAL' }}</h2>
    <p>
      Establish your identity node according to global standards.
      Official processing via <b>connect@unicou.uk</b>
    </p>
  </div>
  <form id="satForm" action="{{ route('careers.submit') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="vacancy_id" value="{{ $vacancy->id }}">

    @if ($errors->any())
      <div class="alert alert-danger" style="border-radius: 10px; margin-bottom: 20px; padding: 15px;">
        <ul style="margin-bottom: 0;">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <!-- ================= BASIC INFO ================= -->
    <section class="sat-section">
      <h4><span>01</span> Basic Information</h4>

      <div class="sat-grid-2">
        <div class="sat-field">
          <label>Full Name *</label>
          <input type="text" name="name" value="{{ old('name') }}" placeholder="Enter full name as per ID" required>
          @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <div class="sat-field">
          <label>Date of Birth (as per ID) *</label>
          <input type="date" name="dob" value="{{ old('dob') }}" required>
          @error('dob') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>
      </div>
    </section>

    <!-- ================= CONTACT ================= -->
    <section class="sat-section">
      <h4><span>02</span> Contact Details</h4>

      <div class="sat-grid-2">
        <div class="sat-field">
          <label>Contact Number *</label>
          <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="+CountryCode Mobile Number" required>
          @error('phone') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <div class="sat-field">
          <label>Email (Voucher Delivery) *</label>
          <input type="email" name="email" value="{{ old('email') }}" placeholder="official@email.com" required>
          @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <div class="sat-field">
          <label>WhatsApp Number *</label>
          <input type="tel" name="whatsapp_number" value="{{ old('whatsapp_number') }}" placeholder="+CountryCode WhatsApp Number" required>
          @error('whatsapp_number') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <div class="sat-field">
          <label>Social Media Profile Link</label>
          <input type="url" name="social_link" value="{{ old('social_link') }}" placeholder="https://linkedin.com/in/username">
          @error('social_link') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>
      </div>
    </section>

    <!-- ================= ADDRESS ================= -->
    <section class="sat-section">
      <h4><span>03</span> Address Information</h4>

      <div class="sat-grid-2">
        <div class="sat-field sat-full">
          <label>Detail Address *</label>
          <input type="text" name="address" value="{{ old('address') }}" placeholder="Street, building, area" required>
          @error('address') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <div class="sat-field">
          <label>City *</label>
          <input type="text" name="city" value="{{ old('city') }}" placeholder="Enter city" required>
          @error('city') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <div class="sat-field">
          <label>State / Province *</label>
          <input type="text" name="state" value="{{ old('state') }}" placeholder="Enter state or province" required>
          @error('state') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <div class="sat-field">
          <label>Country *</label>
          <input type="text" name="country" value="{{ old('country') }}" placeholder="Enter country" required>
          @error('country') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <div class="sat-field">
          <label>Post Code *</label>
          <input type="text" name="post_code" value="{{ old('post_code') }}" placeholder="Postal / ZIP code" required>
          @error('post_code') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>
      </div>
    </section>

    <!-- ================= REFERENCE ================= -->
    <section class="sat-section">
      <h4><span>04</span> Reference Information</h4>

      <div class="sat-grid-2">
        <div class="sat-field">
          <label>Reference Name *</label>
          <input type="text" name="reference_name" value="{{ old('reference_name') }}" placeholder="Reference full name" required>
          @error('reference_name') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <div class="sat-field">
          <label>Organization Name *</label>
          <input type="text" name="organization_name" value="{{ old('organization_name') }}" placeholder="Company / Organization" required>
          @error('organization_name') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <div class="sat-field">
          <label>Reference Email *</label>
          <input type="email" name="reference_email" value="{{ old('reference_email') }}" placeholder="reference@email.com" required>
          @error('reference_email') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <div class="sat-field">
          <label>Reference Contact No *</label>
          <input type="tel" name="reference_phone" value="{{ old('reference_phone') }}" placeholder="+CountryCode Number" required>
          @error('reference_phone') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>
      </div>
    </section>

    <!-- ================= ID DETAILS ================= -->
    <section class="sat-section">
      <h4><span>05</span> Identity Details</h4>

      <div class="sat-grid-2">
        <div class="sat-field">
          <label>ID Document Type *</label>
          <select name="id_type" required>
            <option value="">Select document type</option>
            <option {{ old('id_type') == 'National ID Card' ? 'selected' : '' }}>National ID Card</option>
            <option {{ old('id_type') == 'Passport' ? 'selected' : '' }}>Passport</option>
            <option {{ old('id_type') == 'Driving License' ? 'selected' : '' }}>Driving License</option>
          </select>
          @error('id_type') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <div class="sat-field">
          <label>ID Document Number *</label>
          <input type="text" name="id_number" value="{{ old('id_number') }}" placeholder="Enter document number" required>
          @error('id_number') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <div class="sat-field sat-full">
          <label>Designation *</label>
          <select name="designation" required>
            <option value="">Select designation</option>
            <option {{ old('designation') == 'CEO' ? 'selected' : '' }}>CEO</option>
            <option {{ old('designation') == 'Managing Partner' ? 'selected' : '' }}>Managing Partner</option>
            <option {{ old('designation') == 'Partner' ? 'selected' : '' }}>Partner</option>
            <option {{ old('designation') == 'Proprietor' ? 'selected' : '' }}>Proprietor</option>
          </select>
          @error('designation') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>
      </div>
    </section>

    <!-- ================= BANK DETAILS ================= -->
    <section class="sat-section">
      <h4><span>06</span> Bank Account Details</h4>

      <div class="sat-grid-2">
        <div class="sat-field">
          <label>Bank Name *</label>
          <input type="text" name="bank_name" value="{{ old('bank_name') }}" placeholder="Bank name" required>
          @error('bank_name') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <div class="sat-field">
          <label>Bank Country *</label>
          <input type="text" name="bank_country" value="{{ old('bank_country') }}" placeholder="Country where bank is located" required>
          @error('bank_country') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <div class="sat-field sat-full">
          <label>IBAN / BSB / Account Number *</label>
          <input type="text" name="bank_account_number" value="{{ old('bank_account_number') }}" placeholder="Enter IBAN / Account number" required>
          @error('bank_account_number') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>
      </div>
    </section>

    <!-- ================= UPLOADS ================= -->
    <section class="sat-section">
      <h4><span>07</span> Document Uploads</h4>

      <div class="sat-grid-2">
        <div class="sat-field sat-full">
          <label>Upload ID Document *</label>
          <input type="file" name="id_document" required>
          @error('id_document') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <div class="sat-field sat-full">
          <label>Upload Photograph *</label>
          <input type="file" name="photograph" required>
          @error('photograph') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <div class="sat-field sat-full">
          <label>Upload Reference Letter *</label>
          <input type="file" name="reference_letter" required>
          @error('reference_letter') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>
      </div>
    </section>

    <!-- ================= CONSENT ================= -->
    <div class="sat-consent">
      <input type="checkbox" required>
      <p>
        I agree to the <b>Terms & Conditions</b> and confirm all provided information is accurate.
      </p>
    </div>

    <button class="sat-btn">
      SUBMIT REGISTRATION →
    </button>

  </form>

</div>
@endsection