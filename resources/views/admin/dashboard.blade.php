@extends('layouts.dashboard')

@section('title', 'Admin Dashboard - Jaybtak Pro')
@section('page-title', 'Admin Dashboard')
@section('page-subtitle', 'System overview and analytics')

@section('content')
    <!-- Summary Cards - Database Driven -->
    <div class="row g-4 mb-4">
        @php
            // TODO: Replace with database queries
            // $totalRevenue = \App\Models\Transaction::sum('commission_amount');
            // $totalUsers = \App\Models\User::count();
            // $monthTransfers = \App\Models\Transaction::whereMonth('created_at', now()->month)->count();
            // $activeAgents = \App\Models\User::where('role', 'agent')->count();
        @endphp
        
        <div class="col-md-3">
            <div class="card stat-card h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="icon-box bg-primary-light mb-3">
                        <i data-lucide="trending-up" class="text-primary-custom" style="width: 24px; height: 24px;"></i>
                    </div>
                    <h3 class="mb-1">{{ $totalRevenue && $totalRevenue > 0 ? '$' . number_format($totalRevenue, 2) : '$0.00' }}</h3>
                    <p class="text-muted mb-2">Total Revenue</p>
                </div>
            </div>
        </div>

        <!-- <div class="col-md-3">
            <div class="card stat-card h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-3 p-3 mb-3" 
     style="background: #FFF4D6; width: 50px; height: 50px;">
    <i data-lucide="coins" style="width: 24px; height: 24px; color: #FFB200;"></i>
</div>
                    <h3 class="mb-1">{{ isset($adminCommissionTotal) ? '$' . number_format($adminCommissionTotal, 2) : '$0.00' }}</h3>
                    <p class="text-muted mb-2">Admin Commissions</p>
                </div>
            </div>
        </div> -->

        <div class="col-md-3">
            <div class="card stat-card h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="icon-box bg-success-light mb-3">
                        <i data-lucide="users" class="text-success-custom" style="width: 24px; height: 24px;"></i>
                    </div>
                    <h3 class="mb-1">{{ $totalUsers ?? 0 }}</h3>
                    <p class="text-muted mb-2">Total Users</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="icon-box bg-accent-light mb-3">
                        <i data-lucide="dollar-sign" style="width: 24px; height: 24px; color: var(--accent-color);"></i>
                    </div>
                    <h3 class="mb-1">{{ $monthTransfers ?? 0 }}</h3>
                    <p class="text-muted mb-2">Transfers (Month)</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="icon-box bg-danger-light mb-3">
                        <i data-lucide="user-check" style="width: 24px; height: 24px; color: var(--danger-color);"></i>
                    </div>
                    <h3 class="mb-1">{{ $activeAgents ?? 0 }}</h3>
                    <p class="text-muted mb-2">Active Agents</p>
                </div>
            </div>
        </div>
    </div>



    <!-- Recent Activity & Pending Approvals -->
    <div class="row g-4">
        <!-- Pending Agent Approvals (Left Column) -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-4">Pending Agent Approvals</h5>
                    
                    <div class="d-flex flex-column gap-3">
                        @if(isset($pendingAgents) && $pendingAgents->count() > 0)
                            @foreach($pendingAgents as $profile)
                                <div id="agent-card-{{ $profile->id }}" class="p-3 border rounded-3 pending-agent" style="border-color: #e0e0e0;">
                                    <div class="mb-3">
                                        <h6 class="mb-1">{{ $profile->user->name ?? 'Agent' }}</h6>
                                        <small class="text-muted d-block">{{ $profile->store_name ?? 'Store' }}</small>
                                        <small class="text-muted d-block">{{ $profile->address }}</small>
                                    </div>
                                    <div class="mb-3">
                                        <span class="badge bg-warning">Pending Approval</span>
                                        <small class="text-muted ms-2">Applied: {{ $profile->created_at->format('M j, Y') }}</small>
                                    </div>
                                    
                                    <!-- Approve & Reject Buttons Inside Card -->
                                    <div class="d-flex gap-2 pt-2 border-top">
                                        <form method="POST" action="{{ route('admin.agents.approve', $profile->id) }}" class="approve-form" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">✓ Approve</button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.agents.reject', $profile->id) }}" class="reject-form" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger">✕ Reject</button>
                                        </form>

                                        <a href="{{ route('admin.agents') }}" class="btn btn-sm btn-outline-secondary ms-auto">View All Agents</a>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="p-4 bg-light rounded-3 text-center text-muted">
                                <i data-lucide="check-circle" style="width: 40px; height: 40px; display: block; margin: 0 auto 10px; color: #999;"></i>
                                No pending agent applications
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent User Activity (Right Column) -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">Recent User Activity</h5>
                        <a href="{{ route('admin.users') }}" class="btn btn-link text-primary-custom text-decoration-none">
                            View All Users
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Last Active</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentUsers as $u)
                                    <tr>
                                        <td>{{ $u->name }}</td>
                                        <td>{{ $u->email }}</td>
                                        <td>{{ optional($u->last_login_at)->format('M j, Y H:i') ?? '—' }}</td>
                                        <td>
                                            @if($u->is_active ?? true)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.users') }}" class="btn btn-sm btn-outline-secondary">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i data-lucide="database" style="width: 32px; height: 32px; display: block; margin: 0 auto 10px;"></i>
                                            No recent users to show
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
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
                            // Show simple message (no fancy toast needed)
                            alert(data.message || 'Request processed successfully');
                            location.reload();
                        } else {
                            alert(data.message || 'Error: ' + JSON.stringify(data));
                            button.disabled = false;
                        }
                    })
                    .catch(err => {
                        alert('Error: ' + err.message + '\n\nIf the button works, the page will refresh after approval.');
                        button.disabled = false;
                    });
                });
            });
        });
    </script>
@endpush


