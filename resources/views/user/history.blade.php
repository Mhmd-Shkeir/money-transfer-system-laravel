@extends('layouts.dashboard')
@section('title', 'Transaction History - User')
@section('page-title', 'Transaction History')
@section('page-subtitle', 'View your transaction history')
@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Transaction History</h5>
    </div>
    <div class="card-body">
        @if(isset($transactions) && $transactions->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Reference</th>
                            <th>Amount Sent</th>
                            <th>Fee</th>
                            <th>Amount Received</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                        @php
                            $isOutgoing = auth()->check() && auth()->id() === $transaction->sender_id;
                        @endphp
                        <tr>
                            <td>{{ optional($transaction->created_at)->format('M d, Y H:i') ?? '-' }}</td>
                            <td><span class="badge {{ $isOutgoing ? 'bg-danger' : 'bg-success' }}">{{ $isOutgoing ? 'Sent' : 'Received' }}</span></td>
                            <td>{{ $transaction->reference_code }}</td>
                            <td>
                                <strong>{{ $transaction->fromCurrency?->code ?? 'USD' }}</strong><br>
                                {{ number_format($transaction->amount_sent ?? 0, 2) }}
                            </td>
                            <td>
                                <span class="text-danger">
                                    <strong>{{ number_format($transaction->fee ?? 0, 2) }}</strong>
                                    @if($transaction->discount_amount > 0)
                                        <br><small class="text-success">-{{ number_format($transaction->discount_amount, 2) }}</small>
                                    @endif
                                </span>
                            </td>
                            <td>
                                <strong>{{ $transaction->toCurrency?->code ?? 'USD' }}</strong><br>
                                {{ number_format($transaction->amount_received ?? 0, 2) }}
                            </td>
                            <td>{{ ucfirst(str_replace('_',' ', $transaction->status ?? '')) }}</td>
                            <td>
                                <a href="{{ route('user.history.receipt', $transaction->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted">No transactions found. Once you send or receive money, they will appear here.</p>
        @endif
    </div>
</div>
@endsection
