@extends('layouts.dashboard')
@section('title', 'Refund Request Details')
@section('page-title', 'Refund Request #' . $refundRequest->id)
@section('page-subtitle', 'Review and decide on this refund request')
@section('content')

<div class="row">
    <!-- Refund Request Details -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Refund Request Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Status</p>
                        <p>
                            @if($refundRequest->status === 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($refundRequest->status === 'approved')
                                <span class="badge bg-success">Approved</span>
                            @else
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Requested On</p>
                        <p>{{ $refundRequest->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>

                <hr>

                <div class="mb-3">
                    <p class="text-muted mb-1">Customer Reason</p>
                    <div class="p-3 bg-light rounded">
                        {{ $refundRequest->reason }}
                    </div>
                </div>

                @if($refundRequest->admin_notes)
                    <hr>
                    <div class="mb-3">
                        <p class="text-muted mb-1">Admin Notes</p>
                        <div class="p-3 bg-light rounded">
                            {{ $refundRequest->admin_notes }}
                        </div>
                    </div>
                @endif

                @if($refundRequest->reviewed_at)
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Reviewed By</p>
                            <p>{{ $refundRequest->reviewer?->first_name }} {{ $refundRequest->reviewer?->last_name }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Reviewed At</p>
                            <p>{{ $refundRequest->reviewed_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Transaction Details -->
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Transaction Details</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Reference Code</p>
                        <p><strong>{{ $refundRequest->transaction->reference_code }}</strong></p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Status</p>
                        <p>
                            <span class="badge bg-{{ $refundRequest->transaction->status === 'pending' ? 'warning' : ($refundRequest->transaction->status === 'completed' ? 'success' : 'danger') }}">
                                {{ ucfirst($refundRequest->transaction->status) }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Sender</p>
                        <p>
                            <a href="{{ route('admin.users.show', $refundRequest->transaction->sender) }}">
                                {{ $refundRequest->transaction->sender->first_name }} {{ $refundRequest->transaction->sender->last_name }}
                            </a>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Recipient</p>
                        <p>{{ $refundRequest->transaction->beneficiary?->name ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <p class="text-muted mb-1">Amount Sent</p>
                        <p><strong>{{ $refundRequest->transaction->fromCurrency?->code ?? 'USD' }} {{ number_format($refundRequest->transaction->amount_sent, 2) }}</strong></p>
                    </div>
                    <div class="col-md-4">
                        <p class="text-muted mb-1">Fee</p>
                        <p><strong class="text-danger">{{ $refundRequest->transaction->fromCurrency?->code ?? 'USD' }} {{ number_format($refundRequest->transaction->fee, 2) }}</strong></p>
                    </div>
                    <div class="col-md-4">
                        <p class="text-muted mb-1">Total Paid</p>
                        <p><strong class="text-primary">{{ $refundRequest->transaction->fromCurrency?->code ?? 'USD' }} {{ number_format($refundRequest->transaction->total_paid, 2) }}</strong></p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Payout Method</p>
                        <p>{{ ucfirst(str_replace('_', ' ', $refundRequest->transaction->payout_method)) }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Created</p>
                        <p>{{ $refundRequest->transaction->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Panel -->
    <div class="col-md-4">
            <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Decision</h5>
            </div>
            <div class="card-body">
                @if($refundRequest->status === 'pending')
                    <div class="alert alert-info" role="alert">
                        <strong>Pending Review</strong>
                        <p class="mb-0 mt-2">Choose to approve or reject this refund request. If approved, the transaction will be cancelled. <em>Note: Since the transaction is still pending, no money has been deducted from the customer yet.</em></p>
                    </div>

                    <!-- Approve Form -->
                    <form method="POST" action="{{ route('refund-requests.approve', $refundRequest) }}" class="mb-3">
                        @csrf
                        <div class="mb-3">
                            <label for="approve_notes" class="form-label">Admin Notes (Optional)</label>
                            <textarea name="admin_notes" id="approve_notes" class="form-control" rows="3" placeholder="Enter any notes for this approval..." maxlength="500"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100" onclick="return confirm('Approve this refund? The transaction will be cancelled.');">
                            <i class="fas fa-check"></i> Approve Refund
                        </button>
                    </form>                    <!-- Reject Form -->
                    <form method="POST" action="{{ route('refund-requests.reject', $refundRequest) }}">
                        @csrf
                        <div class="mb-3">
                            <label for="reject_notes" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                            <textarea name="admin_notes" id="reject_notes" class="form-control" rows="3" placeholder="Explain why you're rejecting this refund..." required minlength="10" maxlength="500"></textarea>
                            <small class="text-muted">The customer will see this reason</small>
                        </div>
                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Reject this refund request?');">
                            <i class="fas fa-times"></i> Reject Refund
                        </button>
                    </form>
                @else
                    <div class="alert alert-secondary" role="alert">
                        <strong>Already Reviewed</strong>
                        <p class="mb-0 mt-2">This refund request has already been reviewed and cannot be modified.</p>
                    </div>

                    <div class="text-center">
                        @if($refundRequest->status === 'approved')
                            <span class="badge bg-success" style="font-size: 1.1em;">✓ Approved</span>
                        @else
                            <span class="badge bg-danger" style="font-size: 1.1em;">✗ Rejected</span>
                        @endif
                    </div>
                @endif
            </div>
            <div class="card-footer">
                <a href="{{ route('refund-requests.index') }}" class="btn btn-sm btn-outline-secondary w-100">
                    Back to List
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
