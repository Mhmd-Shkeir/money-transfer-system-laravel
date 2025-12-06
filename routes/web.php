<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminAgentController;
use App\Http\Controllers\SendMoneyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminExchangeRateController;
use App\Http\Controllers\AdminComplianceController;
use App\Http\Controllers\AdminReportsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AgentPayoutRequestController;

/*
|--------------------------------------------------------------------------
| 🌍 Public Pages
|--------------------------------------------------------------------------
*/

// Home - redirect to dashboard if logged in
Route::get('/', function () {
	if (auth()->check()) {
		$user = auth()->user();
		if ($user->role === 'admin') {
			return redirect()->route('admin.dashboard');
		} elseif ($user->role === 'agent') {
			return redirect()->route('agent.dashboard');
		} else {
			return redirect()->route('user.dashboard');
		}
	}
	return view('home');
})->name('home');

// About
Route::view('/about', 'pages.about')->name('about');

// Contact
Route::view('/contact', 'pages.contact')->name('contact');

// Terms & Conditions
Route::view('/terms', 'pages.terms')->name('terms');

// 404 page (for manual testing)
Route::view('/404', 'pages.404')->name('404');

// Public track transfer page and AJAX check (accessible without login)
Route::get('/track-transfer', [SendMoneyController::class, 'trackPage'])->name('user.track');
Route::get('/track-transfer/check', [SendMoneyController::class, 'trackCheck'])->name('user.track.check');

/*
|--------------------------------------------------------------------------
| 👥 Authentication
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| 👥 Authentication Routes
|--------------------------------------------------------------------------
*/

// ✅ Signup (GET = form, POST = handle)
Route::get('/signup', [UserController::class, 'showSignupForm'])->name('signup.form');
Route::post('/signup', [UserController::class, 'signup'])->name('signup');

// ✅ Login (GET = form, POST = handle)
Route::get('/login', [UserController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UserController::class, 'login'])->name('login.post');

// ✅ Logout
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

// ✅ Email verification
Route::get('/verify-email/{token}', [UserController::class, 'verifyEmail'])->name('verify.email');
/*
|--------------------------------------------------------------------------
| 🧑 User Area
|--------------------------------------------------------------------------
*/

// User area (controller-driven where appropriate)
Route::middleware('auth')->group(function () {
	// Dashboard (shows DB-driven stats)
	Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
	Route::post('/dashboard/update-currency', [UserDashboardController::class, 'updateCurrency'])->name('user.dashboard.update-currency');

	// User profile (DB-driven)
	Route::get('/profile', [ProfileController::class, 'show'])->name('user.profile');
	Route::post('/profile', [ProfileController::class, 'update'])->name('user.profile.update');

	// Send money - show form and store
	Route::get('/send-money', [SendMoneyController::class, 'show'])->name('user.send');
	Route::post('/send-money', [SendMoneyController::class, 'store'])->name('user.send.store');

	// Bank deposit fake Visa payment flow
	Route::get('/send-money/bank-deposit', [SendMoneyController::class, 'showBankDepositForm'])->name('user.send.bank_deposit');
	Route::post('/send-money/bank-deposit', [SendMoneyController::class, 'processBankDeposit'])->name('user.send.bank_deposit.process');

	// User history and receipts
	Route::get('/history', [SendMoneyController::class, 'history'])->name('user.history');
	Route::get('/history/{id}', [SendMoneyController::class, 'receipt'])->name('user.history.receipt');

	// User beneficiaries
	Route::get('/beneficiaries', [\App\Http\Controllers\BeneficiaryController::class, 'index'])->name('user.beneficiaries');
	Route::post('/beneficiaries', [\App\Http\Controllers\BeneficiaryController::class, 'store'])->name('user.beneficiaries.store');
	Route::delete('/beneficiaries/{id}', [\App\Http\Controllers\BeneficiaryController::class, 'destroy'])->name('user.beneficiaries.destroy');

	// User support page (contact support)
	Route::get('/support', [\App\Http\Controllers\SupportController::class, 'show'])->name('support');
	Route::post('/support', [\App\Http\Controllers\SupportController::class, 'store'])->name('support.send');

	// Refund requests
	Route::post('/refund-requests', [\App\Http\Controllers\RefundRequestController::class, 'store'])->name('refund-requests.store');

    // Offer lookup (used by client-side send form to find an applicable offer)
    Route::get('/offers/for-amount', [\App\Http\Controllers\OfferController::class, 'forAmount'])->name('offers.for-amount');

// Notifications (for all authenticated users: agents and regular users)
Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])
    ->name('user.notifications'); // <-- keep only this name

Route::post('/notifications/{id}/mark-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])
    ->name('notifications.mark-read');

Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])
    ->name('notifications.mark-all-read');

	Route::delete('/notifications/delete-all', [\App\Http\Controllers\NotificationController::class, 'deleteAll'])
    ->name('notifications.delete-all');

Route::delete('/notifications/{id}', [\App\Http\Controllers\NotificationController::class, 'delete'])
    ->name('notifications.delete');



Route::get('/notifications/panel', [\App\Http\Controllers\NotificationController::class, 'getPanel'])
    ->name('notifications.panel');

Route::get('/notifications/unread-count', [\App\Http\Controllers\NotificationController::class, 'unreadCount'])
    ->name('notifications.unread-count');

	// Universal transaction view (authenticated users)
	Route::get('/transactions/{id}/view', [\App\Http\Controllers\SendMoneyController::class, 'viewTransaction'])
		->name('transactions.view');

	// Reviews and ratings
	Route::get('/reviews', [\App\Http\Controllers\ReviewController::class, 'index'])->name('user.reviews');
	Route::post('/reviews', [\App\Http\Controllers\ReviewController::class, 'store'])->name('user.reviews.store');
	Route::delete('/reviews/{id}', [\App\Http\Controllers\ReviewController::class, 'destroy'])->name('user.reviews.destroy');
});

/*
|--------------------------------------------------------------------------
| 🤝 Agent Area
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:agent'])->group(function () {
	Route::get('/agent/dashboard', [\App\Http\Controllers\AgentDashboardController::class, 'index'])->name('agent.dashboard');
	
	// Incoming transfer requests
	Route::get('/agent/incoming-requests', [\App\Http\Controllers\AgentTransactionController::class, 'incomingRequests'])->name('agent.incoming-requests');
	Route::get('/agent/requests', [\App\Http\Controllers\AgentTransactionController::class, 'incomingRequests'])->name('agent.requests');
	Route::post('/agent/incoming-requests/{id}/accept', [\App\Http\Controllers\AgentTransactionController::class, 'acceptRequest'])->name('agent.request.accept');
	Route::post('/agent/incoming-requests/{id}/reject', [\App\Http\Controllers\AgentTransactionController::class, 'rejectRequest'])->name('agent.request.reject');
	
	// Cash operations
	Route::get('/agent/cash-balance', [\App\Http\Controllers\AgentTransactionController::class, 'cashBalance'])->name('agent.cash-balance');
	Route::post('/agent/cash-in', [\App\Http\Controllers\AgentTransactionController::class, 'cashIn'])->name('agent.cash-in');
	Route::post('/agent/cash-out', [\App\Http\Controllers\AgentTransactionController::class, 'cashOut'])->name('agent.cash-out');
	
	// Agent transactions (processing/history)
	Route::get('/agent/transactions', [\App\Http\Controllers\AgentTransactionController::class, 'transactions'])->name('agent.transactions');
	Route::get('/agent/profile', [ProfileController::class, 'agentShow'])->name('agent.profile');
	Route::post('/agent/profile', [ProfileController::class, 'updateAgentProfile'])->name('agent.profile.update');

	 Route::get('/agent/payout-requests', [AgentPayoutRequestController::class, 'index'])
        ->name('agent.payout.index');
    Route::post('/agent/payout-requests', [AgentPayoutRequestController::class, 'store'])
        ->name('agent.payout.store');
});

/*
|--------------------------------------------------------------------------
| 🛠 Admin Area (optional for later)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->group(function () {
	Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
	Route::get('/admin/users', [\App\Http\Controllers\AdminUserController::class, 'index'])->name('admin.users');
	Route::get('/admin/users/{id}', [\App\Http\Controllers\AdminUserController::class, 'show'])->name('admin.users.show');
	// Edit and update user
	Route::get('/admin/users/{id}/edit', [\App\Http\Controllers\AdminUserController::class, 'edit'])->name('admin.users.edit');
	Route::put('/admin/users/{id}', [\App\Http\Controllers\AdminUserController::class, 'update'])->name('admin.users.update');
	Route::view('/admin/agents', 'admin.agents')->name('admin.agents');

	// Agent approval actions
	Route::post('/admin/agents/{id}/approve', [AdminAgentController::class, 'approve'])->name('admin.agents.approve');
	Route::post('/admin/agents/{id}/reject', [AdminAgentController::class, 'reject'])->name('admin.agents.reject');
	Route::get('/admin/rates', [AdminExchangeRateController::class, 'index'])->name('admin.rates');
	Route::post('/admin/rates', [AdminExchangeRateController::class, 'store'])->name('admin.rates.store');
	Route::put('/admin/rates/{id}', [AdminExchangeRateController::class, 'update'])->name('admin.rates.update');
	Route::delete('/admin/rates/{id}', [AdminExchangeRateController::class, 'destroy'])->name('admin.rates.destroy');
	// Admin reports: main dashboard (summary) + per-role listings
	Route::get('/admin/reports', [AdminReportsController::class, 'index'])->name('admin.reports');
	Route::get('/admin/reports/clients', [AdminReportsController::class, 'clients'])->name('admin.reports.clients');
	Route::get('/admin/reports/agents', [AdminReportsController::class, 'agents'])->name('admin.reports.agents');
	Route::post('/admin/currencies', [AdminExchangeRateController::class, 'storeCurrency'])
    	->name('admin.currencies.store');
	Route::delete('/admin/currencies/{id}', [AdminExchangeRateController::class, 'destroyCurrency'])
    	->name('admin.currencies.destroy');
	Route::get('/admin/compliance', [AdminComplianceController::class, 'index'])->name('admin.compliance');
	Route::post('/admin/compliance', [AdminComplianceController::class, 'store'])->name('admin.compliance.store');
	Route::put('/admin/compliance/{id}', [AdminComplianceController::class, 'update'])->name('admin.compliance.update');
	Route::delete('/admin/compliance/{id}', [AdminComplianceController::class, 'destroy'])->name('admin.compliance.destroy');
    Route::get('/admin/reports', [AdminReportsController::class, 'index'])->name('admin.reports');
	Route::get('/admin/support', [\App\Http\Controllers\AdminSupportController::class, 'index'])->name('admin.support');
	Route::get('/admin/support/{id}', [\App\Http\Controllers\AdminSupportController::class, 'show'])->name('admin.support.show');
	Route::post('/admin/support/{id}/close', [\App\Http\Controllers\AdminSupportController::class, 'close'])->name('admin.support.close');
	Route::post('/admin/support/{id}/reply', [\App\Http\Controllers\AdminSupportController::class, 'reply'])->name('admin.support.reply');
	Route::view('/admin/settings', 'admin.settings')->name('admin.settings');
	Route::get('/admin/profile', [ProfileController::class, 'adminShow'])->name('admin.profile');
	Route::post('/admin/profile', [ProfileController::class, 'update'])->name('admin.profile.update');
	Route::get('/admin/notifications', [NotificationController::class, 'adminIndex'])
    	->name('admin.notifications');
	Route::get('/admin/payout-requests', [AgentPayoutRequestController::class, 'adminIndex'])
        ->name('admin.payout.index');
	Route::post('/admin/payout-requests/{id}/approve', [AgentPayoutRequestController::class, 'approve'])
        ->name('admin.payout.approve');
    Route::post('/admin/payout-requests/{id}/reject', [AgentPayoutRequestController::class, 'reject'])
        ->name('admin.payout.reject');

	// Refund requests management
	Route::get('/admin/refund-requests', [\App\Http\Controllers\RefundRequestController::class, 'adminIndex'])->name('refund-requests.index');
	Route::get('/admin/refund-requests/{id}', [\App\Http\Controllers\RefundRequestController::class, 'show'])->name('refund-requests.show');
	Route::post('/admin/refund-requests/{id}/approve', [\App\Http\Controllers\RefundRequestController::class, 'approve'])->name('refund-requests.approve');
	Route::post('/admin/refund-requests/{id}/reject', [\App\Http\Controllers\RefundRequestController::class, 'reject'])->name('refund-requests.reject');	});

    // Admin offers management
    Route::get('/admin/offers', [\App\Http\Controllers\AdminOfferController::class, 'index'])->name('admin.offers.index');
    Route::get('/admin/offers/create', [\App\Http\Controllers\AdminOfferController::class, 'create'])->name('admin.offers.create');
    Route::post('/admin/offers', [\App\Http\Controllers\AdminOfferController::class, 'store'])->name('admin.offers.store');

/*
|--------------------------------------------------------------------------
| 🔄 API Endpoints
|--------------------------------------------------------------------------
*/

// Exchange rate API endpoint
Route::get('/api/exchange-rate/{fromCurrencyId}/{toCurrencyId}', function ($fromCurrencyId, $toCurrencyId) {
    try {
        $svc = new App\Services\ExchangeRateService();
        $rate = $svc->getRateByIds($fromCurrencyId, $toCurrencyId);

        if ($rate) {
            return response()->json([
                'success' => true,
                'rate' => $rate,
                'from_currency_id' => $fromCurrencyId,
                'to_currency_id' => $toCurrencyId
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Exchange rate not found'
            ], 404);
        }
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error fetching exchange rate'
        ], 500);
    }
})->name('api.exchange-rate');

// ✅ Privacy Policy
// Debug routes (local environment only) to trigger and inspect notifications
if (app()->environment('local')) {
	Route::post('/debug/notify-agents', [\App\Http\Controllers\DebugNotificationController::class, 'notify'])
		->name('debug.notify-agents');
	// Convenience GET endpoint (local only) for quick testing in the browser
	Route::get('/debug/notify-agents', [\App\Http\Controllers\DebugNotificationController::class, 'notify'])
		->name('debug.notify-agents.get');

	Route::get('/debug/notifications/me', [\App\Http\Controllers\DebugNotificationController::class, 'me']);
	Route::get('/debug/notifications/{userId}', [\App\Http\Controllers\DebugNotificationController::class, 'list'])
		->name('debug.notifications.list');
}

Route::view('/privacy', 'pages.privacy')->name('privacy');