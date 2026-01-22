<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>B2B Reseller Agent</title>
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

<div class="sat-view-container">
    <div class="sat-card">
        <div class="sat-header">
            <h2>B2B Reseller Agent</h2>
            <p>
            Establish your identity node according to global standards.
            Official processing via <b>connect@unicou.uk</b>
            </p>
        </div>
        <form id="satForm" method="POST" action="{{ route('auth.form.agent.post') }}" enctype="multipart/form-data">
            @csrf
        <!-- ================= AGENT TYPE ================= -->
        <section class="sat-section">
        <h4><span>01</span> Agent Type</h4>

        <div class="sat-grid-2">
        <div class="sat-consent cls_radio_btn">
            <input type="radio" name="agentType" value="Regular" required>
            <p>Regular Agent</p>
        </div>

        <div class="sat-consent cls_radio_btn">
            <input type="radio" name="agentType" value="Reseller" required>
            <p>
            Reseller Agent<br>
            <small>
                (Can view sub-agent list & order history only.  
                No access to vouchers or sensitive data.)
            </small>
            </p>
        </div>
        </div>
        </section>

        <!-- ================= BUSINESS INFO ================= -->
        <section class="sat-section">
        <h4><span>02</span> Business Information</h4>

        <div class="sat-grid-2">
        <div class="sat-field">
            <label>Business Name *</label>
            <input type="text" name="business_name" placeholder="Registered business name" required>
        </div>

        <div class="sat-field">
            <label>Nature of Business *</label>
            <select name="business_type" required>
            <option value="">Select business type</option>
            <option>Company</option>
            <option>AOP</option>
            <option>Firm</option>
            <option>Proprietorship</option>
            </select>
        </div>

        <div class="sat-field">
            <label>Business Registration Number *</label>
            <input type="text" name="registration_number" placeholder="Registration / Incorporation No" required>
        </div>

        <div class="sat-field">
            <label>Contact Number *</label>
            <input type="tel" name="business_contact" placeholder="+CountryCode Business Contact" required>
        </div>

        <div class="sat-field">
            <label>Email (Voucher Delivery) *</label>
            <input type="email" name="business_email" placeholder="official@business.com" required>
        </div>
        </div>
        </section>

        <!-- ================= BUSINESS ADDRESS ================= -->
        <section class="sat-section">
        <h4><span>03</span> Business Address</h4>

        <div class="sat-grid-2">
        <div class="sat-field sat-full">
            <label>Detail Address *</label>
            <input type="text" name="address" placeholder="Office / Shop address" required>
        </div>

        <div class="sat-field">
            <label>City *</label>
            <input type="text" name="city" placeholder="City" required>
        </div>

        <div class="sat-field">
            <label>State / Province *</label>
            <input type="text" name="state" placeholder="State or province" required>
        </div>

        <div class="sat-field">
            <label>Country *</label>
            <input type="text" name="country" placeholder="Country" required>
        </div>

        <div class="sat-field">
            <label>Post Code *</label>
            <input type="text" name="post_code" placeholder="Postal / ZIP code" required>
        </div>
        </div>
        </section>

        <!-- ================= ONLINE PRESENCE ================= -->
        <section class="sat-section">
        <h4><span>04</span> Online Presence</h4>

        <div class="sat-grid-2">
        <div class="sat-field">
            <label>Website</label>
            <input type="url" name="website" placeholder="https://yourwebsite.com">
        </div>

        <div class="sat-field">
            <label>Social Media Link</label>
            <input type="url" name="social_media" placeholder="https://facebook.com / linkedin.com">
        </div>
        </div>
        </section>

        <!-- ================= REPRESENTATIVE ================= -->
        <section class="sat-section">
        <h4><span>05</span> Business Representative</h4>

        <div class="sat-grid-2">
        <div class="sat-field">
            <label>Representative Name *</label>
            <input type="text" name="representative_name" placeholder="Full name as per ID" required>
        </div>

        <div class="sat-field">
            <label>Date of Birth (as per ID) *</label>
            <input type="date" name="dob" required>
        </div>

        <div class="sat-field">
            <label>ID Document Type *</label>
            <select name="id_type" required>
            <option value="">Select document type</option>
            <option>National ID Card</option>
            <option>Passport</option>
            <option>Driving License</option>
            </select>
        </div>

        <div class="sat-field">
            <label>ID Document Number *</label>
            <input type="text" name="id_number" placeholder="ID / Passport number" required>
        </div>

        <div class="sat-field sat-full">
            <label>Designation *</label>
            <select name="designation" required>
            <option value="">Select designation</option>
            <option>CEO</option>
            <option>Managing Partner</option>
            <option>Partner</option>
            <option>Proprietor</option>
            </select>
        </div>

        <div class="sat-field sat-full">
            <label>WhatsApp Number *</label>
            <input type="tel" name="whatsapp_number" placeholder="+CountryCode WhatsApp number" required>
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
            <input type="text" name="bank_country" placeholder="Country of bank" required>
        </div>

        <div class="sat-field sat-full">
            <label>IBAN / BSB / Account Number *</label>
            <input type="text" name="account_number" placeholder="IBAN / Account number" required>
        </div>
        </div>
        </section>

        <!-- ================= UPLOADS ================= -->
        <section class="sat-section">
        <h4><span>07</span> Upload Documents</h4>

        <div class="sat-grid-2">
        <div class="sat-field sat-full">
            <label>Business Registration Document *</label>
            <input type="file" name="registration_doc" required>
        </div>

        <div class="sat-field sat-full">
            <label>ID Document *</label>
            <input type="file" name="id_doc" required>
        </div>

        <div class="sat-field sat-full">
            <label>Business Logo (Profile Photo)</label>
            <input type="file" name="business_logo">
        </div>
        </div>
        </section>

        <!-- ================= CONSENTS ================= -->
        <div class="sat-consent">
        <input type="checkbox" required>
        <p>
        I agree to the <b>Non-Refundable & Undisclosed Voucher Policy</b>.
        </p>
        </div>

        <div class="sat-consent">
        <input type="checkbox" required>
        <p>
        I accept the <b>Terms & Conditions</b> and confirm all submitted information is correct.
        </p>
        </div>

        <button type="submit" class="sat-btn">
        REGISTER AS B2B AGENT →
        </button>

        </form>

    </div>
</div>
</body>
</html>
