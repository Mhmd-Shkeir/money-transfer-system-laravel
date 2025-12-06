@extends('layouts.dashboard')
@section('title', 'Transaction Receipt')
@section('page-title', 'Transaction Receipt')
@section('page-subtitle', 'Details for a single transaction')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Receipt — {{ $transaction->reference_code ?? 'N/A' }}</h5>
        <div class="d-flex gap-2">
            @if($transaction->payout_method === 'cash_pickup' && $transaction->status === 'pending' && $transaction->sender_id === Auth::id())
                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#refundRequestModal">
                    Request Refund
                </button>
            @endif
            <a href="{{ route('user.history') }}" class="btn btn-sm btn-outline-secondary">Back to history</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Reference:</strong> {{ $transaction->reference_code }}</p>
                <p><strong>Date:</strong> {{ optional($transaction->created_at)->format('M d, Y H:i') ?? '-' }}</p>
                <p><strong>Status:</strong> {{ ucfirst(str_replace('_',' ', $transaction->status ?? '')) }}</p>
                <p><strong>Payout Method:</strong> {{ ucfirst(str_replace('_',' ', $transaction->payout_method ?? '')) }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Sender:</strong> {{ $transaction->sender?->name ?? 'Unknown' }}</p>
                <p><strong>Recipient (beneficiary):</strong> {{ $transaction->beneficiary?->name ?? 'Unknown' }}</p>
                <p><strong>Payment Method:</strong> {{ $transaction->paymentMethod?->provider ?? 'N/A' }}</p>
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-4">
                <p><strong>Amount Sent</strong></p>
                <p>{{ $transaction->fromCurrency?->code ?? '—' }} {{ number_format($transaction->amount_sent ?? 0, 2) }}</p>
            </div>
            <div class="col-md-4">
                <p><strong>Fee</strong></p>
                <div class="text-danger">
                    {{ $transaction->fromCurrency?->code ?? '—' }} {{ number_format($transaction->fee ?? 0, 2) }}
                    @if($transaction->discount_amount > 0)
                        <div class="text-success" style="font-size: 0.9em;">
                            Discount: -{{ number_format($transaction->discount_amount, 2) }}
                            @if($transaction->offer)
                                <br><small>({{ $transaction->offer->name }})</small>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-md-4">
                <p><strong>Amount Received</strong></p>
                <p>{{ $transaction->toCurrency?->code ?? '—' }} {{ number_format($transaction->amount_received ?? 0, 2) }}</p>
            </div>
        </div>

        <hr>
        <div class="row">
            <div class="col-md-12">
                <p class="mb-0"><strong>Total Paid: </strong>
                    <span class="text-primary" style="font-size: 1.2em;">
                        {{ $transaction->fromCurrency?->code ?? 'USD' }} {{ number_format($transaction->total_paid ?? 0, 2) }}
                    </span>
                </p>
            </div>
        </div>

        @if(!empty($transaction->pickup_code))
            <hr>
            <p><strong>Pickup Code:</strong> {{ $transaction->pickup_code }}</p>
        @endif

        @if(!empty($transaction->fake_payment_id))
            <hr>
            <p class="text-muted">Payment reference (test): {{ $transaction->fake_payment_id }}</p>
        @endif
    </div>
</div>

@if($transaction->payout_method === 'cash_pickup' && $transaction->status === 'pending' && $transaction->sender_id === Auth::id())
<!-- Refund Request Modal -->
<div class="modal fade" id="refundRequestModal" tabindex="-1" aria-labelledby="refundRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="refundRequestModalLabel">Request Refund</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('refund-requests.store') }}">
                @csrf
                <div class="modal-body">
                    <p class="text-muted">
                        You are requesting a refund for transaction <strong>{{ $transaction->reference_code }}</strong>.
                        Please provide a reason for your refund request. An admin will review it shortly.
                    </p>
                    <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">
                    
                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason for Refund <span class="text-danger">*</span></label>
                        <textarea name="reason" id="reason" class="form-control" rows="4" placeholder="Please explain why you need a refund..." required minlength="10" maxlength="1000"></textarea>
                        <small class="text-muted">Minimum 10 characters</small>
                    </div>

                    <div class="alert alert-info" role="alert">
                        <strong>Note:</strong> Refunds are only available for pending cash pickup transactions. Since your payment hasn't been processed yet, approving a refund will simply cancel the transaction.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Submit Refund Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
