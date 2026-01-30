@extends('agent.layout.app')

@section('content')
<style>
    .link-container {
        max-width: 900px;
        margin: auto;
        padding: 20px;
    }

    .link-card {
        background: #fff;
        padding: 35px;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        border: 1px solid #f0f0f0;
    }

    .bank-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
    }

    .bank-item {
        border: 2px solid #f0f0f0;
        border-radius: 12px;
        padding: 15px 10px;
        text-align: center;
        cursor: pointer;
        font-size: 0.9rem;
        font-weight: 600;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 60px;
        background: #fafafa;
        color: #555;
    }

    .bank-item:hover {
        border-color: #d23fa0;
        transform: translateY(-2px);
        background: #fff;
    }

    .bank-item.selected {
        background-color: #d23fa0;
        color: #fff;
        border-color: #d23fa0;
        box-shadow: 0 4px 12px rgba(210, 63, 160, 0.2);
    }

    .form-label {
        font-weight: 700;
        margin-bottom: 10px;
        display: block;
        color: #444;
    }

    .form-control-custom {
        width: 100%;
        padding: 14px;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        margin-bottom: 25px;
        transition: border-color 0.2s;
    }

    .form-control-custom:focus {
        border-color: #d23fa0;
        outline: none;
    }

    .note-box {
        background: #fff9fe;
        border: 1px dashed #d23fa0;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 30px;
        font-size: 0.9rem;
        color: #666;
    }

    .btn-add-bank {
        background: linear-gradient(90deg, #d23fa0 0%, #a0317d 100%);
        color: #fff;
        border: none;
        padding: 15px 40px;
        border-radius: 10px;
        font-weight: 700;
        width: 100%;
        box-shadow: 0 4px 15px rgba(210, 63, 160, 0.3);
    }
</style>

<div class="link-container">
    <div class="d-flex align-items-center gap-3 mb-2">
        <h2 class="fw-bold mb-0">Link a New Bank</h2>
        <img src="{{ session('user_flag_url') }}" width="30" class="shadow-sm rounded-sm">
    </div>
    <p class="text-muted small mb-4">Securely connect your bank account to the portal</p>

    <div class="link-card">
        <form action="{{ route('agent.bank.store') }}" method="POST" id="bank-form">
            @csrf
            <input type="hidden" name="bank_name" id="selected-bank-name" required>

            <label class="form-label">Select Your Bank</label>
            <div class="bank-grid">
                @foreach($banks as $bank)
                <div class="bank-item" onclick="selectBank(this, '{{ $bank }}')">{{ $bank }}</div>
                @endforeach
            </div>

            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Account Number</label>
                    <input type="text" name="account_number" class="form-control-custom" placeholder="e.g. 1234567890" required>
                </div>

            </div>

            <div class="note-box">
                <i class="fas fa-info-circle me-2 text-primary"></i>
                A verification fee of <strong>{{ session('user_currency_symbol', 'Rs.') }} 100/-</strong> will be deducted. This amount will be added to your Store Credit.
            </div>

            <button type="submit" class="btn-add-bank">
                <i class="fas fa-plus-circle me-2"></i>Link Bank Account
            </button>
        </form>
    </div>
</div>

<script>
    function selectBank(element, name) {
        document.querySelectorAll('.bank-item').forEach(item => {
            item.classList.remove('selected');
        });
        element.classList.add('selected');
        document.getElementById('selected-bank-name').value = name;
    }
</script>
@endsection