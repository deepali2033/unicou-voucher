@extends('layouts.auth')

@section('title', 'Login')
<style>
    /* ===== Card & Header ===== */
    .sat-card {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .06);
    }

    .sat-header h2 {
        font-size: 22px;
        font-weight: 700;
    }

    .sat-header p {
        font-size: 13px;
        color: #6c757d;
    }

    /* ===== Wizard Stepper (ACTIVE ONE) ===== */
    .wizard-steps {
        display: flex;
        justify-content: space-between;
        position: relative;
        padding: 20px 10px;
        margin-bottom: 30px;
    }

    .wizard-steps::before {
        content: "";
        position: absolute;
        top: 32px;
        left: 0;
        right: 0;
        height: 2px;
        background: #e9ecef;
        z-index: 0;
    }

    .wizard-step {
        flex: 1;
        text-align: center;
        position: relative;
        z-index: 1;
    }

    .wizard-step span {
        width: 36px;
        height: 36px;
        background: #dee2e6;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-weight: 600;
        color: #495057;
    }

    .wizard-step small {
        display: block;
        font-size: 11px;
        margin-top: 6px;
        color: #adb5bd;
    }

    .wizard-step.active span {
        background: #2563eb;
        color: #fff;
    }

    .wizard-step.active small {
        color: #2563eb;
    }

    /* ===== Form Fields ===== */
    .sat-field label {
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 6px;
        color: #6c757d;
    }

    .sat-field input,
    .sat-field select {
        border-radius: 10px;
        padding: 12px 14px;
        font-size: 14px;
        border: 1px solid #e5e7eb;
    }

    .sat-field input:focus,
    .sat-field select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, .15);
    }

    /* ===== Buttons ===== */
    .btn-primary {
        background: #2563eb;
        border: none;
        padding: 10px 24px;
        border-radius: 12px;
        font-weight: 600;
    }

    .btn-light {
        background: #f8f9fa;
        border-radius: 12px;
    }

    /* ===== Step Visibility ===== */
    .form-step {
        display: none;
    }

    .form-step.active {
        display: block;
    }

    button.btn.btn-light.prev-btn {
        display: flex;
        white-space: nowrap;
        align-items: center;
        padding: 0px 20px;
    }

    button.btn.btn-primary.next-btn {
        width: max-content;
    }


    /* B2B Reseller Agent form end */

    .captcha-wrapper.p-3.border.rounded.bg-light.d-flex.align-items-center {
        width: max-content;
    }
</style>


@section('content')


<div class="sat-view-container">


    <div class="sat-card">
        <div class="sat-header d-flex justify-content-between align-items-center">
            <div>
                <h2>B2B Reseller Agent</h2>
                <h4 class="text-primary mt-2">USER ID: {{ Auth::user()->user_id }}</h4>
                <p>
                    Establish your identity node according to global standards.
                    Official processing via <b>connect@unicou.uk</b>
                </p>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm" onclick="fillAgentDemoData()">
                <i class="fas fa-magic"></i> Auto Fill
            </button>
        </div>

        @if ($errors->any())
        <div class="alert alert-danger mx-4 mt-3 mb-0">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ is_array($error) ? implode(', ', $error) : $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="wizard-steps mb-4">
            <div class="wizard-step active" data-step="1">
                <span>✓</span>
                <small>TYPE</small>
            </div>
            <div class="wizard-step" data-step="2"><span>2</span><small>BUSINESS</small></div>
            <div class="wizard-step" data-step="3"><span>3</span><small>ADDRESS</small></div>
            <div class="wizard-step" data-step="4"><span>4</span><small>ONLINE</small></div>
            <div class="wizard-step" data-step="5"><span>5</span><small>ID</small></div>
            <div class="wizard-step" data-step="6"><span>6</span><small>BANK</small></div>
            <div class="wizard-step" data-step="7"><span>7</span><small>UPLOAD</small></div>
        </div>




        <form id="satForm" method="POST" action="{{ route('auth.form.agent.post') }}" enctype="multipart/form-data">
            @csrf
            <!-- ================= AGENT TYPE ================= -->
            <section class="sat-section form-step" data-step="1">
                <h4><span>01</span> Agent Type</h4>

                <div class="sat-grid-2">
                    @if(auth()->user()->account_type === 'agent')
                    <label class="sat-consent cls_radio_btn d-flex align-items-center gap-2 p-3 border rounded cursor-pointer">
                        <input type="radio" name="agentType" value="Regular" checked required style="width: 20px; height: 20px;">
                        <div class="ms-2">
                            <p class="mb-0 fw-bold">Regular Agent</p>
                        </div>
                    </label>
                    @else
                    <label class="sat-consent cls_radio_btn d-flex align-items-center gap-2 p-3 border rounded cursor-pointer">
                        <input type="radio" name="agentType" value="Regular" required style="width: 20px; height: 20px;">
                        <div class="ms-2">
                            <p class="mb-0 fw-bold">Regular Agent</p>
                        </div>
                    </label>

                    <label class="sat-consent cls_radio_btn d-flex align-items-center gap-2 p-3 border rounded cursor-pointer">
                        <input type="radio" name="agentType" value="Reseller" required style="width: 20px; height: 20px;">
                        <div class="ms-2">
                            <p class="mb-0 fw-bold">Reseller Agent</p>
                            <small class="text-muted" style="font-size: 0.75rem;">
                                (Can view sub-agent list & order history only. No access to vouchers or sensitive data.)
                            </small>
                        </div>
                    </label>
                    @endif
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-light prev-btn">← Back</button>
                    <button type="button" class="btn btn-primary next-btn">Continue →</button>
                </div>
            </section>

            <!-- ================= BUSINESS INFO ================= -->
            <section class="sat-section form-step" data-step="2">
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
                        <input type="tel" name="business_contact" placeholder="+CountryCode Business Contact" value="{{ Auth::user()->phone }}" required>
                    </div>

                    <div class="sat-field">
                        <label>Email (Voucher Delivery) *</label>
                        <input type="email" name="business_email" placeholder="official@business.com" value="{{ Auth::user()->email }}" required>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-light prev-btn">← Back</button>
                    <button type="button" class="btn btn-primary next-btn">Continue →</button>
                </div>
            </section>

            <!-- ================= BUSINESS ADDRESS ================= -->
            <section class="sat-section form-step" data-step="3">
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

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-light prev-btn">← Back</button>
                    <button type="button" class="btn btn-primary next-btn">Continue →</button>
                </div>
            </section>

            <!-- ================= ONLINE PRESENCE ================= -->
            <section class="sat-section form-step" data-step="4">
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

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-light prev-btn">← Back</button>
                    <button type="button" class="btn btn-primary next-btn">Continue →</button>
                </div>

            </section>

            <!-- ================= REPRESENTATIVE ================= -->
            <section class="sat-section form-step" data-step="5">
                <h4><span>05</span> Business Representative</h4>

                <div class="sat-grid-2">
                    <div class="sat-field">
                        <label>Representative Name *</label>
                        <input type="text" name="representative_name" placeholder="Full name as per ID" value="{{ Auth::user()->name }}" required>
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


                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-light prev-btn">← Back</button>
                    <button type="button" class="btn btn-primary next-btn">Continue →</button>
                </div>
            </section>

            <!-- ================= BANK DETAILS ================= -->
            <section class="sat-section form-step" data-step="6">
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

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-light prev-btn">← Back</button>
                    <button type="button" class="btn btn-primary next-btn">Continue →</button>
                </div>
            </section>

            <!-- ================= UPLOADS ================= -->
            <section class="sat-section form-step" data-step="7">
                <h4><span>07</span> Upload Documents</h4>
                <div class="alert alert-warning mb-3" style="font-size: 0.85rem;">
                    <i class="fas fa-exclamation-circle me-1"></i> <strong>Important:</strong> Your name, address, and date of birth in this form <b>MUST EXACTLY MATCH</b> the details on your uploaded ID documents. Discrepancies will lead to automatic rejection by ShuftiPro.
                </div>

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

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-light prev-btn">← Back</button>
                    <button type="button" class="btn btn-primary next-btn">Continue →</button>
                </div>

            </section>

            <!-- ================= CONSENTS ================= -->
            <div class="sat-consent form-step" data-step="8">
                <input type="checkbox" required id="consent_policy">
                <p>
                    I agree to the <a href="javascript:void(0)" class="text-primary fw-bold" data-bs-toggle="modal" data-bs-target="#termsModal">Non-Refundable & Undisclosed Voucher Policy</a>.
                </p>
            </div>

            <div class="sat-consent form-step" data-step="9">
                <input type="checkbox" required id="consent_terms">
                <p>
                    I accept the <a href="javascript:void(0)" class="text-primary fw-bold" data-bs-toggle="modal" data-bs-target="#termsModal">Terms & Conditions</a> and confirm all submitted information is correct.
                </p>
            </div>

            <button type="submit" class="sat-btn">
                REGISTER AS B2B AGENT →
            </button>

        </form>

    </div>
</div>

<!-- Terms and Conditions Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="terms-content">
                    <h6 class="fw-bold mb-3">1. Declaration of Authenticity & Truth</h6>
                    <p>I declare that all business credentials and ID documents provided are authentic. Any misrepresentation grants UniCou the right to terminate my account immediately and seize all pending credits/vouchers as a penalty.</p>

                    <h6 class="fw-bold mb-3">2. Account Security & Credential Protection</h6>
                    <ul class="mb-3">
                        <li><strong>Confidentiality:</strong> I am solely responsible for maintaining the security of my account login and passwords.</li>
                        <li><strong>Staff Misuse:</strong> I accept full legal and financial liability for all transactions made through my account, including those resulting from misuse by my staff or sub-agents, internal theft, or shared credentials.</li>
                    </ul>

                    <h6 class="fw-bold mb-3">3. Non-Refundable & "Undisclosed" Voucher Policy</h6>
                    <ul class="mb-3">
                        <li><strong>Final Sale:</strong> I acknowledge that exam vouchers (PTE, IELTS, TOEFL, etc.) are digital assets and are strictly non-refundable and non-exchangeable once issued to my account.</li>
                        <li><strong>Undisclosed Status:</strong> This policy applies regardless of whether the voucher remains Undisclosed to the end candidate. Once the voucher code is generated and delivered to my portal, the transaction is considered consumed and final.</li>
                        <li><strong>No Reversals:</strong> I agree that I cannot request a refund for unused, expired, or mistakenly purchased vouchers.</li>
                    </ul>

                    <h6 class="fw-bold mb-3">4. Reseller & Sub-Agent Liability</h6>
                    <p>If I operate as a Reseller:</p>
                    <ul class="mb-3">
                        <li><strong>Full Responsibility:</strong> I accept absolute liability for the actions of my sub-agents. Any fraud or "fake payment" by them is treated as my personal breach.</li>
                        <li><strong>Payment Obligation:</strong> I am responsible for paying UniCou regardless of whether my sub-agent has paid me.</li>
                    </ul>

                    <h6 class="fw-bold mb-3">5. Payment Warranty & Anti-Fraud Protection</h6>
                    <ul class="mb-3">
                        <li><strong>Fraud Consequences:</strong> In the event of a "Fake Payment" or chargeback, UniCou is authorized to instantly deactivate all associated vouchers.</li>
                        <li><strong>Result Invalidation:</strong> I agree that UniCou may notify Pearson, IDP, British Council, or ETS of the fraud, leading to the cancellation of test results.</li>
                    </ul>

                    <h6 class="fw-bold mb-3">6. Irrevocable Indemnity Bond</h6>
                    <p>I hereby agree to indemnify and hold UniCou harmless against any losses, third-party claims, or damages arising from my (or my staff/sub- agents') negligence or fraudulent activity.</p>

                    <h6 class="fw-bold mb-3">7. Settlement & Currency</h6>
                    <p>Payments are settled via Airwallex/Tazapay/Verto at UniCou’s rates. I am responsible for all local bank fees and currency conversion costs.</p>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">I Understand</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('satForm').addEventListener('submit', function() {
        const btn = this.querySelector('.sat-btn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> VERIFYING DOCUMENTS... PLEASE WAIT';
    });

    function fillAgentDemoData() {
        const form = document.getElementById('satForm');
        form.querySelector('input[name="agentType"][value="Regular"]').checked = true;
        form.querySelector('input[name="business_name"]').value = "UniCou Global Solutions";
        form.querySelector('select[name="business_type"]').value = "Company";
        form.querySelector('input[name="registration_number"]').value = "REG123456789";
        form.querySelector('input[name="business_contact"]').value = "+44 7700 900000";
        form.querySelector('input[name="business_email"]').value = "contact@unicou-demo.uk";
        form.querySelector('input[name="address"]').value = "123 Business Avenue, Tech City";
        form.querySelector('input[name="city"]').value = "London";
        form.querySelector('input[name="state"]').value = "Greater London";
        form.querySelector('input[name="country"]').value = "United Kingdom";
        form.querySelector('input[name="post_code"]').value = "EC1A 1BB";
        form.querySelector('input[name="website"]').value = "https://unicou-demo.uk";
        form.querySelector('input[name="social_media"]').value = "https://linkedin.com/company/unicou";
        form.querySelector('input[name="representative_name"]').value = "John Doe";
        form.querySelector('input[name="dob"]').value = "1990-01-01";
        form.querySelector('select[name="id_type"]').value = "Passport";
        form.querySelector('input[name="id_number"]').value = "L87654321";
        form.querySelector('select[name="designation"]').value = "CEO";
        form.querySelector('input[name="whatsapp_number"]').value = "+44 7700 900000";
        form.querySelector('input[name="bank_name"]').value = "Barclays Bank";
        form.querySelector('input[name="bank_country"]').value = "United Kingdom";
        form.querySelector('input[name="account_number"]').value = "GB12BARC20202012345678";
        document.getElementById('consent_policy').checked = true;
        document.getElementById('consent_terms').checked = true;
    }
</script>


<script>
    let currentStep = parseInt(localStorage.getItem('agentStep')) || 1;

    const steps = document.querySelectorAll('.form-step');
    const indicators = document.querySelectorAll('.wizard-step');

    function showStep(step) {

        // hide all form steps
        steps.forEach(s => s.classList.remove('active'));

        // reset stepper
        indicators.forEach(i => {
            i.classList.remove('active');
            i.querySelector('span').innerText = i.dataset.step;
        });

        // show current form
        document.querySelector(`.form-step[data-step="${step}"]`)?.classList.add('active');

        // activate current step
        const activeStep = document.querySelector(`.wizard-step[data-step="${step}"]`);
        if (activeStep) {
            activeStep.classList.add('active');
            activeStep.querySelector('span').innerText = "✓";
        }

        localStorage.setItem('agentStep', step);
    }

    // init
    showStep(currentStep);

    // NEXT
    document.querySelectorAll('.next-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            if (currentStep < indicators.length) {
                currentStep++;
                showStep(currentStep);
            }
        });
    });

    // BACK
    document.querySelectorAll('.prev-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        });
    });
</script>


@endpush
@endsection