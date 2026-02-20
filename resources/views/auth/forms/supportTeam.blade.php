<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Support Team TERMINAL</title>
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="sat-view-container">
  <div class="sat-card">

    <div class="sat-header">
      <h2>Support Team TERMINAL</h2>
      <p>
        Establish your identity node according to global standards.
        Official processing via <b>connect@unicou.uk</b>
      </p>
    </div>
    <form id="satForm" action="{{ route('job.apply') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="vacancy_id" value="{{ $vacancy->id ?? '' }}">

<!-- ================= BASIC INFO ================= -->
<section class="sat-section">
  <h4><span>01</span> Basic Information</h4>

  <div class="sat-grid-2">
    <div class="sat-field">
      <label>Full Name *</label>
      <input type="text" name="name" placeholder="Enter full name as per ID" required>
    </div>

    <div class="sat-field">
      <label>Date of Birth (as per ID) *</label>
      <input type="date" name="dob" required>
    </div>
  </div>
</section>

<!-- ================= CONTACT ================= -->
<section class="sat-section">
  <h4><span>02</span> Contact Details</h4>

  <div class="sat-grid-2">
    <div class="sat-field">
      <label>Contact Number *</label>
      <input type="tel" name="phone" placeholder="+CountryCode Mobile Number" required>
    </div>

    <div class="sat-field">
      <label>Email (Voucher Delivery) *</label>
      <input type="email" name="email" placeholder="official@email.com" required>
    </div>

    <div class="sat-field">
      <label>WhatsApp Number *</label>
      <input type="tel" name="whatsapp_number" placeholder="+CountryCode WhatsApp Number" required>
    </div>

    <div class="sat-field">
      <label>Social Media Profile Link</label>
      <input type="url" name="social_link" placeholder="https://linkedin.com/in/username">
    </div>
  </div>
</section>

<!-- ================= ADDRESS ================= -->
<section class="sat-section">
  <h4><span>03</span> Address Information</h4>

  <div class="sat-grid-2">
    <div class="sat-field sat-full">
      <label>Detail Address *</label>
      <input type="text" name="address" placeholder="Street, building, area" required>
    </div>

    <div class="sat-field">
      <label>Country *</label>
      <select name="country" id="country" required></select>
    </div>

    <div class="sat-field">
      <label>State / Province *</label>
      <select name="state" id="state" required></select>
    </div>

    <div class="sat-field">
      <label>City *</label>
      <select name="city" id="city" required></select>
    </div>

    <div class="sat-field">
      <label>Post Code *</label>
      <input type="text" name="post_code" placeholder="Postal / ZIP code" required>
    </div>
  </div>
</section>

<!-- ================= REFERENCE ================= -->
<section class="sat-section">
  <h4><span>04</span> Reference Information</h4>

  <div class="sat-grid-2">
    <div class="sat-field">
      <label>Reference Name *</label>
      <input type="text" name="reference_name" placeholder="Reference full name" required>
    </div>

    <div class="sat-field">
      <label>Organization Name *</label>
      <input type="text" name="organization_name" placeholder="Company / Organization" required>
    </div>

    <div class="sat-field">
      <label>Reference Email *</label>
      <input type="email" name="reference_email" placeholder="reference@email.com" required>
    </div>

    <div class="sat-field">
      <label>Reference Contact No *</label>
      <input type="tel" name="reference_phone" placeholder="+CountryCode Number" required>
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
        <option value="National ID Card">National ID Card</option>
        <option value="Passport">Passport</option>
        <option value="Driving License">Driving License</option>
      </select>
    </div>

    <div class="sat-field">
      <label>ID Document Number *</label>
      <input type="text" name="id_number" placeholder="Enter document number" required>
    </div>

    <div class="sat-field sat-full">
      <label>Designation *</label>
      <select name="designation" required>
        <option value="">Select designation</option>
        <option value="CEO">CEO</option>
        <option value="Managing Partner">Managing Partner</option>
        <option value="Partner">Partner</option>
        <option value="Proprietor">Proprietor</option>
      </select>
    </div>
  </div>
</section>

<!-- ================= BANK DETAILS ================= -->
<section class="sat-section">
  <h4><span>06</span> Bank Account Details</h4>

  <div class="sat-grid-2">
    <div class="sat-field">
      <label>Bank Name *</label>
      <input type="text" name="bank_name" placeholder="Bank name" required>
    </div>

    <div class="sat-field">
      <label>Bank Country *</label>
      <input type="text" name="bank_country" placeholder="Country where bank is located" required>
    </div>

    <div class="sat-field sat-full">
      <label>IBAN / BSB / Account Number *</label>
      <input type="text" name="bank_account_number" placeholder="Enter IBAN / Account number" required>
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
    </div>

    <div class="sat-field sat-full">
      <label>Upload Photograph *</label>
      <input type="file" name="photograph" required>
    </div>

    <div class="sat-field sat-full">
      <label>Upload Reference Letter *</label>
      <input type="file" name="reference_letter" required>
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
</div>
<script>
$(document).ready(function() {
    $('#satForm').on('submit', function(e) {
        e.preventDefault();
        
        let formData = new FormData(this);
        let submitBtn = $('.sat-btn');
        submitBtn.prop('disabled', true).text('SUBMITTING...');

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Successfully applied',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        window.location.href = "{{ route('home') }}";
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: response.message || 'Something went wrong',
                        icon: 'error'
                    });
                    submitBtn.prop('disabled', false).text('SUBMIT REGISTRATION →');
                }
            },
            error: function(xhr) {
                let errors = xhr.responseJSON.errors;
                let errorMessage = 'Something went wrong';
                if (errors) {
                    errorMessage = Object.values(errors).flat().join('\n');
                }
                Swal.fire({
                    title: 'Error!',
                    text: errorMessage,
                    icon: 'error'
                });
                submitBtn.prop('disabled', false).text('SUBMIT REGISTRATION →');
            }
        });
    });
});
</script>

<script type="module">
  import {
    Country,
    State,
    City
  } from "https://cdn.jsdelivr.net/npm/country-state-city@3.2.1/+esm";

  document.addEventListener("DOMContentLoaded", function() {
    const countrySelect = document.getElementById("country");
    const stateSelect = document.getElementById("state");
    const citySelect = document.getElementById("city");

    // Set default options
    countrySelect.innerHTML = '<option value="">Select Country</option>';
    stateSelect.innerHTML = '<option value="">Select State</option>';
    citySelect.innerHTML = '<option value="">Select City</option>';

    // Populate Countries
    const countries = Country.getAllCountries();
    countries.forEach(country => {
      const option = document.createElement('option');
      option.value = country.name;
      option.textContent = country.name;
      option.dataset.code = country.isoCode;
      countrySelect.appendChild(option);
    });

    // Country Change Event
    countrySelect.addEventListener('change', function() {
      const selectedOption = this.options[this.selectedIndex];
      const countryCode = selectedOption.dataset.code;

      stateSelect.innerHTML = '<option value="">Select State</option>';
      citySelect.innerHTML = '<option value="">Select City</option>';

      if (countryCode) {
        const states = State.getStatesOfCountry(countryCode);
        states.forEach(state => {
          const option = document.createElement('option');
          option.value = state.name;
          option.textContent = state.name;
          option.dataset.code = state.isoCode;
          stateSelect.appendChild(option);
        });
      }
    });

    // State Change Event
    stateSelect.addEventListener('change', function() {
      const countryCode = countrySelect.options[countrySelect.selectedIndex].dataset.code;
      const stateCode = this.options[this.selectedIndex].dataset.code;

      citySelect.innerHTML = '<option value="">Select City</option>';

      if (countryCode && stateCode) {
        const cities = City.getCitiesOfState(countryCode, stateCode);
        cities.forEach(city => {
          const option = document.createElement('option');
          option.value = city.name;
          option.textContent = city.name;
          citySelect.appendChild(option);
        });
      }
    });
  });
</script>
</body>
</html>
