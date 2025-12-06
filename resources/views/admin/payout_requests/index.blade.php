@extends('layouts.dashboard')

@section('title', 'Cash Payout Request - Admin')
@section('page-title', 'Payout Requests')
@section('page-subtitle', 'Manage agent payout requests')

@section('content')
<div class="container">

    <h2 class="mb-4">Agent Payout Requests</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Agent</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Requested At</th>
                <th>Notes</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            @foreach($requests as $r)
            <tr>
                <td>{{ $r->id }}</td>
                <td>{{ $r->agentUser->first_name }} {{ $r->agentUser->last_name }}</td>
                <td>${{ number_format($r->amount,2) }}</td>

                <td>
                    @if($r->status == 'pending')
                        <span class="badge bg-warning">Pending</span>
                    @elseif($r->status == 'approved')
                        <span class="badge bg-success">Approved</span>
                    @else
                        <span class="badge bg-danger">Rejected</span>
                    @endif
                </td>

                <td>{{ $r->created_at->format('Y-m-d H:i') }}</td>
                <td>{{ $r->agent_note }}</td>

                <td>
                    @if($r->status == 'pending')
                        <!-- APPROVE BUTTON -->
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#approveModal{{ $r->id }}">
                            Approve
                        </button>

                        <!-- REJECT BUTTON -->
                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $r->id }}">
                            Reject
                        </button>
                    @else
                        <em>No actions</em>
                    @endif
                </td>
            </tr>


            <!-- APPROVE MODAL -->
            <div class="modal fade" id="approveModal{{ $r->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('admin.payout.approve', $r->id) }}">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Approve Request #{{ $r->id }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <p>Amount: <strong>${{ number_format($r->amount,2) }}</strong></p>
                                <label>Admin Note (optional)</label>
                                <textarea class="form-control" name="admin_note"></textarea>
                            </div>

                            <div class="modal-footer">
                                <button class="btn btn-success">Approve</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>


            <!-- REJECT MODAL -->
            <div class="modal fade" id="rejectModal{{ $r->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('admin.payout.reject', $r->id) }}">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title text-danger">Reject Request #{{ $r->id }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <p>Are you sure you want to reject this request?</p>
                                <label>Admin Note (required)</label>
                                <textarea class="form-control" name="admin_note" required></textarea>
                            </div>

                            <div class="modal-footer">
                                <button class="btn btn-danger">Reject</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @endforeach
        </tbody>
    </table>

    {{ $requests->links() }}

</div>
@endsection
