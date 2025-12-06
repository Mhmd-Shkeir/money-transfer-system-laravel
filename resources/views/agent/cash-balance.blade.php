@extends('layouts.dashboard')

@section('title', 'Cash Balance & History - Agent')
@section('page-title', 'Cash Management')
@section('page-subtitle', 'View your cash balance and transaction history')

@section('content')

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Cash on Hand</h6>
                <h2 class="text-success mb-0">{{ number_format($agentProfile->cash_balance, 2) }}</h2>
                <small class="text-muted">Current Balance</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Total Cash In</h6>
                <h2 class="text-success mb-0">{{ number_format($agentProfile->total_cash_in, 2) }}</h2>
                <small class="text-muted">Received</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Total Cash Out</h6>
                <h2 class="text-danger mb-0">{{ number_format($agentProfile->total_cash_out, 2) }}</h2>
                <small class="text-muted">Paid Out</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Total Transactions</h6>
                <h2 class="mb-0">{{ $agentProfile->total_transactions }}</h2>
                <small class="text-muted">Processed</small>
            </div>
        </div>
    </div>
</div>

<!-- Transaction History -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-light">
        <h5 class="mb-0">Transaction History</h5>
    </div>
    <div class="card-body">
        @if($cashTransactions->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Reference</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cashTransactions as $txn)
                            <tr>
                                <td>
                                    <small>{{ $txn->created_at->format('M d, Y H:i') }}</small>
                                </td>
                                <td>
                                    @if($txn->transaction_type === 'cash_in')
                                        <span class="badge bg-success">Cash In</span>
                                    @elseif($txn->transaction_type === 'cash_out')
                                        <span class="badge bg-danger">Cash Out</span>
                                    @else
                                        <span class="badge bg-info">Transfer</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $txn->cash_reference ?? $txn->reference_code }}</strong>
                                </td>
                                <td>
                                    <span class="@if($txn->transaction_type === 'cash_in') text-success @elseif($txn->transaction_type === 'cash_out') text-danger @endif">
                                        @if($txn->transaction_type === 'cash_in')
                                            +
                                        @elseif($txn->transaction_type === 'cash_out')
                                            -
                                        @endif
                                        {{ number_format($txn->amount_sent, 2) }}
                                    </span>
                                </td>
                                <td>
                                    @if($txn->status === 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($txn->status === 'processing')
                                        <span class="badge bg-warning">Processing</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($txn->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($txn->notes)
                                        <small class="text-muted">{{ Str::limit($txn->notes, 30) }}</small>
                                    @else
                                        <small class="text-muted text-secondary">-</small>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $cashTransactions->links() }}
            </div>
        @else
            <div class="alert alert-info text-center py-4">
                <p class="mb-0">No cash transactions recorded yet.</p>
            </div>
        @endif
    </div>
</div>

@endsection
