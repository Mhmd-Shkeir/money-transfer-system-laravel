@extends('layouts.dashboard')

@section('title', 'User Dashboard - Jaybtak Pro')
@section('page-title', 'Dashboard')
@section('page-subtitle')
    Welcome back, {{ optional(Auth::user())->first_name ?? 'Guest' }}!
@endsection

@section('content')
    <!-- Summary Cards - Database Driven -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card stat-card h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="icon-box bg-primary-light">
                            <i data-lucide="dollar-sign" class="text-primary-custom" style="width: 24px; height: 24px;"></i>
                        </div>
                        <span class="badge bg-light text-muted">This Month</span>
                    </div>
                    <h3 class="mb-1">{{ $totalSent && $totalSent > 0 ? '$' . number_format($totalSent, 2) : '$0.00' }}</h3>
                    <p class="text-muted mb-0">Total Sent</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="icon-box bg-success-light">
                            <i data-lucide="send" class="text-success-custom" style="width: 24px; height: 24px;"></i>
                        </div>
                        <span class="badge bg-light text-muted">Total</span>
                    </div>
                    <h3 class="mb-1">{{ $transactionsCount ?? 0 }}</h3>
                    <p class="text-muted mb-0">Transactions</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="icon-box bg-accent-light">
                            <i data-lucide="users" style="width: 24px; height: 24px; color: var(--accent-color);"></i>
                        </div>
                        <span class="badge bg-light text-muted">Saved</span>
                    </div>
                    <h3 class="mb-1">{{ $beneficiariesCount ?? 0 }}</h3>
                    <p class="text-muted mb-0">Beneficiaries</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="icon-box bg-danger-light">
                            <i data-lucide="trending-up" style="width: 24px; height: 24px; color: var(--danger-color);"></i>
                        </div>
                        <span class="badge bg-success-light text-success-custom">+0%</span>
                    </div>
                    <h3 class="mb-1">{{ $savedFees && $savedFees > 0 ? '$' . number_format($savedFees, 2) : '$0.00' }}</h3>
                    <p class="text-muted mb-0">Saved in Fees</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Transactions -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">Recent Transactions</h4>
                        <a href="{{ route('user.history') }}" class="btn btn-link text-primary-custom text-decoration-none">
                            View All
                        </a>
                    </div>

                    <div class="d-flex flex-column gap-3">
                        @if(isset($recent) && $recent->count() > 0)
                            @foreach($recent as $transaction)
                                <div class="p-3 bg-light rounded-3 d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center {{ $transaction->status === 'completed' ? 'bg-success-light' : 'bg-danger-light' }}"
                                             style="width: 40px; height: 40px;">
                                            <i data-lucide="arrow-up-right" style="width: 20px; height: 20px;"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $transaction->beneficiary->name ?? 'Beneficiary' }}</h6>
                                            <small class="text-muted">{{ $transaction->reference_code }} • {{ $transaction->created_at->format('M j, Y') }}</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <h6 class="mb-0">${{ number_format($transaction->amount_sent, 2) }}</h6>
                                        <small class="{{ $transaction->status === 'completed' ? 'text-success-custom' : 'text-primary-custom' }}">
                                            {{ ucfirst($transaction->status) }}
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="p-3 bg-light rounded-3 text-center">
                                <p class="text-muted">
                                    <i data-lucide="database" style="width: 32px; height: 32px; display: block; margin: 0 auto 10px;"></i>
                                    No transactions yet. Create one using Send Money.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Promotions -->
        <div class="col-lg-4">
            <div class="d-flex flex-column gap-4">
                <!-- Quick Actions -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="mb-4">Quick Actions</h5>
                        <div class="d-flex flex-column gap-3">
                            <a href="{{ route('user.send') }}" class="btn btn-primary d-flex align-items-center justify-content-start">
                                <i data-lucide="send" class="me-2" style="width: 20px; height: 20px;"></i>
                                Send Money
                            </a>
                            <a href="{{ route('user.beneficiaries') }}" class="btn btn-outline-primary d-flex align-items-center justify-content-start">
                                <i data-lucide="users" class="me-2" style="width: 20px; height: 20px;"></i>
                                Add Beneficiary
                            </a>
                            <a href="{{ route('user.track') }}" class="btn btn-outline-primary d-flex align-items-center justify-content-start">
                                <i data-lucide="clock" class="me-2" style="width: 20px; height: 20px;"></i>
                                Track Transfer
                            </a>
                        </div>
                    </div>
                <!-- Wallet Balance -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="mb-0">Wallet Balance</h6>
                            <!-- Currency Selector -->
                            <form action="{{ route('user.dashboard.update-currency') }}" method="POST" class="d-inline">
                                @csrf
                                <select name="currency_id" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                                    @foreach($currencies as $currency)
                                        <option value="{{ $currency->id }}" {{ Auth::user()->currency_id == $currency->id ? 'selected' : '' }}>
                                            {{ $currency->code }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                @php
                                    $user = Auth::user();
                                    $preferredCurrency = $user->getPreferredCurrency();
                                    // Use controller-provided display balance if available, fallback to model converter
                                    $displayBalance = $walletBalanceDisplay ?? $user->getBalanceInPreferredCurrency();
                                @endphp
                                <h4 class="mb-0">
                                    {{ $preferredCurrency->symbol ?? '$' }}{{ number_format($displayBalance, 2) }}
                                </h4>
                                <p class="text-muted mb-0">Available Balance</p>
                                <small class="text-muted">Displayed in {{ $preferredCurrency->code ?? 'USD' }}</small>
                            </div>
                            <div>
                                <i data-lucide="wallet" style="width:28px;height:28px;color:var(--primary-color);"></i>
                            </div>
                        </div>
                    </div>
                    @php
                        // Determine top active offer (highest min_amount <= any)
                        $topOffer = \App\Models\Offer::where('is_active', 1)
                            ->where(function($q) { $q->whereNull('start_date')->orWhere('start_date', '<=', now()); })
                            ->where(function($q) { $q->whereNull('end_date')->orWhere('end_date', '>=', now()); })
                            ->orderBy('min_amount', 'desc')
                            ->first();
                    @endphp

                    @if($topOffer)
                        <div class="card mt-3 border-0 shadow-sm">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <small class="text-muted">Top Offer</small>
                                        <h6 class="mb-1">{{ $topOffer->name ?? 'Special Offer' }}</h6>
                                        <small class="text-muted">Min Amount: ${{ number_format($topOffer->min_amount, 2) }} • Discount: {{ number_format($topOffer->discount_percentage, 2) }}%</small>
                                    </div>
                                    <div class="text-end">
                                        @if($topOffer->start_date || $topOffer->end_date)
                                            <small class="text-muted">Valid</small>
                                            <div class="small text-muted">{{ $topOffer->start_date ? $topOffer->start_date->format('M j, Y') : 'Any' }} – {{ $topOffer->end_date ? $topOffer->end_date->format('M j, Y') : 'Any' }}</div>
                                        @else
                                            <small class="text-muted">Always Active</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
