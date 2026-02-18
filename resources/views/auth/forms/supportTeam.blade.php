<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Support Team TERMINAL</title>
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
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
    <form id="satForm">

<!-- ================= BASIC INFO ================= -->
<section class="sat-section">
  <h4><span>01</span> Basic Information</h4>

  <div class="sat-grid-2">
    <div class="sat-field">
      <label>Full Name *</label>
      <input type="text" placeholder="Enter full name as per ID" required>
    </div>

    <div class="sat-field">
      <label>Date of Birth (as per ID) *</label>
      <input type="date" required>
    </div>
  </div>
</section>

<!-- ================= CONTACT ================= -->
<section class="sat-section">
  <h4><span>02</span> Contact Details</h4>

  <div class="sat-grid-2">
    <div class="sat-field">
      <label>Contact Number *</label>
      <input type="tel" placeholder="+CountryCode Mobile Number" required>
    </div>

    <div class="sat-field">
      <label>Email (Voucher Delivery) *</label>
      <input type="email" placeholder="official@email.com" required>
    </div>

    <div class="sat-field">
      <label>WhatsApp Number *</label>
      <input type="tel" placeholder="+CountryCode WhatsApp Number" required>
    </div>

    <div class="sat-field">
      <label>Social Media Profile Link</label>
      <input type="url" placeholder="https://linkedin.com/in/username">
    </div>
  </div>
</section>

<!-- ================= ADDRESS ================= -->
<section class="sat-section">
  <h4><span>03</span> Address Information</h4>

  <div class="sat-grid-2">
    <div class="sat-field sat-full">
      <label>Detail Address *</label>
      <input type="text" placeholder="Street, building, area" required>
    </div>

    <div class="sat-field">
      <label>City *</label>
      <input type="text" placeholder="Enter city" required>
    </div>

    <div class="sat-field">
      <label>State / Province *</label>
      <input type="text" placeholder="Enter state or province" required>
    </div>

    <div class="sat-field">
      <label>Country *</label>
      <input type="text" placeholder="Enter country" required>
    </div>

    <div class="sat-field">
      <label>Post Code *</label>
      <input type="text" placeholder="Postal / ZIP code" required>
    </div>
  </div>
</section>

<!-- ================= REFERENCE ================= -->
<section class="sat-section">
  <h4><span>04</span> Reference Information</h4>

  <div class="sat-grid-2">
    <div class="sat-field">
      <label>Reference Name *</label>
      <input type="text" placeholder="Reference full name" required>
    </div>

    <div class="sat-field">
      <label>Organization Name *</label>
      <input type="text" placeholder="Company / Organization" required>
    </div>

    <div class="sat-field">
      <label>Reference Email *</label>
      <input type="email" placeholder="reference@email.com" required>
    </div>

    <div class="sat-field">
      <label>Reference Contact No *</label>
      <input type="tel" placeholder="+CountryCode Number" required>
    </div>
  </div>
</section>

<!-- ================= ID DETAILS ================= -->
<section class="sat-section">
  <h4><span>05</span> Identity Details</h4>

  <div class="sat-grid-2">
    <div class="sat-field">
      <label>ID Document Type *</label>
      <select required>
        <option value="">Select document type</option>
        <option>National ID Card</option>
        <option>Passport</option>
        <option>Driving License</option>
      </select>
    </div>

    <div class="sat-field">
      <label>ID Document Number *</label>
      <input type="text" placeholder="Enter document number" required>
    </div>

    <div class="sat-field sat-full">
      <label>Designation *</label>
      <select required>
        <option value="">Select designation</option>
        <option>CEO</option>
        <option>Managing Partner</option>
        <option>Partner</option>
        <option>Proprietor</option>
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
      <input type="text" placeholder="Bank name" required>
    </div>

    <div class="sat-field">
      <label>Bank Country *</label>
      <input type="text" placeholder="Country where bank is located" required>
    </div>

    <div class="sat-field sat-full">
      <label>IBAN / BSB / Account Number *</label>
      <input type="text" placeholder="Enter IBAN / Account number" required>
    </div>
  </div>
</section>

<!-- ================= UPLOADS ================= -->
<section class="sat-section">
  <h4><span>07</span> Document Uploads</h4>

  <div class="sat-grid-2">
    <div class="sat-field sat-full">
      <label>Upload ID Document *</label>
      <input type="file" required>
    </div>

    <div class="sat-field sat-full">
      <label>Upload Photograph *</label>
      <input type="file" required>
    </div>

    <div class="sat-field sat-full">
      <label>Upload Reference Letter *</label>
      <input type="file" required>
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
</body>
</html>
