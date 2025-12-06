@extends('layouts.dashboard')

@section('title', 'Notifications')

@section('content')
<div class="container-fluid py-4">

    <h2 class="mb-1">Admin Notifications</h2>
    <p class="text-muted mb-4">System activity including users, transactions and agents.</p>

    <div class="row g-4">

        {{-- NEW USERS --}}
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">Newly Registered Users</h5>
                </div>

                <div class="card-body p-0">
                    @if($recentUsers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Registered</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentUsers as $user)
                                        <tr>
                                            <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td class="text-capitalize">
                                                <span class="badge bg-secondary">{{ $user->role }}</span>
                                            </td>
                                            <td>{{ optional($user->created_at)->diffForHumans() }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-muted py-4">No recent users.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- RECENT TRANSACTIONS --}}
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">Recent Transactions</h5>
                </div>

                <div class="card-body p-0">
                    @if($recentTransactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Ref</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTransactions as $t)
                                        <tr>
                                            <td>{{ $t->reference_code }}</td>
                                            <td>
                                                @if($t->sender)
                                                    {{ $t->sender->first_name }} {{ $t->sender->last_name }}
                                                    <br>
                                                    <small class="text-muted">{{ $t->sender->email }}</small>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>
                                                    {{ number_format($t->total_paid ?? $t->amount_sent, 2) }}
                                                </strong>
                                                <span class="text-muted small">
                                                    {{ $t->fromCurrency->code ?? '' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge 
                                                    @if($t->status === 'completed') bg-success 
                                                    @elseif($t->status === 'pending') bg-warning 
                                                    @else bg-secondary 
                                                    @endif
                                                ">
                                                    {{ ucfirst($t->status) }}
                                                </span>
                                            </td>
                                            <td>{{ optional($t->created_at)->diffForHumans() }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-muted py-4">No recent transactions.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- AGENT APPLICATIONS --}}
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">Pending Agent Applications</h5>
                </div>

                <div class="card-body">
                    @if($agentApplications->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Store</th>
                                        <th>Address</th>
                                        <th>Applied</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($agentApplications as $ap)
                                        <tr>
                                            <td>{{ $ap->user->first_name }} {{ $ap->user->last_name }}</td>
                                            <td>{{ $ap->user->email }}</td>
                                            <td>{{ $ap->store_name ?? 'N/A' }}</td>
                                            <td>{{ $ap->address ?? 'N/A' }}</td>
                                            <td>{{ optional($ap->created_at)->diffForHumans() }}</td>
                                            <td><span class="badge bg-warning">Pending</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-muted mb-0">No pending agent applications.</p>
                    @endif
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
