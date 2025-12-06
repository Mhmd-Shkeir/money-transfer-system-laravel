@extends('layouts.dashboard')
@section('title', 'Send Money - User')
@section('page-title', 'Send Money')
@section('page-subtitle', 'Send money internationally')
@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Send Money</h5>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Validation Errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('user.send.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Amount to Send <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="number" step="0.01" id="amount_sent" name="amount_sent" class="form-control @error('amount_sent') is-invalid @enderror" value="{{ old('amount_sent') }}" required>
                    <select id="sendCurrency" name="send_currency_id" class="form-select" style="max-width: 150px;">
                        @php
                            $currencies = \App\Models\Currency::all();
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
                @error('amount_sent')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <small class="text-muted d-block mt-2">
                    <span id="currencyDisplay">Amount in USD: <strong id="usdAmount">0.00</strong></span>
                </small>
            </div>

            {{-- Send/receive currency selection removed. Sender currency is sent as a hidden field. --}}
            <input type="hidden" name="from_currency_id" value="{{ old('from_currency_id', $fromCurrencyId ?? '') }}">

            <div class="mb-3">
                <label class="form-label">Choose Existing Beneficiary (optional)</label>
                <select name="beneficiary_id" class="form-select @error('beneficiary_id') is-invalid @enderror">
                    <option value="">-- New Beneficiary --</option>
                    @if(isset($beneficiaries) && $beneficiaries->count())
                        @foreach($beneficiaries as $b)
                            <option value="{{ $b->id }}" {{ old('beneficiary_id') == $b->id ? 'selected' : '' }}>{{ $b->name }} ({{ $b->email ?? 'no email' }})</option>
                        @endforeach
                    @endif
                </select>
                @error('beneficiary_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <hr>
            <h6>New Beneficiary Details (if no existing beneficiary selected)</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="beneficiary_name" class="form-control @error('beneficiary_name') is-invalid @enderror" value="{{ old('beneficiary_name') }}">
                    @error('beneficiary_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="beneficiary_email" class="form-control @error('beneficiary_email') is-invalid @enderror" value="{{ old('beneficiary_email') }}">
                    @error('beneficiary_email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" name="beneficiary_phone" class="form-control @error('beneficiary_phone') is-invalid @enderror" value="{{ old('beneficiary_phone') }}">
                    @error('beneficiary_phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Payout Method</label>
                    <select name="payout_method" class="form-select">
                        <option value="mobile_wallet" {{ old('payout_method') === 'mobile_wallet' ? 'selected' : '' }}>Mobile Wallet</option>
                        <option value="cash_pickup" {{ old('payout_method') === 'cash_pickup' ? 'selected' : '' }}>Cash Pickup</option>
                        <option value="bank_deposit" {{ old('payout_method') === 'bank_deposit' ? 'selected' : '' }}>Bank Deposit</option>
                    </select>
                </div>
            </div>

            <div class="mt-3" id="bank-deposit-cta" style="display:none;">
                <a id="bankDepositLink" href="#" class="btn btn-success">Proceed to Bank Deposit Payment</a>
            </div>



            <div class="mt-4">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div>
                            <small class="text-muted">Commission Rate</small>
                            <div id="commissionRate" class="text-muted">2.50%</div>
                            <div id="commissionDisplay" class="fw-semibold">$0.00</div>
                        </div>
                    <div>
                        <small class="text-muted">Total To Pay</small>
                        <div id="totalDisplay" class="fw-semibold">$0.00</div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Create Transaction</button>
                <a href="{{ route('user.dashboard') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<script>
    (function(){
        const payoutSelect = document.querySelector('select[name="payout_method"');
        const cta = document.getElementById('bank-deposit-cta');
        const link = document.getElementById('bankDepositLink');
        const amountInput = document.getElementById('amount_sent');
        const beneficiarySelect = document.querySelector('select[name="beneficiary_id"]');
        const commissionDisplay = document.getElementById('commissionDisplay');
        const totalDisplay = document.getElementById('totalDisplay');
        const currencySelect = document.getElementById('sendCurrency');
        const usdAmountDisplay = document.getElementById('usdAmount');

        // Exchange rates cache
        let exchangeRates = {};
        // Currently selected applicable offer for the USD amount
        let currentOffer = null;
        const offersEndpoint = '{{ route("offers.for-amount") }}';

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
            return 1.0; // Default fallback
        }

        async function fetchOfferForAmount(amount) {
            currentOffer = null;
            if (!amount || isNaN(amount) || Number(amount) <= 0) return;

            try {
                const url = offersEndpoint + '?amount=' + encodeURIComponent(Number(amount).toFixed(2));
                const resp = await fetch(url, { credentials: 'same-origin' });
                const data = await resp.json();
                if (data && data.success && data.offer) {
                    currentOffer = data.offer;
                }
            } catch (e) {
                console.error('Failed to fetch offer:', e);
            }
        }

        async function updateCurrencyDisplay() {
            const selectedCurrencyId = currencySelect ? currencySelect.value : null;
            const amount = parseFloat(amountInput && amountInput.value ? amountInput.value : 0) || 0;

            if (selectedCurrencyId && selectedCurrencyId !== '') {
                // Get USD currency ID
                const usdCurrencyId = '{{ \App\Models\Currency::where("code", "USD")->value("id") }}';

                if (selectedCurrencyId !== usdCurrencyId) {
                    const rate = await getExchangeRate(selectedCurrencyId, usdCurrencyId);
                    const usdAmount = (amount * rate).toFixed(2);
                    usdAmountDisplay.textContent = usdAmount;

                    // Fetch applicable offer for this USD amount, then update CTA
                    await fetchOfferForAmount(usdAmount);
                    updateCTA(usdAmount);
                } else {
                    usdAmountDisplay.textContent = amount.toFixed(2);
                    await fetchOfferForAmount(amount);
                    updateCTA(amount);
                }
            }
        }

        function updateCTA(usdAmount = null){
            if(!payoutSelect) return;

            // Use provided USD amount or calculate from input
            let amount = usdAmount !== null ? parseFloat(usdAmount) : parseFloat(amountInput && amountInput.value ? amountInput.value : 0) || 0;

            let commission = 0.00;
            // Determine commission rate and apply any active offer discount
            const baseCommissionPercent = 2.5; // percent
            let commissionRatePercent = 0.0;

            if (payoutSelect.value === 'cash_pickup' || payoutSelect.value === 'bank_deposit') {
                commissionRatePercent = baseCommissionPercent;
                if (currentOffer && currentOffer.discount_percentage) {
                    // discount_percentage is the percent of the commission to keep (e.g., 50 -> keep 50% of commission)
                    commissionRatePercent = parseFloat((baseCommissionPercent * (parseFloat(currentOffer.discount_percentage) / 100.0)).toFixed(2));
                }
                commission = parseFloat((amount * (commissionRatePercent / 100.0)).toFixed(2));
            } else if (payoutSelect.value === 'mobile_wallet') {
                commissionRatePercent = 0.00;
                commission = 0.00;
            }

            // Update commission rate display
            const commissionRateElem = document.getElementById('commissionRate');
            if (commissionRateElem) {
                commissionRateElem.textContent = commissionRatePercent.toFixed(2) + '%';
            }

            const total = (amount + commission).toFixed(2);
            commissionDisplay && (commissionDisplay.textContent = '$' + commission.toFixed(2));
            totalDisplay && (totalDisplay.textContent = '$' + total);

            if(payoutSelect.value === 'bank_deposit'){
                cta.style.display = 'block';
                const params = new URLSearchParams();
                if(amountInput && amountInput.value) params.set('amount_sent', amountInput.value);
                if(beneficiarySelect && beneficiarySelect.value) params.set('beneficiary_id', beneficiarySelect.value);
                link.href = '{{ route("user.send.bank_deposit") }}' + (params.toString() ? ('?'+params.toString()) : '');
            } else {
                cta.style.display = 'none';
            }
        }

        if(payoutSelect){
            payoutSelect.addEventListener('change', updateCTA);
            updateCTA();
        }

        if(amountInput){
            amountInput.addEventListener('input', updateCurrencyDisplay);
        }

        if(currencySelect){
            currencySelect.addEventListener('change', updateCurrencyDisplay);
            updateCurrencyDisplay();
        }

        if(beneficiarySelect){
            beneficiarySelect.addEventListener('change', updateCTA);
        }
    })();
</script>
@endsection


