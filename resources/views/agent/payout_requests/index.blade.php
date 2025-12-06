@extends('layouts.dashboard')

@section('title', 'Cash Payout Request - Agent')
@section('page-title', 'Payout Requests')
@section('page-subtitle', 'Request cash payouts and view your payout history')

@section('content')
<div class="container">

    <h2 class="mb-4">My Payout Requests</h2>

    {{-- SUCCESS & ERROR MESSAGES --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <!-- REQUEST NEW PAYOUT -->
    <div class="card mb-4">
        <div class="card-header">
            <strong>Request Payout</strong>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('agent.payout.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Amount</label>
                    <input type="number" step="0.01" name="amount" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Notes (optional)</label>
                    <textarea name="agent_note" class="form-control"></textarea>
                </div>

                <button class="btn btn-primary">Submit Request</button>
            </form>
        </div>
    </div>



    <!-- PENDING REQUESTS -->
    <h4 class="mt-5">Pending Requests</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pending as $req)
                <tr>
                    <td>{{ $req->id }}</td>
                    <td>${{ number_format($req->amount,2) }}</td>
                    <td><span class="badge bg-warning">Pending</span></td>
                    <td>{{ $req->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $req->agent_note }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">No pending requests</td></tr>
            @endforelse
        </tbody>
    </table>


    <!-- HISTORY -->
    <h4 class="mt-5">History (Approved/Rejected)</h4>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Admin Notes</th>
                <th>Approved By</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($history as $req)
                <tr>
                    <td>{{ $req->id }}</td>
                    <td>${{ number_format($req->amount,2) }}</td>
                    <td>
                        @if($req->status == 'approved')
                            <span class="badge bg-success">Approved</span>
                        @else
                            <span class="badge bg-danger">Rejected</span>
                        @endif
                    </td>
                    <td>{{ $req->admin_note }}</td>
                    <td>{{ $req->approvedBy->first_name ?? '—' }}</td>
                    <td>{{ $req->approved_at->format('Y-m-d H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">No history</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $history->links() }}
</div>
@endsection
