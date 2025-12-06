@extends('layouts.dashboard')
@section('title', 'Agent Dashboard')
@section('page-title', 'Agent Dashboard')
@section('page-subtitle', 'Welcome to your agent dashboard')

@section('content')



<div class="row g-4 mb-4">
<div class="col-12 mb-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center">

            <h6 class="text-success mb-2 d-flex justify-content-center align-items-center">
                <i data-lucide="wallet" class="me-2" style="width: 20px; height: 20px;"></i>
                Cash on Hand
            </h6>

            <!-- Bigger amount -->
            <h3 class="fw-bold mb-0" style="font-size: 2rem;">
                {{ number_format($agent->cash_balance, 2) }}
            </h3>

            <small class="text-muted">Current Balance</small>

            <hr class="my-3">

            <div class="row text-center">
                <div class="col-6">
                    <p class="mb-1 text-muted">Total In</p>

                    <!-- Slightly larger green number -->
                    <strong class="text-success" style="font-size: 1.3rem;">
                        {{ number_format($agent->total_cash_in, 2) }}
                    </strong>
                </div>

                <div class="col-6">
                    <p class="mb-1 text-muted">Total Out</p>

                    <!-- Slightly larger red number -->
                    <strong class="text-danger" style="font-size: 1.3rem;">
                        {{ number_format($agent->total_cash_out, 2) }}
                    </strong>
                </div>
            </div>

        </div>
    </div>
</div>


</div>



<div class="row g-4">

    <!-- Left: Business Information -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">Business Information</h5>
            </div>
            <div class="card-body">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <small class="text-muted">Business Name</small>
                        <p class="mb-3"><strong>{{ $agent?->business_name ?? 'Not set' }}</strong></p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Store Name</small>
                        <p class="mb-3"><strong>{{ $agent?->store_name ?? 'Not set' }}</strong></p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <small class="text-muted">Business Registration Number</small>
                        <p class="mb-3"><strong>{{ $agent?->business_registration_number ?? 'Not provided' }}</strong></p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Tax ID</small>
                        <p class="mb-3"><strong>{{ $agent?->tax_id ?? 'Not provided' }}</strong></p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <small class="text-muted">Country</small>
                        <p class="mb-3"><strong>{{ $agent?->country?->name ?? 'Not set' }}</strong></p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Operating Currency</small>
                        <p>
                            @if($agent?->currency_id && $agent?->currency)
                                <span class="badge bg-info">{{ $agent->currency->code }}</span>
                                <strong>{{ $agent->currency->name }}</strong>
                            @else
                                <span class="text-muted">Not set</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <small class="text-muted">Business Description</small>
                        <p><strong>{{ $agent?->business_description ?? 'No description provided' }}</strong></p>
                    </div>
                </div>

                <a href="{{ route('agent.profile') }}" class="btn btn-sm btn-outline-primary">
                    Edit Business Info
                </a>
            </div>
        </div>
    </div>



    <!-- Right: Account Status -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">Account Status</h5>
            </div>
            <div class="card-body">

                <div class="mb-3">
                    <small class="text-muted">Approval Status</small>
                    <p class="mb-2">
                        @if($agent?->is_approved)
                            <span class="badge bg-success">Approved</span>
                        @else
                            <span class="badge bg-warning">Pending Approval</span>
                        @endif
                    </p>
                </div>

                <div class="mb-3">
                    <small class="text-muted">Commission Rate</small>
                    <p class="mb-0"><strong>{{ $agent?->commission_rate ?? 0 }}%</strong></p>
                </div>

                <div class="mb-3">
                    <small class="text-muted">Commission Earned</small>
                    <p class="mb-0"><strong>${{ number_format($agentCommissionTotal ?? 0, 2) }}</strong></p>
                </div>

                <div class="mb-3">
                    <small class="text-muted">Account Status</small>
                    <p class="mb-2">
                        @if(Auth::user()->status === 'active')
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Suspended</span>
                        @endif
                    </p>
                </div>

                <hr>

                <div class="mb-3">
                    <small class="text-muted">Member Since</small>
                    <p class="mb-0"><strong>{{ Auth::user()->created_at->format('M d, Y') }}</strong></p>
                </div>

                <div>
                    <small class="text-muted">Last Login</small>
                    <p class="mb-0">
                        <strong>
                            {{ Auth::user()->last_login ? Auth::user()->last_login->format('M d, Y H:i') : 'Never' }}
                        </strong>
                    </p>
                </div>

            </div>
        </div>
    </div>

</div>

@endsection
