@php
    $userRole = Auth::user()->role ?? 'user';
    $currentRoute = Route::currentRouteName();
@endphp

<div class="d-flex flex-column flex-shrink-0 p-3 bg-white border-end" style="width: 280px; min-height: 100vh;">
    <!-- Logo -->
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                <img src="{{ asset('JAYBTAK_LOGO.png') }}" 
                    alt="JAYBTAK Pro" 
                    class="img-fluid"
                    style="height: 50px;">
            </a>

    <hr>

    <!-- Navigation -->
    <ul class="nav nav-pills flex-column mb-auto">
        @if(in_array($userRole, ['user', 'client']))
            <!-- User Menu -->
            <li class="nav-item">
                <a href="{{ route('user.dashboard') }}" 
                   class="nav-link {{ $currentRoute === 'user.dashboard' ? 'active' : 'text-dark' }}"
                   style="border-radius: var(--border-radius); margin-bottom: 0.5rem;">
                    <i data-lucide="home" class="me-2" style="width: 20px; height: 20px;"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('user.send') }}" 
                   class="nav-link {{ $currentRoute === 'user.send' ? 'active' : 'text-dark' }}"
                   style="border-radius: var(--border-radius); margin-bottom: 0.5rem;">
                    <i data-lucide="send" class="me-2" style="width: 20px; height: 20px;"></i>
                    Send Money
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('user.track') }}" 
                   class="nav-link {{ $currentRoute === 'user.track' ? 'active' : 'text-dark' }}"
                   style="border-radius: var(--border-radius); margin-bottom: 0.5rem;">
                    <i data-lucide="clock" class="me-2" style="width: 20px; height: 20px;"></i>
                    Track Transfer
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('user.history') }}" 
                   class="nav-link {{ $currentRoute === 'user.history' ? 'active' : 'text-dark' }}"
                   style="border-radius: var(--border-radius); margin-bottom: 0.5rem;">
                    <i data-lucide="history" class="me-2" style="width: 20px; height: 20px;"></i>
                    History
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('user.beneficiaries') }}" 
                   class="nav-link {{ $currentRoute === 'user.beneficiaries' ? 'active' : 'text-dark' }}"
                   style="border-radius: var(--border-radius); margin-bottom: 0.5rem;">
                    <i data-lucide="users" class="me-2" style="width: 20px; height: 20px;"></i>
                    Beneficiaries
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('user.notifications') }}" 
                   class="nav-link {{ $currentRoute === 'user.notifications' ? 'active' : 'text-dark' }}"
                   style="border-radius: var(--border-radius); margin-bottom: 0.5rem;">
                    <i data-lucide="bell" class="me-2" style="width: 20px; height: 20px;"></i>
                    Notifications
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('user.profile') }}" 
                   class="nav-link {{ $currentRoute === 'user.profile' ? 'active' : 'text-dark' }}"
                   style="border-radius: var(--border-radius); margin-bottom: 0.5rem;">
                    <i data-lucide="user" class="me-2" style="width: 20px; height: 20px;"></i>
                    Profile
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('support') }}" 
                   class="nav-link {{ $currentRoute === 'support' ? 'active' : 'text-dark' }}"
                   style="border-radius: var(--border-radius); margin-bottom: 0.5rem;">
                    <i data-lucide="help-circle" class="me-2" style="width: 20px; height: 20px;"></i>
                    Support
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('user.reviews') }}" 
                   class="nav-link {{ $currentRoute === 'user.reviews' ? 'active' : 'text-dark' }}"
                   style="border-radius: var(--border-radius); margin-bottom: 0.5rem;">
                    <i data-lucide="star" class="me-2" style="width: 20px; height: 20px;"></i>
                    Reviews & Ratings
                </a>
            </li>
        @elseif($userRole === 'agent')
            <!-- Agent Menu -->
            <li class="nav-item">
                <a href="{{ route('agent.dashboard') }}" 
                   class="nav-link {{ $currentRoute === 'agent.dashboard' ? 'active' : 'text-dark' }}"
                   style="border-radius: var(--border-radius); margin-bottom: 0.5rem;">
                    <i data-lucide="home" class="me-2" style="width: 20px; height: 20px;"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('agent.requests') }}" 
                   class="nav-link {{ $currentRoute === 'agent.requests' ? 'active' : 'text-dark' }}"
                   style="border-radius: var(--border-radius); margin-bottom: 0.5rem;">
                    <i data-lucide="inbox" class="me-2" style="width: 20px; height: 20px;"></i>
                    Incoming Requests
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('agent.transactions') }}" 
                   class="nav-link {{ $currentRoute === 'agent.transactions' ? 'active' : 'text-dark' }}"
                   style="border-radius: var(--border-radius); margin-bottom: 0.5rem;">
                    <i data-lucide="credit-card" class="me-2" style="width: 20px; height: 20px;"></i>
                    Transaction Processing
                </a>
            </li>
            <li class="nav-item">
    <a href="{{ route('agent.payout.index') }}" 
       class="nav-link {{ $currentRoute === 'agent.payout.index' ? 'active' : 'text-dark' }}"
       style="border-radius: var(--border-radius); margin-bottom: 0.5rem;">
        <i data-lucide="coins" class="me-2" style="width: 20px; height: 20px;"></i>
        Payout Requests
    </a>
</li>
            <li class="nav-item">
                <a href="{{ route('agent.cash-balance') }}" 
                   class="nav-link {{ $currentRoute === 'agent.cash-balance' ? 'active' : 'text-dark' }}"
                   style="border-radius: var(--border-radius); margin-bottom: 0.5rem;">
                    <i data-lucide="wallet" class="me-2" style="width: 20px; height: 20px;"></i>
                    Cash Balance
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('agent.profile') }}" 
                   class="nav-link {{ $currentRoute === 'agent.profile' ? 'active' : 'text-dark' }}"
                   style="border-radius: var(--border-radius); margin-bottom: 0.5rem;">
                    <i data-lucide="user" class="me-2" style="width: 20px; height: 20px;"></i>
                    Profile
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('support') }}" 
                   class="nav-link {{ $currentRoute === 'support' ? 'active' : 'text-dark' }}"
                   style="border-radius: var(--border-radius); margin-bottom: 0.5rem;">
                    <i data-lucide="help-circle" class="me-2" style="width: 20px; height: 20px;"></i>
                    Support
                </a>
            </li>
        @elseif($userRole === 'admin')
            <!-- Admin Menu -->
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" 
                   class="nav-link {{ $currentRoute === 'admin.dashboard' ? 'active' : 'text-dark' }}"
                   style="border-radius: var(--border-radius); margin-bottom: 0.5rem;">
                    <i data-lucide="home" class="me-2" style="width: 20px; height: 20px;"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.users') }}" 
                   class="nav-link {{ $currentRoute === 'admin.users' ? 'active' : 'text-dark' }}"
                   style="border-radius: var(--border-radius); margin-bottom: 0.5rem;">
                    <i data-lucide="users" class="me-2" style="width: 20px; height: 20px;"></i>
                    User Management
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.agents') }}" 
                   class="nav-link {{ $currentRoute === 'admin.agents' ? 'active' : 'text-dark' }}"
                   style="border-radius: var(--border-radius); margin-bottom: 0.5rem;">
                    <i data-lucide="user-check" class="me-2" style="width: 20px; height: 20px;"></i>
                    Agent Approval
                </a>
            </li>
            <li class="nav-item">
    <a href="{{ route('admin.payout.index') }}" 
       class="nav-link {{ $currentRoute === 'admin.payout.index' ? 'active' : 'text-dark' }}"
       style="border-radius: var(--border-radius); margin-bottom: 0.5rem;">
        <i data-lucide="coins" class="me-2" style="width: 20px; height: 20px;"></i>
        Payout Requests
    </a>
</li>

            <li class="nav-item">
                <a href="{{ route('refund-requests.index') }}" 
                   class="nav-link {{ $currentRoute === 'refund-requests.index' ? 'active' : 'text-dark' }}"
                   style="border-radius: var(--border-radius); margin-bottom: 0.5rem;">
                    <i data-lucide="undo-2" class="me-2" style="width: 20px; height: 20px;"></i>
                    Refund Requests
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.offers.index') }}"
                   class="nav-link {{ in_array($currentRoute, ['admin.offers.index','admin.offers.create']) ? 'active' : 'text-dark' }}"
                   style="border-radius: var(--border-radius); margin-bottom: 0.5rem;">
                    <i data-lucide="tag" class="me-2" style="width: 20px; height: 20px;"></i>
                    Offers
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.rates') }}" 
                   class="nav-link {{ $currentRoute === 'admin.rates' ? 'active' : 'text-dark' }}"
                   style="border-radius: var(--border-radius); margin-bottom: 0.5rem;">
                    <i data-lucide="dollar-sign" class="me-2" style="width: 20px; height: 20px;"></i>
                    Exchange Rates
                </a>
            </li>
            <!-- <li class="nav-item">
                <a href="{{ route('admin.notifications') }}" 
                   class="nav-link {{ $currentRoute === 'user.notifications' ? 'active' : 'text-dark' }}"
                   style="border-radius: var(--border-radius); margin-bottom: 0.5rem;">
                    <i data-lucide="bell" class="me-2" style="width: 20px; height: 20px;"></i>
                    Notifications
                </a>
            </li> -->
            <li class="nav-item">
                <a href="{{ route('admin.reports') }}" 
                   class="nav-link {{ $currentRoute === 'admin.reports' ? 'active' : 'text-dark' }}"
                   style="border-radius: var(--border-radius); margin-bottom: 0.5rem;">
                    <i data-lucide="bar-chart-3" class="me-2" style="width: 20px; height: 20px;"></i>
                    Reports & Analytics
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.compliance') }}" 
                   class="nav-link {{ $currentRoute === 'admin.compliance' ? 'active' : 'text-dark' }}"
                   style="border-radius: var(--border-radius); margin-bottom: 0.5rem;">
                    <i data-lucide="shield" class="me-2" style="width: 20px; height: 20px;"></i>
                    Fraud & Compliance
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.support') }}" 
                   class="nav-link {{ $currentRoute === 'admin.support' ? 'active' : 'text-dark' }}"
                   style="border-radius: var(--border-radius); margin-bottom: 0.5rem;">
                    <i data-lucide="message-square" class="me-2" style="width: 20px; height: 20px;"></i>
                    Customer Support
                </a>
            </li>
        @endif
    </ul>

    <hr>

    <!-- Agent Business Info Card (only for agents) -->
    @if($userRole === 'agent' && isset(Auth::user()->agentProfile))
        <div style="background-color: #f8f9fa; padding: 12px; border-radius: var(--border-radius); margin-bottom: 1rem; font-size: 0.85rem;">
            <p style="font-weight: 600; margin-bottom: 0.5rem; color: var(--text-primary);">Business Info</p>
            <div style="margin-bottom: 0.5rem;">
                <small style="color: var(--text-secondary);">Business Name</small>
                <p style="margin: 0; font-weight: 500; font-size: 0.8rem;">{{ Auth::user()->agentProfile?->business_name ?? 'Not set' }}</p>
            </div>
            <div style="margin-bottom: 0.5rem;">
                <small style="color: var(--text-secondary);">Store Name</small>
                <p style="margin: 0; font-weight: 500; font-size: 0.8rem;">{{ Auth::user()->agentProfile?->store_name ?? 'Not set' }}</p>
            </div>
            <div style="margin-bottom: 0.5rem;">
                <small style="color: var(--text-secondary);">Country</small>
                <p style="margin: 0; font-weight: 500; font-size: 0.8rem;">{{ Auth::user()->agentProfile?->country?->name ?? 'Not set' }}</p>
            </div>
            <div style="margin-bottom: 0.5rem;">
                <small style="color: var(--text-secondary);">Currency</small>
                <p style="margin: 0; font-weight: 500; font-size: 0.8rem;">{{ Auth::user()->agentProfile?->currency?->code ?? 'Not set' }}</p>
            </div>
            <div style="margin-bottom: 0.5rem;">
                <small style="color: var(--text-secondary);">Status</small>
                <p style="margin: 0; font-weight: 500; font-size: 0.8rem;">
                    @if(Auth::user()->agentProfile?->is_approved)
                        <span style="color: var(--success-color);">✓ Approved</span>
                    @else
                        <span style="color: var(--danger-color);">⏳ Pending</span>
                    @endif
                </p>
            </div>
        </div>
    @endif

    <hr>

    <!-- User Dropdown -->
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" 
           id="dropdownUser" data-bs-toggle="dropdown">
            <div class="icon-box icon-box-sm me-2 bg-primary-light">
                <i data-lucide="user" style="width: 20px; height: 20px; color: var(--primary-color);"></i>
            </div>
            <div>
                <strong>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</strong>
                <br>
                <small class="text-muted">{{ ucfirst($userRole) }}</small>
            </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
            <li>
                @if(in_array($userRole, ['user', 'client']))
                            <a class="dropdown-item" href="{{ route('user.profile') }}">Profile</a>
                        @elseif($userRole === 'agent')
                    <a class="dropdown-item" href="{{ route('agent.profile') }}">Profile</a>
                @elseif($userRole === 'admin')
                    <a class="dropdown-item" href="{{ route('admin.profile') }}">Profile</a>
                @endif
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="dropdown-item" style="width: 100%; text-align: left; border: none; background: none; cursor: pointer;">
                        <i data-lucide="log-out" style="width: 16px; height: 16px; display: inline-block; margin-right: 8px;"></i>
                        Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>
</div>

@push('styles')
<style>
    .nav-link.active {
        background-color: var(--primary-color) !important;
        color: white !important;
    }
    .nav-link:hover:not(.active) {
        background-color: rgba(0, 119, 182, 0.1);
    }
</style>
@endpush
