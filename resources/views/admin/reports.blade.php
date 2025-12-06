@extends('layouts.dashboard')

@section('title', 'Reports & Analytics')
@section('page-title', 'Reports & Analytics')
@section('page-subtitle', 'Business performance insights')

@section('content')

<div class="row">

    {{-- Summary Cards: Clients / Agents split + Transactions / Revenue --}}
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6>Clients</h6>
                <h3>{{ $totalClients ?? 0 }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-secondary text-white">
            <div class="card-body">
                <h6>Agents</h6>
                <h3>{{ $totalAgents ?? 0 }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6>Total Transactions</h6>
                <h3>{{ $totalTransactions }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6>Total Revenue</h6>
                <h3>${{ number_format($totalRevenue, 2) }}</h3>
            </div>
        </div>
    </div>

</div>

{{-- Quick Shortcuts: small tables for latest clients and agents --}}
<div class="row mt-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h6 class="mb-0">Latest Clients (Quick)</h6></div>
            <div class="card-body py-2">
                @if(isset($topClients) && $topClients->isNotEmpty())
                    <table class="table table-sm mb-0">
                        <tbody>
                        @foreach($topClients as $c)
                            <tr>
                                <td class="p-1">{{ $c->first_name }} {{ $c->last_name }}</td>
                                <td class="p-1 text-muted" style="width:110px">{{ $c->created_at->format('Y-m-d') }}</td>
                                <td class="p-1 text-end" style="width:80px"><a href="{{ route('admin.users.show', $c->id) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="mb-0 text-muted">No recent clients</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h6 class="mb-0">Latest Agents (Quick)</h6></div>
            <div class="card-body py-2">
                @if(isset($topAgents) && $topAgents->isNotEmpty())
                    <table class="table table-sm mb-0">
                        <tbody>
                        @foreach($topAgents as $a)
                            <tr>
                                <td class="p-1">{{ $a->agentProfile?->business_name ?? ($a->first_name . ' ' . $a->last_name) }}</td>
                                <td class="p-1 text-muted" style="width:110px">{{ $a->created_at->format('Y-m-d') }}</td>
                                <td class="p-1 text-end" style="width:80px"><a href="{{ route('admin.users.show', $a->id) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="mb-0 text-muted">No recent agents</p>
                @endif
            </div>
        </div>
    </div>
</div>


{{-- Charts --}}
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h5>Transactions Per Month</h5></div>
            <div class="card-body"><canvas id="transactionsChart"></canvas></div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h5>Revenue Per Month</h5></div>
            <div class="card-body"><canvas id="revenueChart"></canvas></div>
        </div>
    </div>
</div>


{{-- Recent Transactions --}}
<div class="card mt-4">
    <div class="card-header"><h5>Recent Transactions</h5></div>
    <div class="card-body">
        <table class="table">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Sender</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>

            <tbody>
                @foreach($recentTransactions as $t)
                    <tr>
                        <td>{{ $t->id }}</td>
                        <td>
                            @if($t->sender)
                                {{ $t->sender->first_name }} {{ $t->sender->last_name }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>                        
                        <td>{{ $t->amount_sent }}</td>
                        <td>{{ $t->status }}</td>
                        <td>{{ $t->created_at }}</td>
                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>
</div>


{{-- Recent Compliance --}}
<div class="card mt-4 mb-5">
    <div class="card-header"><h5>Recent Compliance Activity</h5></div>
    <div class="card-body">
        <table class="table">
            <thead class="table-light">
                <tr>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>

            <tbody>
                @foreach($recentCompliance as $c)
                    <tr>
                        <td>{{ $c->type }}</td>
                        <td>{{ $c->description }}</td>
                        <td>{{ $c->status }}</td>
                        <td>{{ $c->created_at }}</td>
                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>
</div>



{{-- Users: Clients & Agents tabs --}}
<div class="card mt-4">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="reportsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="clients-tab" data-bs-toggle="tab" data-bs-target="#clients" type="button" role="tab" aria-controls="clients" aria-selected="true">Clients</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="agents-tab" data-bs-toggle="tab" data-bs-target="#agents" type="button" role="tab" aria-controls="agents" aria-selected="false">Agents</button>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="reportsTabsContent">
            <div class="tab-pane fade show active" id="clients" role="tabpanel" aria-labelledby="clients-tab">
                @if($clients->isEmpty())
                    <p class="text-muted">No clients found.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Joined</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($clients as $u)
                                    <tr>
                                        <td>{{ $u->id }}</td>
                                        <td>{{ $u->first_name }} {{ $u->last_name }}</td>
                                        <td>{{ $u->email }}</td>
                                        <td>{{ $u->created_at->format('Y-m-d') }}</td>
                                        <td class="text-end"><a href="{{ route('admin.users.show', $u->id) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-2">{{ $clients->links() }}</div>
                @endif
            </div>

            <div class="tab-pane fade" id="agents" role="tabpanel" aria-labelledby="agents-tab">
                @if($agentsList->isEmpty())
                    <p class="text-muted">No agents found.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Business</th>
                                    <th>Owner</th>
                                    <th>Email</th>
                                    <th>Joined</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($agentsList as $u)
                                    <tr>
                                        <td>{{ $u->id }}</td>
                                        <td>{{ $u->agentProfile?->business_name ?? '—' }}</td>
                                        <td>{{ $u->first_name }} {{ $u->last_name }}</td>
                                        <td>{{ $u->email }}</td>
                                        <td>{{ $u->created_at->format('Y-m-d') }}</td>
                                        <td class="text-end"><a href="{{ route('admin.users.show', $u->id) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-2">{{ $agentsList->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>


{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

    // Transactions Chart
    new Chart(document.getElementById('transactionsChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode(array_keys($transactionsPerMonth->toArray())) !!},
            datasets: [{
                label: 'Transactions',
                data: {!! json_encode(array_values($transactionsPerMonth->toArray())) !!},
                borderWidth: 2
            }]
        }
    });

    // Revenue Chart
    new Chart(document.getElementById('revenueChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($revenuePerMonth->toArray())) !!},
            datasets: [{
                label: 'Revenue ($)',
                data: {!! json_encode(array_values($revenuePerMonth->toArray())) !!},
            }]
        }
    });

</script>

@endsection

