@extends('layouts.dashboard')
@section('title', 'Bank Deposit - Payment')
@section('page-title', 'Bank Deposit (VISA)')

@section('content')
<div class="card">
    <div class="card-header"><h5>Bank Deposit - Pay with VISA</h5></div>
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
n                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('user.send.bank_deposit.process') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Beneficiary</label>
                <select name="beneficiary_id" class="form-select" required>
                    <option value="">-- select beneficiary --</option>
                    @foreach($beneficiaries as $b)
                        <option value="{{ $b->id }}" {{ isset($prefillBeneficiary) && $prefillBeneficiary == $b->id ? 'selected' : '' }}>{{ $b->name }} @if($b->email) ({{ $b->email }}) @endif</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Amount to Send</label>
                <div class="input-group">
                    <input type="number" name="amount_sent" id="amount_sent" step="0.01" class="form-control" value="{{ old('amount_sent', $prefillAmount ?? '') }}" required onchange="updateSummary()" oninput="updateSummary()">
                    <select id="sendCurrency" name="send_currency_id" class="form-select" style="max-width: 150px;">
                        @php
                            if (!isset($currencies)) {
                                $currencies = \App\Models\Currency::all();
                            }
                            $userCurrency = Auth::user()->currency;
                            $defaultCurrencyId = $userCurrency?->id ?? \App\Models\Currency::where('code', 'USD')->value('id');
                        @endphp
                        @foreach($currencies as $c)
                            <option value="{{ $c->id }}" {{ old('send_currency_id', $defaultCurrencyId) == $c->id ? 'selected' : '' }}>
                                {{ $c->code }} ({{ $c->symbol }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <small class="text-muted d-block mt-2">2.5% commission will be added: <strong id="amount_commission">0.00</strong></small>
                <small class="text-muted d-block">USD Amount: <strong id="usdAmount">0.00</strong></small>
                <small class="text-muted d-block">Total to pay: <strong id="total_to_pay">0.00</strong></small>
            </div>

            @php
                if (!isset($currencies)) {
                    $currencies = \App\Models\Currency::all();
                }
            @endphp

            @if($currencies && $currencies->count())
            <div class="alert alert-info" role="alert">
                <strong>Currency options enabled:</strong> choose sender/receiver currencies below.
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Send Currency (optional)</label>
                    <select name="from_currency_id" class="form-select">
                        <option value="">-- Use default --</option>
                        @foreach($currencies as $c)
                            <option value="{{ $c->id }}" {{ old('from_currency_id') == $c->id ? 'selected' : '' }}>{{ $c->code }} — {{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Receive Currency (optional)</label>
                    <select name="to_currency_id" class="form-select">
                        <option value="">-- Use beneficiary country currency --</option>
                        @foreach($currencies as $c)
                            <option value="{{ $c->id }}" {{ old('to_currency_id') == $c->id ? 'selected' : '' }}>{{ $c->code }} — {{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <hr>
            @endif

            <hr>
            <h6>Card Details (Visa)</h6>
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Card Number</label>
                    <input type="text" name="card_number" class="form-control" placeholder="4111 1111 1111 1111" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Expiry Month</label>
                    <input type="number" name="expiry_month" class="form-control" min="1" max="12" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Expiry Year</label>
                    <input type="number" name="expiry_year" class="form-control" min="2025" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">CVV</label>
                    <input type="text" name="cvv" class="form-control" maxlength="3" required>
                </div>
            </div>

            <hr>
            <h6>Bank Account (for payout)</h6>
            <div class="mb-3">
                <label class="form-label">Bank Name</label>
                <input type="text" name="bank_name" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Account Holder</label>
                <input type="text" name="account_holder" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Account Number</label>
                <input type="text" name="account_number" class="form-control">
            </div>

            <div class="mt-3">
                <button class="btn btn-primary">Payment</button>
                <a href="{{ route('user.send') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
// Exchange rates cache
let exchangeRates = {};

async function getExchangeRate(fromCurrencyId, toCurrencyId) {
    const key = `${fromCurrencyId}->${toCurrencyId}`;
    if (exchangeRates[key] !== undefined) {
        return exchangeRates[key];
    }

    try {
        const response = await fetch(`/api/exchange-rate/${fromCurrencyId}/${toCurrencyId}`);
        const data = await response.json();
        if (data.rate) {
            exchangeRates[key] = data.rate;
            return data.rate;
        }
    } catch (e) {
        console.error('Failed to fetch exchange rate:', e);
    }
    return 1.0;
}

async function updateSummary() {
    const amountInput = document.getElementById('amount_sent');
    const currencySelect = document.getElementById('sendCurrency');
    const amount = parseFloat(amountInput.value) || 0;
    const commissionRate = 0.025; // 2.5%
    
    const usdCurrencyId = '{{ \App\Models\Currency::where("code", "USD")->value("id") }}';
    const selectedCurrencyId = currencySelect ? currencySelect.value : usdCurrencyId;
    
    let usdAmount = amount;
    
    // Convert to USD if different currency is selected
    if (selectedCurrencyId !== usdCurrencyId) {
        const rate = await getExchangeRate(selectedCurrencyId, usdCurrencyId);
        usdAmount = amount * rate;
    }
    
    const commission = usdAmount * commissionRate;
    const totalToPay = usdAmount + commission;
    
    document.getElementById('amount_commission').textContent = commission.toFixed(2);
    document.getElementById('usdAmount').textContent = usdAmount.toFixed(2);
    document.getElementById('total_to_pay').textContent = totalToPay.toFixed(2);
}

// Event listeners
document.getElementById('amount_sent').addEventListener('input', updateSummary);
document.getElementById('sendCurrency').addEventListener('change', updateSummary);

// Run on page load
document.addEventListener('DOMContentLoaded', function() {
    updateSummary();
});
</script>
@endsection
