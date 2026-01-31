@extends('layouts.master')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Linked Banks</h2>
            <p class="text-muted">Manage all your bank accounts in one place.</p>
        </div>
        <button class="btn btn-primary px-4 py-2 fw-bold" style="border-radius: 10px; background-color: #2563eb;" data-bs-toggle="modal" data-bs-target="#linkBankModal">
            <i class="fas fa-plus me-2"></i> Add New Bank
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3" style="border-radius: 16px; background-color: #0f172a; color: white;">
                <p class="small text-uppercase fw-bold opacity-75 mb-1">Total Net Worth</p>
                <h3 class="fw-bold mb-1">₹ 1035650.5, 1</h3>
                <p class="small text-success fw-bold mb-0">
                    <i class="fas fa-arrow-up me-1"></i> +2.5% this month
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3" style="border-radius: 16px; background: white;">
                <p class="small text-uppercase fw-bold text-muted mb-1">Total Accounts</p>
                <h3 class="fw-bold mb-1" $banks->count() </h3>
                <p class="small text-muted mb-0">All systems active</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3" style="border-radius: 16px; background: white;">
                <p class="small text-uppercase fw-bold text-muted mb-1">Monthly Saving</p>
                <h3 class="fw-bold mb-1">₹45,000</h3>
                <p class="small text-primary fw-bold mb-0">Saving Goal: 80%</p>
            </div>
        </div>
    </div>

    <!-- Banks Grid -->
    <div class="row g-4 mb-5">

        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4 position-relative" style="border-radius: 20px; border-left: 5px solid  !important;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-muted bg-light" style="width: 45px; height: 45px; border: 1px solid #e2e8f0;">
                            0, 1
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0"></h5>
                            <span class="small text-muted text-uppercase"></span>
                        </div>
                    </div>
                    <span class="badge bg-success-subtle text-success fw-bold px-3 py-2 rounded-pill small">ACTIVE</span>
                </div>
                <div class="mb-4">
                    <p class="small text-muted mb-1">Current Balance</p>
                    <h3 class="fw-bold">₹ </h3>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <p class="small text-muted mb-0">•••• </p>
                    <p class="small text-muted mb-0">Sync: 2 mins ago</p>
                </div>
            </div>
        </div>


        <!-- Add Another Bank Placeholder -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm d-flex align-items-center justify-content-center cursor-pointer"
                style="border-radius: 20px; min-height: 220px; border: 2px dashed #cbd5e1 !important; background: transparent;"
                data-bs-toggle="modal" data-bs-target="#linkBankModal">
                <div class="text-center">
                    <div class="rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 50px; height: 50px;">
                        <i class="fas fa-plus text-muted"></i>
                    </div>
                    <p class="fw-bold text-muted mb-0">Add another bank</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="card border-0 shadow-sm p-4" style="border-radius: 20px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0">Recent Transactions</h5>
            <a href="#" class="text-primary fw-bold text-decoration-none small">View All</a>
        </div>
        <div class="table-responsive">
            <table class="table table-borderless align-middle">
                <thead>
                    <tr class="text-muted small text-uppercase">
                        <th>Entity</th>
                        <th>Category</th>
                        <th>Date</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded bg-light p-2"><i class="fas fa-coffee text-muted"></i></div>
                                <span class="fw-bold">Starbucks Coffee</span>
                            </div>
                        </td>
                        <td class="text-muted">Food & Drink</td>
                        <td class="text-muted">2023-10-24</td>
                        <td class="text-end fw-bold">-₹450</td>
                    </tr>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded bg-light p-2"><i class="fas fa-laptop text-muted"></i></div>
                                <span class="fw-bold">Apple Store</span>
                            </div>
                        </td>
                        <td class="text-muted">Electronics</td>
                        <td class="text-muted">2023-10-23</td>
                        <td class="text-end fw-bold">-₹12,500</td>
                    </tr>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded bg-light p-2"><i class="fas fa-wallet text-muted"></i></div>
                                <span class="fw-bold">Salary Credit</span>
                            </div>
                        </td>
                        <td class="text-muted">Income</td>
                        <td class="text-muted">2023-10-22</td>
                        <td class="text-end fw-bold text-success">+₹50,000</td>
                    </tr>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded bg-light p-2"><i class="fas fa-shopping-bag text-muted"></i></div>
                                <span class="fw-bold">Amazon India</span>
                            </div>
                        </td>
                        <td class="text-muted">Shopping</td>
                        <td class="text-muted">2023-10-21</td>
                        <td class="text-end fw-bold">-₹1,200</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Link Bank Modal -->
<div class="modal fade" id="linkBankModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 24px; overflow: hidden;">
            <div class="modal-body p-0">
                <!-- Step 1: Select Bank -->
                <div id="step-1" class="modal-step p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold mb-0">Link New Bank</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <p class="text-muted small mb-4">Apne bank ko select karein jise aap link karna chahte hain.</p>

                    <div class="input-group mb-4 bg-light rounded-3 p-1">
                        <span class="input-group-text bg-transparent border-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" class="form-control bg-transparent border-0" placeholder="Search your bank (e.g. SBI, HDFC)">
                    </div>

                    <div class="row g-3 mb-4">
                        @php $popularBanks = ['HDFC Bank', 'SBI Bank', 'ICICI Bank', 'Kotak Bank', 'Axis Bank', 'Yes Bank']; @endphp
                        @foreach($popularBanks as $b)
                        <div class="col-6">
                            <div class="card border-1 p-3 text-center cursor-pointer bank-select-card" style="border-radius: 16px; transition: all 0.2s;" onclick="goToStep(2, '{{ $b }}')">
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-muted bg-light mx-auto mb-2" style="width: 40px; height: 40px; border: 1px solid #e2e8f0;">
                                    {{ substr($b, 0, 1) }}
                                </div>
                                <span class="small fw-bold">{{ $b }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="text-center">
                        <p class="small text-muted mb-0"><i class="fas fa-lock me-1"></i> End-to-end encrypted under AA Framework.</p>
                    </div>
                </div>

                <!-- Step 2: Authenticate (Mobile Number) -->
                <div id="step-2" class="modal-step p-4 d-none">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <button type="button" class="btn btn-link text-muted p-0" onclick="goToStep(1)"><i class="fas fa-chevron-left"></i></button>
                        <h4 class="fw-bold mb-0">Authenticate</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="bg-light rounded-4 p-4 mb-4 text-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-muted bg-white mx-auto mb-2 shadow-sm" style="width: 50px; height: 50px;" id="selected-bank-logo">
                            H
                        </div>
                        <p class="small text-muted text-uppercase mb-1">Connecting to</p>
                        <h5 class="fw-bold mb-0" id="selected-bank-name-display">HDFC Bank</h5>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small">Mobile Number</label>
                        <div class="input-group border rounded-3 p-1">
                            <span class="input-group-text bg-transparent border-0 text-muted">+91</span>
                            <input type="text" class="form-control border-0" placeholder="Enter registered mobile" id="mobile-number">
                        </div>
                        <p class="small text-muted mt-2">Ensure this mobile number is linked with your <span class="bank-name-span">HDFC Bank</span> account.</p>
                    </div>

                    <button class="btn btn-primary w-100 py-3 fw-bold mb-4" style="border-radius: 16px; background-color: #93c5fd; border: none;" onclick="goToStep(3)">Request OTP</button>

                    <div class="text-center">
                        <p class="small text-muted mb-0"><i class="fas fa-lock me-1"></i> End-to-end encrypted under AA Framework.</p>
                    </div>
                </div>

                <!-- Step 3: Enter OTP -->
                <div id="step-3" class="modal-step p-4 d-none">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <button type="button" class="btn btn-link text-muted p-0" onclick="goToStep(2)"><i class="fas fa-chevron-left"></i></button>
                        <h4 class="fw-bold mb-0">Enter OTP</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <p class="text-muted small mb-4">We've sent a 6-digit verification code to your mobile number.</p>

                    <div class="d-flex gap-2 mb-4 justify-content-between">
                        <input type="text" class="form-control text-center py-3 fw-bold" style="border-radius: 12px; font-size: 1.2rem;" maxlength="1">
                        <input type="text" class="form-control text-center py-3 fw-bold" style="border-radius: 12px; font-size: 1.2rem;" maxlength="1">
                        <input type="text" class="form-control text-center py-3 fw-bold" style="border-radius: 12px; font-size: 1.2rem;" maxlength="1">
                        <input type="text" class="form-control text-center py-3 fw-bold" style="border-radius: 12px; font-size: 1.2rem;" maxlength="1">
                        <input type="text" class="form-control text-center py-3 fw-bold" style="border-radius: 12px; font-size: 1.2rem;" maxlength="1">
                        <input type="text" class="form-control text-center py-3 fw-bold" style="border-radius: 12px; font-size: 1.2rem;" maxlength="1">
                    </div>

                    <button class="btn btn-primary w-100 py-3 fw-bold mb-4" style="border-radius: 16px; background-color: #2563eb;" onclick="goToStep(4)">Verify & Proceed</button>

                    <div class="text-center">
                        <p class="small text-muted mb-0">Didn't receive code? <a href="#" class="text-primary text-decoration-none fw-bold">Resend OTP</a></p>
                    </div>
                </div>

                <!-- Step 4: Choose Account -->
                <div id="step-4" class="modal-step p-4 d-none">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold mb-0">Choose Accounts</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="text-center mb-4">
                        <h5 class="fw-bold mb-1">Accounts Found</h5>
                        <p class="text-muted small">Aapke accounts dhoondh liye gaye hain.</p>
                    </div>

                    <div class="mb-4">
                        <div class="card border-primary p-3 mb-3 d-flex flex-row align-items-center justify-content-between" style="border-radius: 16px; background-color: #f0f7ff;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px;">
                                    <i class="fas fa-university"></i>
                                </div>
                                <div>
                                    <p class="fw-bold mb-0">HDFC Bank Savings</p>
                                    <p class="small text-muted mb-0">XXXX 8822</p>
                                </div>
                            </div>
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white" style="width: 24px; height: 24px;">
                                <i class="fas fa-check small"></i>
                            </div>
                        </div>
                        <div class="card border-1 p-3 d-flex flex-row align-items-center justify-content-between opacity-50" style="border-radius: 16px;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-muted border" style="width: 40px; height: 40px;">
                                    <i class="fas fa-university"></i>
                                </div>
                                <div>
                                    <p class="fw-bold mb-0">HDFC Bank Current</p>
                                    <p class="small text-muted mb-0">XXXX 1144</p>
                                </div>
                            </div>
                            <div class="rounded-circle border d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-primary w-100 py-3 fw-bold mb-4" style="border-radius: 16px; background-color: #2563eb;" onclick="goToStep(5)">Confirm & Proceed</button>

                    <div class="text-center">
                        <p class="small text-muted mb-0"><i class="fas fa-lock me-1"></i> RBI regulated AA encryption applied.</p>
                    </div>
                </div>

                <!-- Step 5: Data Consent -->
                <div id="step-5" class="modal-step p-4 d-none">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold mb-0">Data Consent</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="bg-light rounded-4 p-4 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="small text-uppercase fw-bold text-muted">Data Sharing Consent</span>
                            <span class="badge bg-success-subtle text-success fw-bold px-2 py-1 rounded small">SECURE</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-6">
                                <p class="small text-muted mb-0">Validity</p>
                                <p class="fw-bold mb-0">12 Months</p>
                            </div>
                            <div class="col-6">
                                <p class="small text-muted mb-0">Frequency</p>
                                <p class="fw-bold mb-0">Daily</p>
                            </div>
                            <div class="col-6">
                                <p class="small text-muted mb-0">Data Type</p>
                                <p class="fw-bold mb-0">Transactions</p>
                            </div>
                            <div class="col-6">
                                <p class="small text-muted mb-0">Framework</p>
                                <p class="fw-bold mb-0">AA (RBI)</p>
                            </div>
                        </div>
                    </div>

                    <p class="small text-muted text-center mb-4">By clicking Approve, you agree to share your financial data with FinPulse via the Account Aggregator framework.</p>

                    <form id="link-bank-form" action="{{ route('agent.bank.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="bank_name" id="final-bank-name">
                        <input type="hidden" name="account_number" id="final-account-number" value="XXXX 8822">
                        <input type="hidden" name="id_number" value="12345-6789012-3"> <!-- Placeholder for cnic requirement -->

                        <button type="button" class="btn btn-dark w-100 py-3 fw-bold mb-3" style="border-radius: 16px; background-color: #1e293b;" onclick="submitBankForm()">Approve & Link Now</button>
                    </form>
                    <button class="btn btn-link w-100 text-muted text-decoration-none fw-bold small" data-bs-dismiss="modal">Deny Consent</button>

                    <div class="text-center mt-3">
                        <p class="small text-muted mb-0"><i class="fas fa-lock me-1"></i> RBI regulated AA encryption applied.</p>
                    </div>
                </div>

                <!-- Step 6: Success -->
                <div id="step-6" class="modal-step p-4 d-none text-center">
                    <div class="d-flex justify-content-end mb-2">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <h4 class="fw-bold mb-4">Success</h4>

                    <div class="rounded-circle bg-success-subtle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                        <i class="fas fa-check text-success fs-2"></i>
                    </div>

                    <h3 class="fw-bold mb-2">Mubarak Ho!</h3>
                    <p class="text-muted mb-5">Aapke accounts securely link ho chuke hain aur dashboard par sync ho rahe hain.</p>

                    <button class="btn btn-primary w-100 py-3 fw-bold mb-4" style="border-radius: 16px; background-color: #2563eb;" onclick="location.reload()">Show My Dashboard</button>

                    <div class="text-center">
                        <p class="small text-muted mb-0"><i class="fas fa-lock me-1"></i> RBI regulated AA encryption applied.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .cursor-pointer {
        cursor: pointer;
    }

    .bank-select-card:hover {
        border-color: #2563eb !important;
        background-color: #f0f7ff;
        transform: translateY(-2px);
    }

    .modal-step {
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<script>
    function goToStep(step, bankName = null) {
        // Hide all steps
        document.querySelectorAll('.modal-step').forEach(el => el.classList.add('d-none'));

        // Show target step
        document.getElementById('step-' + step).classList.remove('d-none');

        if (bankName) {
            document.getElementById('selected-bank-name-display').innerText = bankName;
            document.getElementById('selected-bank-logo').innerText = bankName.charAt(0);
            document.querySelectorAll('.bank-name-span').forEach(el => el.innerText = bankName);
            document.getElementById('final-bank-name').value = bankName;
        }
    }

    function submitBankForm() {
        // Show success step first for UI feel
        goToStep(6);

        // Delay submission to let user see success message or just submit directly
        // Given user wants "Mubarak Ho" screen, maybe stay there and refresh?
        // Let's stay on step 6 and then reload which would fetch from DB if we submitted.

        const form = document.getElementById('link-bank-form');
        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(response => {
            // Success handled by UI showing Step 6
        }).catch(error => console.error('Error:', error));
    }

    // Auto-focus OTP inputs
    document.querySelectorAll('#step-3 input').forEach((input, index, inputs) => {
        input.addEventListener('input', () => {
            if (input.value && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        });
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !input.value && index > 0) {
                inputs[index - 1].focus();
            }
        });
    });
</script>
@endsection