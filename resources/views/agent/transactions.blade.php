@extends('layouts.dashboard')
@section('title', 'Transaction Processing - Agent')
@section('page-title', 'Transaction Processing')
@section('page-subtitle', 'Process transactions in your operating currency')
@section('content')
@php
    // Ensure $agent is available in the view. Some routes render this view
    // via a generic view controller and don't pass $agent explicitly.
    // Fall back to the authenticated user's agent profile when possible.
    $agent = isset($agent) ? $agent : (auth()->check() ? auth()->user()->agentProfile : null);
@endphp
<div class="row g-4">
    <!-- Operating Currency Information -->
    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0">Operating Currency</h6>
            </div>
            <div class="card-body">
                @if($agent->currency)
                    <div class="text-center mb-3">
                        <h3 class="badge bg-info text-dark" style="font-size: 1.5em;">{{ $agent->currency->code }}</h3>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Currency Name</small>
                        <p class="mb-0"><strong>{{ $agent->currency->name }}</strong></p>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Currency Symbol</small>
                        <p class="mb-0"><strong>{{ $agent->currency->symbol }}</strong></p>
                    </div>
                    <div>
                        <small class="text-muted">Total Volume</small>
                        <p class="mb-0"><strong>{{ $agent->currency->symbol ?? '$' }}{{ number_format($totalVolume ?? 0, 2) }}</strong></p>
                    </div>
                @else
                    <div class="alert alert-warning mb-0">
                        <small>No operating currency set. Please update your profile.</small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Transaction Processing -->
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">Transaction Processing</h5>
            </div>
            <div class="card-body">
                @if($agent->currency)
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Transactions are processed in {{ $agent->currency->name }} ({{ $agent->currency->code }})</strong>
                        <br>
                        <small>All amounts on this page are displayed with the {{ $agent->currency->symbol }} symbol.</small>
                    </div>

                    <!-- Transaction Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Amount ({{ $agent->currency->code }})</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($transactions) && $transactions->count())
                                    @foreach($transactions as $txn)
                                        <tr>
                                            <td><small>{{ $txn->created_at->format('M d, Y H:i') }}</small></td>
                                            <td>
                                                @if($txn->transaction_type === 'transfer')
                                                    Transfer from {{ optional($txn->sender)->first_name }} {{ optional($txn->sender)->last_name }}
                                                @elseif($txn->transaction_type === 'cash_in')
                                                    Cash In
                                                @elseif($txn->transaction_type === 'cash_out')
                                                    Cash Out
                                                @else
                                                    {{ ucfirst($txn->transaction_type) }}
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $symbol = $agent->currency->symbol ?? '$';
                                                @endphp
                                                <span class="@if($txn->transaction_type==='cash_in') text-success @elseif($txn->transaction_type==='cash_out') text-danger @endif">
                                                    @if($txn->transaction_type === 'cash_in') + @elseif($txn->transaction_type === 'cash_out') - @endif
                                                    {{ $symbol }}{{ number_format($txn->amount_sent, 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($txn->status === 'completed')
                                                    <span class="badge bg-success">Completed</span>
                                                @elseif($txn->status === 'processing')
                                                    <span class="badge bg-warning">Processing</span>
                                                @elseif($txn->status === 'pending')
                                                    <span class="badge bg-secondary">Pending</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($txn->status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($txn->status === 'processing' && $txn->transaction_type === 'transfer')
                                                    <form method="POST" action="{{ route('agent.cash-out') }}" style="display:inline-block">
                                                        @csrf
                                                        <input type="hidden" name="transaction_id" value="{{ $txn->id }}">
                                                        <input type="hidden" name="amount" value="{{ $txn->amount_sent }}">
                                                        <input type="hidden" name="recipient_name" value="{{ $txn->beneficiary->name ?? '' }}">
                                                        <button type="submit" class="btn btn-sm btn-danger">Complete Payout</button>
                                                    </form>
                                                @else
                                                    <small class="text-muted">-</small>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4"><small>No transactions yet</small></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <hr>

                    <!-- Quick Stats -->
                    <div class="row g-3 mt-3">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted">Total Transactions</small>
                                <p class="h5 mb-0">{{ $totalTransactions ?? 0 }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted">Total Volume</small>
                                <p class="h5 mb-0">{{ $agent->currency->symbol ?? '$' }}{{ number_format($totalVolume ?? 0, 2) }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted">Pending</small>
                                <p class="h5 mb-0">{{ $pendingCount ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <h5 class="alert-heading">
                            <i class="fas fa-exclamation-triangle me-2"></i>Configuration Required
                        </h5>
                        <p class="mb-0">
                            To process transactions, you need to set your operating currency first.
                            <br>
                            <a href="{{ route('agent.profile') }}" class="btn btn-sm btn-warning mt-2">Go to Profile</a>
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
