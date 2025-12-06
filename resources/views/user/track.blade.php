@extends('layouts.app')

@section('title', 'Track Your Transfer')

@section('content')
<div class="container py-5">
    <h1 class="mb-4 text-primary">Track Your Transfer</h1>
    <p class="text-secondary mb-4">
        Enter your transaction reference below to track the current status of your money transfer.
    </p>

    <form id="track-form" class="row g-3">
        <div class="col-md-8">
            <input type="text" id="ref-input" name="ref" class="form-control" placeholder="Enter Reference Code" value="{{ $prefill ?? '' }}">
        </div>
        <div class="col-md-4">
            <button type="submit" id="track-btn" class="btn btn-primary w-100">Track Now</button>
        </div>
    </form>

    <div id="track-result" class="mt-5">
        <div id="idle-message" class="text-muted">
            <p>Once you submit a reference code, the current status and details will appear here and update in real-time.</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    const form = document.getElementById('track-form');
    const refInput = document.getElementById('ref-input');
    const resultEl = document.getElementById('track-result');
    const idle = document.getElementById('idle-message');
    let pollInterval = null;

    function renderTransaction(data) {
        if (idle) { idle.remove(); }
        resultEl.innerHTML = `
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Reference: ${data.reference_code}</h5>
                    <p><strong>Status:</strong> ${data.status}</p>
                    <p><strong>Amount Sent:</strong> ${data.from_currency || '—'} ${Number(data.amount_sent || 0).toFixed(2)}</p>
                    <p><strong>Amount Received:</strong> ${data.to_currency || '—'} ${Number(data.amount_received || 0).toFixed(2)}</p>
                    <p><strong>Payout Method:</strong> ${data.payout_method}</p>
                    <p><strong>Sender:</strong> ${data.sender || '—'}</p>
                    <p><strong>Recipient:</strong> ${data.beneficiary || '—'}</p>
                    ${data.pickup_code ? `<p><strong>Pickup Code:</strong> ${data.pickup_code}</p>` : ''}
                </div>
            </div>
        `;


    }

    function renderNotFound() {
        if (idle) { idle.remove(); }
        resultEl.innerHTML = `<div class="alert alert-warning">Transaction not found. Check the reference and try again.</div>`;
    }

    function fetchStatus(ref) {
        if (!ref || !ref.trim()) {
            resultEl.innerHTML = `<div class="alert alert-info">Please enter a reference code.</div>`;
            return;
        }

        fetch(`/track-transfer/check?ref=${encodeURIComponent(ref)}`)
            .then(res => res.json())
            .then(json => {
                if (!json.found) {
                    renderNotFound();
                    return;
                }

                renderTransaction(json);

                if (['completed','failed','cancelled'].includes((json.status || '').toLowerCase())) {
                    stopPolling();
                }
            })
            .catch(err => {
                console.error(err);
                resultEl.innerHTML = `<div class="alert alert-danger">Error fetching status.</div>`;
            });
    }

    function startPolling(ref) {
        stopPolling();
        fetchStatus(ref);
        pollInterval = setInterval(() => fetchStatus(ref), 5000);
    }

    function stopPolling() {
        if (pollInterval) {
            clearInterval(pollInterval);
            pollInterval = null;
        }
    }

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const ref = refInput.value;
        startPolling(ref);
    });

    const pref = refInput.value || '';
    if (pref.trim()) {
        startPolling(pref.trim());
    }
})();
</script>
@endpush

@endsection
