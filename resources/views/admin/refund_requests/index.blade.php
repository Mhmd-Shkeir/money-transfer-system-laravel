@extends('layouts.dashboard')
@section('title', 'Refund Requests Management')
@section('page-title', 'Refund Requests')
@section('page-subtitle', 'Review and manage customer refund requests')
@section('content')

<style>
    .pagination {
        font-size: 0.95rem;
    }
    
    .pagination .page-link {
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
    }
    
    .pagination .page-item.disabled .page-link {
        cursor: not-allowed;
        opacity: 0.5;
    }
</style>

<ul class="nav nav-tabs mb-4" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
            Pending <span class="badge bg-warning">{{ $pendingRequests->total() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="resolved-tab" data-bs-toggle="tab" data-bs-target="#resolved" type="button" role="tab">
            Resolved <span class="badge bg-secondary">{{ $resolvedRequests->total() }}</span>
        </button>
    </li>
</ul>

<div class="tab-content">
    <!-- Pending Refund Requests Tab -->
    <div class="tab-pane fade show active" id="pending" role="tabpanel">
        @if($pendingRequests->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Transaction</th>
                            <th>Amount</th>
                            <th>Reason</th>
                            <th>Requested</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingRequests as $request)
                            <tr>
                                <td><strong>#{{ $request->id }}</strong></td>
                                <td>
                                    <a href="{{ route('admin.users.show', $request->user) }}">
                                        {{ $request->user->first_name }} {{ $request->user->last_name }}
                                    </a>
                                    <br>
                                    <small class="text-muted">{{ $request->user->email }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('transactions.view', $request->transaction) }}">
                                        {{ $request->transaction->reference_code }}
                                    </a>
                                </td>
                                <td>
                                    <strong>${{ number_format($request->transaction->total_paid, 2) }}</strong>
                                </td>
                                <td>
                                    <small>{{ Str::limit($request->reason, 50) }}</small>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $request->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('refund-requests.show', $request) }}" class="btn btn-sm btn-primary">
                                        Review
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $pendingRequests->links() }}
            </div>
        @else
            <div class="alert alert-info" role="alert">
                No pending refund requests at this time.
            </div>
        @endif
    </div>

    <!-- Resolved Refund Requests Tab -->
    <div class="tab-pane fade" id="resolved" role="tabpanel">
        @if($resolvedRequests->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Transaction</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Reviewed By</th>
                            <th>Reviewed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($resolvedRequests as $request)
                            <tr>
                                <td><strong>#{{ $request->id }}</strong></td>
                                <td>
                                    <a href="{{ route('admin.users.show', $request->user) }}">
                                        {{ $request->user->first_name }} {{ $request->user->last_name }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('transactions.view', $request->transaction) }}">
                                        {{ $request->transaction->reference_code }}
                                    </a>
                                </td>
                                <td>
                                    <strong>${{ number_format($request->transaction->total_paid, 2) }}</strong>
                                </td>
                                <td>
                                    @if($request->status === 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                                <td>
                                    @if($request->reviewer)
                                        {{ $request->reviewer->first_name }} {{ $request->reviewer->last_name }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $request->reviewed_at?->diffForHumans() }}</small>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $resolvedRequests->links() }}
            </div>
        @else
            <div class="alert alert-info" role="alert">
                No resolved refund requests.
            </div>
        @endif
    </div>
</div>

@endsection
