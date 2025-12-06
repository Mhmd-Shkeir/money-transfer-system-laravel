@extends('layouts.dashboard')

@section('title', 'Manage Agents - Admin Dashboard')
@section('page-title', 'Agent Applications')
@section('page-subtitle', 'Review and manage agent/partner store applications')

@section('content')
<div class="container-fluid">
    <!-- Pending Agent Applications -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Pending Agent Applications</h5>
                </div>
                <div class="card-body">
                    @php
                        $pendingAgents = \App\Models\AgentProfile::whereIn('is_approved', [0, null])->with('user')->get();
                    @endphp

                    @if($pendingAgents->count() > 0)
                        <div class="row g-3">
                            @foreach($pendingAgents as $profile)
                                <div class="col-md-6 col-lg-4">
                                    <div id="agent-card-{{ $profile->id }}" class="card h-100 border shadow-sm">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $profile->user->first_name ?? '' }} {{ $profile->user->last_name ?? '' }}</h6>
                                            <p class="card-text text-muted small mb-2">
                                                <strong>Store:</strong> {{ $profile->store_name }}<br>
                                                <strong>Address:</strong> {{ $profile->address }}<br>
                                                <strong>Email:</strong> {{ $profile->user->email ?? 'N/A' }}<br>
                                                <strong>Applied:</strong> {{ $profile->created_at->format('M j, Y') }}
                                            </p>
                                            <span class="badge bg-warning">Pending</span>
                                        </div>
                                        <div class="card-footer bg-light d-flex gap-2">
                                            <form method="POST" action="{{ route('admin.agents.approve', $profile->id) }}" class="approve-form" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">✓ Approve</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.agents.reject', $profile->id) }}" class="reject-form" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger">✕ Reject</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i data-lucide="check-circle" style="width: 20px; height: 20px; display: inline-block; margin-right: 8px;"></i>
                            No pending agent applications. All applications have been reviewed.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Approved Agents -->
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Approved Agents</h5>
                </div>
                <div class="card-body">
                    @php
                        $approvedAgents = \App\Models\AgentProfile::where('is_approved', 1)->with('user')->get();
                    @endphp

                    @if($approvedAgents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Agent Name</th>
                                        <th>Store Name</th>
                                        <th>Email</th>
                                        <th>Approved Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($approvedAgents as $profile)
                                        <tr>
                                            <td>{{ $profile->user->first_name ?? '' }} {{ $profile->user->last_name ?? '' }}</td>
                                            <td>{{ $profile->store_name }}</td>
                                            <td>{{ $profile->user->email ?? 'N/A' }}</td>
                                            <td>{{ $profile->updated_at->format('M j, Y') }}</td>
                                            <td><span class="badge bg-success">Active</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            No approved agents yet.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Rejected Agents -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Rejected Agents</h5>
                </div>
                <div class="card-body">
                    @php
                        $rejectedAgents = \App\Models\AgentProfile::where('is_approved', -1)->with('user')->get();
                    @endphp

                    @if($rejectedAgents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Agent Name</th>
                                        <th>Store Name</th>
                                        <th>Email</th>
                                        <th>Rejected Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rejectedAgents as $profile)
                                        <tr>
                                            <td>{{ $profile->user->first_name ?? '' }} {{ $profile->user->last_name ?? '' }}</td>
                                            <td>{{ $profile->store_name }}</td>
                                            <td>{{ $profile->user->email ?? 'N/A' }}</td>
                                            <td>{{ $profile->updated_at->format('M j, Y') }}</td>
                                            <td><span class="badge bg-danger">Rejected</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            No rejected agents.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            // Handle approve/reject form submissions
            document.querySelectorAll('.approve-form, .reject-form').forEach(form => {
                form.addEventListener('submit', function(e){
                    e.preventDefault();
                    
                    const url = this.action;
                    const button = this.querySelector('button[type=submit]');
                    button.disabled = true;
                    
                    // Build FormData with CSRF token
                    const fd = new FormData(this);
                    
                    fetch(url, {
                        method: 'POST',
                        body: fd,
                        credentials: 'same-origin',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => {
                        if(r.ok) return r.json();
                        throw new Error('Server returned status ' + r.status);
                    })
                    .then(data => {
                        if(data.success){
                            const cardId = 'agent-card-' + data.profile.id;
                            const card = document.getElementById(cardId);
                            if(card){
                                card.style.transition = 'opacity 300ms ease';
                                card.style.opacity = '0';
                                setTimeout(() => card.remove(), 300);
                            }
                            alert(data.message || 'Request processed successfully');
                            location.reload();
                        } else {
                            alert(data.message || 'Error: ' + JSON.stringify(data));
                            button.disabled = false;
                        }
                    })
                    .catch(err => {
                        alert('Error: ' + err.message);
                        button.disabled = false;
                    });
                });
            });
        });
    </script>
@endpush
