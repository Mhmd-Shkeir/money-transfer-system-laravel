@extends('layouts.dashboard')
@section('title','Bank Deposit Confirmation')
@section('page-title','Payment Authorized')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="mb-4">✅ Payment Authorized</h5>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <p><strong>Transaction ID:</strong> {{ $transaction->id }}</p>
                <p><strong>Reference Code:</strong> {{ $transaction->reference_code }}</p>
                <p><strong>Status:</strong> <span class="badge bg-success">{{ ucfirst($transaction->status) }}</span></p>
            </div>
            <div class="col-md-6">
                <p><strong>Payment ID:</strong> {{ $fakePaymentId }}</p>
                <p><strong>Date:</strong> {{ $transaction->created_at->format('M d, Y H:i') }}</p>
            </div>
        </div>

        <hr>
        <h6 class="mb-3">Transaction Details</h6>
        
        <table class="table table-sm">
            <tbody>
                <tr>
                    <td><strong>Beneficiary:</strong></td>
                    <td>{{ $transaction->beneficiary->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Amount Sent:</strong></td>
                    <td>{{ number_format($transaction->amount_sent, 2) }} {{ $transaction->fromCurrency->code ?? 'USD' }}</td>
                </tr>
                <tr class="table-warning">
                    <td><strong>Commission (2.5%):</strong></td>
                    <td><strong>{{ number_format($transaction->fee, 2) }} {{ $transaction->fromCurrency->code ?? 'USD' }}</strong></td>
                </tr>
                @if($transaction->exchange_rate != 1.0)
                <tr>
                    <td><strong>Exchange Rate:</strong></td>
                    <td>1 {{ $transaction->fromCurrency->code ?? 'USD' }} = {{ number_format($transaction->exchange_rate, 4) }} {{ $transaction->toCurrency->code ?? 'USD' }}</td>
                </tr>
                <tr>
                    <td><strong>Amount Received:</strong></td>
                    <td>{{ number_format($transaction->amount_received, 2) }} {{ $transaction->toCurrency->code ?? 'USD' }}</td>
                </tr>
                @endif
                <tr class="table-info">
                    <td><strong>Total Paid:</strong></td>
                    <td><strong>{{ number_format($transaction->total_paid, 2) }} {{ $transaction->fromCurrency->code ?? 'USD' }}</strong></td>
                </tr>
            </tbody>
        </table>

        <hr>
        <a href="{{ route('user.dashboard') }}" class="btn btn-primary">Back to Dashboard</a>
        <a href="{{ route('user.history') }}" class="btn btn-outline-primary">View Transaction History</a>
    </div>
</div>
@endsection
