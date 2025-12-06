@extends('layouts.dashboard')

@section('title', 'Incoming Transfer Requests - Agent')
@section('page-title', 'Incoming Requests')
@section('page-subtitle', 'View and process transfer requests')

@section('content')

<div class="row">
    <div class="col-lg-8">
        <!-- Incoming Transfer Requests -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i data-lucide="inbox" style="width: 20px; height: 20px; display: inline-block; margin-right: 8px;"></i>
                    Pending Transfer Requests
                </h5>
            </div>
            <div class="card-body">
                @if($incomingRequests->count() > 0)
                    @foreach($incomingRequests as $request)
                        <div class="card mb-3 border-left border-info">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <h6 class="card-title mb-1">{{ $request->sender->first_name }} {{ $request->sender->last_name }}</h6>
                                        <p class="text-muted small mb-2">
                                            <strong>To:</strong> {{ $request->beneficiary->name }}<br>
                                            <strong>Amount:</strong> {{ number_format($request->amount_sent, 2) }}<br>
                                            <strong>Requested:</strong> {{ $request->created_at->format('M d, Y H:i') }}
                                        </p>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <div class="badge bg-warning text-dark mb-2">{{ $request->payout_method }}</div>
                                        <p class="text-muted small">Ref: {{ $request->reference_code }}</p>
                                    </div>
                                </div>
                                <div class="mt-3 pt-2 border-top">
                                    @php
                                        $canPay = ($agentProfile->cash_balance >= ($request->amount_sent ?? 0));
                                    @endphp

                                    <form method="POST" action="{{ route('agent.request.accept', $request->id) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" {{ $canPay ? '' : 'disabled' }}>
                                            <i class="fas fa-check me-1"></i>
                                            Accept & Process
                                        </button>
                                        @if(!$canPay)
                                            <small class="text-danger ms-2">Insufficient balance for payout</small>
                                        @endif
                                    </form>

                                    <form method="POST" action="{{ route('agent.request.reject', $request->id) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-times me-1"></i>
                                            Decline
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $incomingRequests->links() }}
                    </div>
                @else
                    <div class="alert alert-info text-center py-4">
                        <i data-lucide="inbox" style="width: 32px; height: 32px; display: inline-block; margin-bottom: 8px;"></i>
                        <p class="mb-0">No pending transfer requests at this time.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions Sidebar -->
    <div class="col-lg-4">
        <!-- Cash Balance Card -->
        <div class="card border-0 shadow-sm mb-4 border-left border-success">
            <div class="card-body">
                <h6 class="card-title text-success mb-2">
                    <i data-lucide="wallet" style="width: 20px; height: 20px; display: inline-block; margin-right: 6px;"></i>
                    Cash on Hand
                </h6>
                <h3 class="mb-0">{{ number_format($agentProfile->cash_balance, 2) }}</h3>
                <small class="text-muted">Current Balance</small>
                <hr>
                <div class="row text-center small">
                    <div class="col-6">
                        <p class="mb-1">Total In</p>
                        <strong class="text-success">{{ number_format($agentProfile->total_cash_in, 2) }}</strong>
                    </div>
                    <div class="col-6">
                        <p class="mb-1">Total Out</p>
                        <strong class="text-danger">{{ number_format($agentProfile->total_cash_out, 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Cash Operations -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light border-bottom">
                <h6 class="mb-0">Cash Operations</h6>
            </div>
            <div class="card-body">
                <a href="{{ route('agent.cash-balance') }}" class="btn btn-outline-primary btn-sm w-100 mb-2">
                    <i data-lucide="eye" style="width: 16px; height: 16px; display: inline-block; margin-right: 4px;"></i>
                    View Transactions
                </a>
                <button type="button" class="btn btn-success btn-sm w-100 mb-2" data-bs-toggle="modal" data-bs-target="#cashInModal">
                    <i data-lucide="plus-circle" style="width: 16px; height: 16px; display: inline-block; margin-right: 4px;"></i>
                    Cash In
                </button>
                <button type="button" class="btn btn-danger btn-sm w-100" data-bs-toggle="modal" data-bs-target="#cashOutModal">
                    <i data-lucide="minus-circle" style="width: 16px; height: 16px; display: inline-block; margin-right: 4px;"></i>
                    Cash Out
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Cash In Modal -->
<div class="modal fade" id="cashInModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h6 class="modal-title">Record Cash In</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('agent.cash-in') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Amount Received *</label>
                        <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" 
                               placeholder="0.00" min="0.01" step="0.01" required>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Customer Name *</label>
                        <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror" 
                               placeholder="Customer name" required>
                        @error('customer_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Customer Phone</label>
                        <input type="tel" name="customer_phone" class="form-control" placeholder="Phone number">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reference Code (Optional)</label>
                        <input type="text" name="reference" class="form-control" placeholder="e.g., INV-12345">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Add any notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Record Cash In</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cash Out Modal -->
<div class="modal fade" id="cashOutModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h6 class="modal-title">Record Cash Out</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('agent.cash-out') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Amount Paid Out *</label>
                        <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" 
                               placeholder="0.00" min="0.01" step="0.01" required>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Available: {{ number_format($agentProfile->cash_balance, 2) }}</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Recipient Name *</label>
                        <input type="text" name="recipient_name" class="form-control @error('recipient_name') is-invalid @enderror" 
                               placeholder="Recipient name" required>
                        @error('recipient_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Recipient Phone</label>
                        <input type="tel" name="recipient_phone" class="form-control" placeholder="Phone number">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reference Code (Optional)</label>
                        <input type="text" name="reference" class="form-control" placeholder="e.g., TXN-12345">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Add any notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Record Cash Out</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
