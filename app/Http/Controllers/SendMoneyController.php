<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Beneficiary;
use App\Models\Transaction;
use App\Models\User;
use App\Models\TransferFee;
use App\Models\Currency;
use App\Models\Country;
use App\Models\Offer;
use App\Services\ExchangeRateService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\PaymentMethod;
use App\Http\ViewModels\BankDepositViewModel;

class SendMoneyController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $beneficiaries = $user->beneficiaries()->get();
        $fromCurrencyId = $user->currency_id ?: Country::where('name', $user->country)->value('currency_id');
        if (!$fromCurrencyId) {
            $fromCurrencyId = Currency::value('id');
        }

        return view('user.send', compact('beneficiaries'))->with('fromCurrencyId', $fromCurrencyId);
    }

    public function showBankDepositForm(Request $request)
    {
        $user = Auth::user();
        $beneficiaries = $user->beneficiaries()->get();
        $fromCurrencyId = $user->currency_id ?: Country::where('name', $user->country)->value('currency_id');
        if (!$fromCurrencyId) {
            $fromCurrencyId = Currency::value('id');
        }

        $currencies = Currency::all();
        return view('user.bank_deposit', compact('beneficiaries','currencies'))->with('fromCurrencyId', $fromCurrencyId);
    }

    public function processBankDeposit(Request $request)
    {
        $request->validate([
            'beneficiary_id' => 'required|exists:beneficiaries,id',
            'amount_sent' => 'required|numeric|min:0.01',
            'send_currency_id' => 'nullable|exists:currencies,id',
            'card_number' => 'required|string',
            'expiry_month' => 'required|integer|min:1|max:12',
            'expiry_year' => 'required|integer|min:' . date('Y'),
            'cvv' => 'required|digits:3',
            'bank_name' => 'nullable|string|max:255',
            'account_holder' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:64',
        ]);

        $user = Auth::user();

        $card = preg_replace('/\s+/', '', $request->input('card_number'));
        if (!$this->luhnCheck($card)) {
            return back()->withErrors(['card_number' => 'Invalid Visa card number (failed Luhn check).'])->withInput();
        }

        $expMonth = (int)$request->input('expiry_month');
        $expYear = (int)$request->input('expiry_year');
        $expDate = Carbon::create($expYear, $expMonth, 1)->endOfMonth();
        if ($expDate->lt(Carbon::now())) {
            return back()->withErrors(['expiry_month' => 'Card expiry date has passed.'])->withInput();
        }

        $fakePaymentId = (string) Str::uuid();

        $paymentMethod = PaymentMethod::firstOrCreate([
            'user_id' => $user->id,
            'provider' => 'VisaCard',
            'type' => 'credit_card'
        ], [
            'account_holder' => $request->input('account_holder') ?: $user->first_name . ' ' . $user->last_name,
            'account_number' => substr($card, -4),
            'is_verified' => 1,
        ]);

        $beneficiaryId = $request->input('beneficiary_id');
        $beneficiary = Beneficiary::with('country')->find($beneficiaryId);

        $amountInSelectedCurrency = (float) $request->input('amount_sent');
        $sendCurrencyId = $request->input('send_currency_id');
        
        $usdCurrencyId = Currency::where('code', 'USD')->value('id');
        $amount = $amountInSelectedCurrency;
        
        if ($sendCurrencyId && $sendCurrencyId !== $usdCurrencyId) {
            $svc = new ExchangeRateService();
            $rate = $svc->getRateByIds($sendCurrencyId, $usdCurrencyId);
            if ($rate) {
                $amount = round($amountInSelectedCurrency * $rate, 2);
            }
        }

        $toCurrencyId = $request->input('to_currency_id') ?: ($beneficiary?->country?->currency_id ?? null);
        $fromCurrencyId = $request->input('from_currency_id');
        if (!$fromCurrencyId && !empty($user->country)) {
            $fromCurrencyId = Country::where('name', $user->country)->value('currency_id');
        }
        if (!$fromCurrencyId) {
            $fromCurrencyId = Currency::value('id');
        }

        $exchangeRate = 1.0;
        $fee = 0.00;
        $amountReceived = $amount;
        $totalPaid = $amount;

        $agentCommissionRate = 0.025; // 2.5%
        $commissionAmount = round($amount * $agentCommissionRate, 2);

        // Apply any applicable offer discount to the commission (server-side check)
        try {
            $applicableOffer = Offer::where('is_active', 1)
                ->where(function($q) { $q->whereNull('start_date')->orWhere('start_date', '<=', now()); })
                ->where(function($q) { $q->whereNull('end_date')->orWhere('end_date', '>=', now()); })
                ->where('min_amount', '<=', $amount)
                ->orderBy('min_amount', 'desc')
                ->first();

            if ($applicableOffer) {
                $commissionAmount = round($commissionAmount * ($applicableOffer->discount_percentage / 100.0), 2);
            }
        } catch (\Throwable $e) {
            // If anything fails here, fall back to the base commissionAmount
        }
        
        $amountSent = $amount;

        if ($fromCurrencyId && $toCurrencyId && $fromCurrencyId != $toCurrencyId) {
            $svc = new ExchangeRateService();
            $rate = $svc->getRateByIds($fromCurrencyId, $toCurrencyId);

            if ($rate) {
                $exchangeRate = $rate;
                $amountReceived = round($amountSent * $exchangeRate, 2);

                $feeRule = TransferFee::where('from_currency_id', $fromCurrencyId)
                    ->where('to_currency_id', $toCurrencyId)
                    ->where('effective_date', '<=', Carbon::now())
                    ->orderBy('effective_date', 'desc')
                    ->first();

                if ($feeRule) {
                    $fee = round(($feeRule->percentage_fee / 100.0) * $amountSent + $feeRule->fixed_fee, 2);
                }

                $totalPaid = round($amountSent + $fee + $commissionAmount, 2);
            }
        } else {
            $amountReceived = $amountSent;
            $totalPaid = round($amountSent + $commissionAmount, 2);
        }

        $transaction = Transaction::create([
            'sender_id' => $user->id,
            'beneficiary_id' => $beneficiaryId,
            'payment_method_id' => $paymentMethod->id,
            'amount_sent' => $amountSent,
            'from_currency_id' => $fromCurrencyId,
            'to_currency_id' => $toCurrencyId,
            'exchange_rate' => $exchangeRate,
            'fee' => round($fee + $commissionAmount, 2),
            'total_paid' => $totalPaid,
            'amount_received' => $amountReceived,
            'payout_method' => 'bank_deposit',
            'status' => 'authorized',
            'reference_code' => Str::upper(Str::random(10)),
            'fake_payment_id' => $fakePaymentId,
        ]);

        $user->withdraw($totalPaid);

        app(\App\Http\Controllers\NotificationController::class)->notifyAdmins(
            'new_transaction',
            'New Transaction Created',
            'Transaction #' . $transaction->id . ' created by ' .
                ($transaction->sender->first_name ?? 'Unknown') . ' ' .
                ($transaction->sender->last_name ?? ''),
            $transaction->id
        );

        return view('user.bank_deposit_confirm', ['transaction' => $transaction, 'fakePaymentId' => $fakePaymentId]);
    }


    public function history()
    {
        $user = Auth::user();

        $sent = Transaction::with('beneficiary')->where('sender_id', $user->id)->get();

        $incoming = Transaction::with('beneficiary')->whereHas('beneficiary', function($q) use ($user) {
            $q->where('email', $user->email)->orWhere('phone', $user->phone);
        })->get();

        $transactions = $sent->merge($incoming)->sortByDesc('created_at');

        return view('user.history', compact('transactions'));
    }

    public function trackPage(Request $request)
    {
        $prefill = $request->query('ref');
        if (Auth::check()) {
            return view('user.track_dashboard', ['prefill' => $prefill]);
        }
        return view('user.track', ['prefill' => $prefill]);
    }

    public function trackCheck(Request $request)
    {
        $request->validate([
            'ref' => 'required|string|max:64'
        ]);

        $ref = $request->query('ref') ?? $request->input('ref');

        $transaction = Transaction::with(['beneficiary','sender','fromCurrency','toCurrency','paymentMethod'])
            ->where('reference_code', $ref)
            ->orWhere('reference_code', Str::upper($ref))
            ->first();

        if (!$transaction) {
            return response()->json(['found' => false], 404);
        }

        return response()->json([
            'found' => true,
            'id' => $transaction->id,
            'reference_code' => $transaction->reference_code,
            'status' => $transaction->status,
            'amount_sent' => $transaction->amount_sent,
            'amount_received' => $transaction->amount_received,
            'fee' => $transaction->fee,
            'payout_method' => $transaction->payout_method,
            'created_at' => optional($transaction->created_at)->toDateTimeString(),
            'sender' => $transaction->sender?->name,
            'beneficiary' => $transaction->beneficiary?->name,
            'pickup_code' => $transaction->pickup_code ?? null,
            'from_currency' => $transaction->fromCurrency?->code,
            'to_currency' => $transaction->toCurrency?->code,
            'payment_provider' => $transaction->paymentMethod?->provider,
        ]);
    }


    public function receipt($id)
    {
        $user = Auth::user();
        $transaction = Transaction::with(['beneficiary','sender','paymentMethod','offer','fromCurrency','toCurrency'])->findOrFail($id);

        $isSender = $transaction->sender_id === $user->id;
        $isBeneficiaryMatch = false;

        if ($transaction->beneficiary) {
            $isBeneficiaryMatch = ($transaction->beneficiary->email && $transaction->beneficiary->email === $user->email)
                || ($transaction->beneficiary->phone && $transaction->beneficiary->phone === $user->phone);
        }

        if (!($isSender || $isBeneficiaryMatch)) {
            abort(403, 'You are not authorized to view this receipt.');
        }

        return view('user.receipt', ['transaction' => $transaction]);
    }

    
    public function viewTransaction($id)
    {
        $user = Auth::user();
        $transaction = Transaction::with(['beneficiary','sender','paymentMethod','agent','offer','fromCurrency','toCurrency'])->findOrFail($id);

        if ($user->role === 'admin') {
            return view('user.receipt', ['transaction' => $transaction]);
        }

        if ($user->role === 'agent') {
            $agentProfile = $user->agentProfile;
            if ($agentProfile && $transaction->agent_id === $agentProfile->id) {
                return view('user.receipt', ['transaction' => $transaction]);
            }
        }

        $isSender = $transaction->sender_id === $user->id;
        $isBeneficiaryMatch = false;

        if ($transaction->beneficiary) {
            $isBeneficiaryMatch = ($transaction->beneficiary->email && $transaction->beneficiary->email === $user->email)
                || ($transaction->beneficiary->phone && $transaction->beneficiary->phone === $user->phone);
        }

        if (!($isSender || $isBeneficiaryMatch)) {
            abort(403, 'You are not authorized to view this transaction.');
        }

        return view('user.receipt', ['transaction' => $transaction]);
    }


    protected function luhnCheck(string $number): bool
    {
        $sum = 0;
        $numDigits = strlen($number);
        $parity = $numDigits % 2;

        for ($i = 0; $i < $numDigits; $i++) {
            $digit = (int)$number[$i];
            if ($i % 2 == $parity) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            $sum += $digit;
        }

        return ($sum % 10) === 0;
    }


    public function store(Request $request)
    {
        $request->validate([
            'amount_sent' => 'required|numeric|min:0.01',
            'send_currency_id' => 'nullable|exists:currencies,id',
            'beneficiary_id' => 'nullable|exists:beneficiaries,id',
            'beneficiary_name' => 'required_if:beneficiary_id,|nullable|string|max:255',
            'beneficiary_email' => 'nullable|email|max:255',
            'beneficiary_phone' => 'nullable|string|max:20',
            'payout_method' => 'nullable|in:bank_deposit,cash_pickup,mobile_wallet',
            'use_wallet' => 'nullable|boolean',
        ]);

        $user = Auth::user();

        $amountInSelectedCurrency = (float) $request->input('amount_sent');
        $sendCurrencyId = $request->input('send_currency_id');
        
        $usdCurrencyId = Currency::where('code', 'USD')->value('id');
        $amount = $amountInSelectedCurrency;
        
        if ($sendCurrencyId && $sendCurrencyId !== $usdCurrencyId) {
            $svc = new ExchangeRateService();
            $rate = $svc->getRateByIds($sendCurrencyId, $usdCurrencyId);
            if ($rate) {
                $amount = round($amountInSelectedCurrency * $rate, 2);
            }
        }

        $beneficiaryId = $request->input('beneficiary_id');

        if ($beneficiaryId) {
            $beneficiary = Beneficiary::where('id', $beneficiaryId)
                ->where('user_id', $user->id)
                ->firstOrFail();
        } else {
            if (!$request->input('beneficiary_name')) {
                return back()->withErrors(['beneficiary_name' => 'Either select an existing beneficiary or enter a name for a new one.']);
            }

            $countryId = $request->input('beneficiary_country_id') ?: DB::table('countries')->value('id');

            if (!$countryId) {
                $countryId = DB::table('countries')->insertGetId([
                    'name' => 'Default Country',
                    'iso_code' => 'DEF',
                    'currency_id' => null,
                    'region' => 'Unknown'
                ]);
            }

            $existingBeneficiary = Beneficiary::where('user_id', $user->id)
                ->where(function($q) use ($request) {
                    $email = $request->input('beneficiary_email');
                    $phone = $request->input('beneficiary_phone');
                    if ($email) {
                        $q->orWhere('email', $email);
                    }
                    if ($phone) {
                        $q->orWhere('phone', $phone);
                    }
                })
                ->first();

            if ($existingBeneficiary) {
                $beneficiary = $existingBeneficiary;
            } else {
                $beneficiary = Beneficiary::create([
                    'user_id' => $user->id,
                    'name' => $request->input('beneficiary_name'),
                    'email' => $request->input('beneficiary_email'),
                    'phone' => $request->input('beneficiary_phone'),
                    'country_id' => $countryId,
                ]);
            }
        }

        if ($beneficiary->email === $user->email || $beneficiary->phone === $user->phone) {
            return back()->withErrors(['beneficiary_email' => 'You cannot send money to yourself.'])->withInput();
        }

        $payout = $request->input('payout_method') ?: 'bank_deposit';

        $amountInSelectedCurrency = (float) $request->input('amount_sent');
        $sendCurrencyId = $request->input('send_currency_id');
        
        $usdCurrencyId = Currency::where('code', 'USD')->value('id');
        $amount = $amountInSelectedCurrency;
        
        if ($sendCurrencyId && $sendCurrencyId !== $usdCurrencyId) {
            $svc = new ExchangeRateService();
            $rate = $svc->getRateByIds($sendCurrencyId, $usdCurrencyId);
            if ($rate) {
                $amount = round($amountInSelectedCurrency * $rate, 2);
            }
        }

        $exchangeRate = 1.0;
        $fee = 0.00;
        $totalPaid = $amount;
        $amountReceived = $amount;

        $toCurrencyId = $request->input('to_currency_id') ?: ($beneficiary->country?->currency_id ?? null);
        $fromCurrencyId = $request->input('from_currency_id') ?: null;
        if (!$fromCurrencyId && !empty($user->country)) {
            $fromCurrencyId = Country::where('name', $user->country)->value('currency_id');
        }
        if (!$fromCurrencyId) {
            $fromCurrencyId = Currency::value('id');
        }

        if ($fromCurrencyId && $toCurrencyId && $fromCurrencyId != $toCurrencyId) {
            $svc = new ExchangeRateService();
            $rate = $svc->getRateByIds($fromCurrencyId, $toCurrencyId);

            if ($rate) {
                $exchangeRate = $rate;
                $amountReceived = round($amount * $exchangeRate, 2);

                $feeRule = TransferFee::where('from_currency_id', $fromCurrencyId)
                    ->where('to_currency_id', $toCurrencyId)
                    ->where('effective_date', '<=', Carbon::now())
                    ->orderBy('effective_date', 'desc')
                    ->first();

                if ($feeRule) {
                    $fee = round(($feeRule->percentage_fee / 100.0) * $amount + $feeRule->fixed_fee, 2);
                }

                $totalPaid = round($amount + $fee, 2);
            }
        }

        if ($payout === 'cash_pickup') {
            $agentCommissionRate = 0.025; // 2.5%
            $agentCommission = round($amount * $agentCommissionRate, 2);
            // Apply any applicable offer discount to the agent commission (server-side)
            try {
                $applicableOffer = Offer::where('is_active', 1)
                    ->where(function($q) { $q->whereNull('start_date')->orWhere('start_date', '<=', now()); })
                    ->where(function($q) { $q->whereNull('end_date')->orWhere('end_date', '>=', now()); })
                    ->where('min_amount', '<=', $amount)
                    ->orderBy('min_amount', 'desc')
                    ->first();

                if ($applicableOffer) {
                    $agentCommission = round($agentCommission * ($applicableOffer->discount_percentage / 100.0), 2);
                }
            } catch (\Throwable $e) {
                // fallback to base commission if lookup fails
            }
            // Commission is added on top, beneficiary receives full amount
            $fee = $agentCommission;
            $totalPaid = round($amount + $fee, 2);
        }

        if ($payout === 'bank_deposit') {
            $agentCommissionRate = 0.025; // 2.5%
            $agentCommission = round($amount * $agentCommissionRate, 2);
            // Apply any applicable offer discount to the agent commission (server-side)
            try {
                $applicableOffer = Offer::where('is_active', 1)
                    ->where(function($q) { $q->whereNull('start_date')->orWhere('start_date', '<=', now()); })
                    ->where(function($q) { $q->whereNull('end_date')->orWhere('end_date', '>=', now()); })
                    ->where('min_amount', '<=', $amount)
                    ->orderBy('min_amount', 'desc')
                    ->first();

                if ($applicableOffer) {
                    $agentCommission = round($agentCommission * ($applicableOffer->discount_percentage / 100.0), 2);
                }
            } catch (\Throwable $e) {
                // fallback to base commission if lookup fails
            }
            // Commission is added on top, beneficiary receives full amount
            $fee = $agentCommission;
            $totalPaid = round($amount + $fee, 2);
        }

       
        if ($payout === 'mobile_wallet') {
            $recipient = null;

            if ($beneficiary->email) {
                $recipient = User::where('email', $beneficiary->email)->first();
            }
            if (!$recipient && $beneficiary->phone) {
                $recipient = User::where('phone', $beneficiary->phone)->first();
            }

            if (!$recipient) {
                return back()->withErrors(['beneficiary_email' => 'Recipient must be a registered user for mobile wallet.']);
            }

            if (!($user->is_email_verified && $user->is_phone_verified)) {
                return back()->withErrors(['sender' => 'Your email/phone must be verified.']);
            }
            if (!($recipient->is_email_verified && $recipient->is_phone_verified)) {
                return back()->withErrors(['beneficiary_email' => 'Recipient must have verified email/phone.']);
            }

            if (!$user->hasSufficientBalance($totalPaid)) {
                return back()->withErrors(['amount_sent' => 'Insufficient wallet balance.'])->withInput();
            }

            try {
                $transaction = null;

                DB::transaction(function () use ($user, $recipient, $amount, $fromCurrencyId, $toCurrencyId, $exchangeRate, $fee, $totalPaid, $amountReceived, $beneficiary, $payout, &$transaction) {

                   
                    $recipientCurrencyId = $recipient->currency_id ?: ($beneficiary->country?->currency_id ?? $toCurrencyId);

                    $recipientRate = 1.0;
                    $amountToDeposit = round($amount, 2);

                    if (!$user->withdraw($totalPaid)) {
                        throw new \Exception('Failed to withdraw');
                    }

                    if (!$recipient->deposit($amountToDeposit)) {
                        throw new \Exception('Failed to deposit');
                    }

                    $transaction = Transaction::create([
                        'sender_id' => $user->id,
                        'beneficiary_id' => $beneficiary->id,
                        'amount_sent' => $amount,
                        'from_currency_id' => $fromCurrencyId,
                        'to_currency_id' => $recipientCurrencyId,
                        'exchange_rate' => $recipientRate,
                        'fee' => $fee,
                        'total_paid' => $totalPaid,
                        'amount_received' => $amountToDeposit,
                        'payout_method' => $payout,
                        'status' => 'completed',
                        'reference_code' => Str::upper(Str::random(10)),
                        'completed_at' => Carbon::now(),
                    ]);

                    app(\App\Http\Controllers\NotificationController::class)->notifyAdmins(
                        'new_transaction',
                        'New Transaction Created',
                        'Transaction #' . $transaction->id . ' created by ' .
                            ($transaction->sender->first_name ?? 'Unknown') . ' ' .
                            ($transaction->sender->last_name ?? ''),
                        $transaction->id
                    );

                    $recipientBeneficiary = Beneficiary::where('user_id', $recipient->id)
                        ->where(function($q) use ($recipient) {
                            $q->where('email', $recipient->email)->orWhere('phone', $recipient->phone);
                        })->first();

                    if (!$recipientBeneficiary) {
                        $recipientBeneficiary = Beneficiary::create([
                            'user_id' => $recipient->id,
                            'name' => $beneficiary->name,
                            'email' => $recipient->email,
                            'phone' => $recipient->phone,
                            'country_id' => $beneficiary->country_id,
                        ]);
                    }

                    Transaction::create([
                        'sender_id' => $recipient->id,
                        'beneficiary_id' => $recipientBeneficiary->id,
                        'amount_sent' => 0,
                        'from_currency_id' => $fromCurrencyId,
                        'to_currency_id' => $recipientCurrencyId,
                        'exchange_rate' => $recipientRate,
                        'fee' => 0,
                        'total_paid' => 0,
                        'amount_received' => $amountToDeposit,
                        'payout_method' => $payout,
                        'status' => 'completed',
                        'reference_code' => $transaction->reference_code . '-R',
                        'completed_at' => Carbon::now(),
                    ]);
                });
            } catch (\Exception $e) {
                return back()->withErrors(['amount_sent' => 'Transfer failed: ' . $e->getMessage()])->withInput();
            }

            return redirect()->route('user.dashboard')->with('success', 'Transfer completed instantly.');
        }


     
        if ($request->input('use_wallet')) {
            try {
                $created = DB::transaction(function() use ($user, $amount, $fromCurrencyId, $toCurrencyId, $exchangeRate, $fee, $totalPaid, $amountReceived, $beneficiary, $payout) {

                    if (!$user->hasSufficientBalance($totalPaid)) {
                        throw new \Exception('insufficient_wallet');
                    }

                    if (!$user->withdraw($totalPaid)) {
                        throw new \Exception('withdrawal_failed');
                    }

                    $transaction = Transaction::create([
                        'sender_id' => $user->id,
                        'beneficiary_id' => $beneficiary->id,
                        'amount_sent' => $amount,
                        'from_currency_id' => $fromCurrencyId,
                        'to_currency_id' => $toCurrencyId,
                        'exchange_rate' => $exchangeRate,
                        'fee' => $fee,
                        'total_paid' => $totalPaid,
                        'amount_received' => $amountReceived,
                        'payout_method' => $payout,
                        'status' => 'completed',
                        'completed_at' => Carbon::now(),
                        'reference_code' => Str::upper(Str::random(10)),
                    ]);

                    app(\App\Http\Controllers\NotificationController::class)->notifyAdmins(
                        'new_transaction',
                        'New Transaction Created',
                        'Transaction #' . $transaction->id . ' created by ' .
                            ($transaction->sender->first_name ?? 'Unknown') . ' ' .
                            ($transaction->sender->last_name ?? ''),
                        $transaction->id
                    );

                    return $transaction;
                });
            } catch (\Exception $e) {
                if ($e->getMessage() === 'insufficient_wallet') {
                    return back()->withErrors(['use_wallet' => 'Insufficient wallet balance.'])->withInput();
                }
                if ($e->getMessage() === 'withdrawal_failed') {
                    return back()->withErrors(['use_wallet' => 'Failed to process wallet transaction.'])->withInput();
                }
                throw $e;
            }

            return redirect()->route('user.dashboard')->with('success', 'Transaction completed using wallet balance.');
        }


     

        $transaction = Transaction::create([
            'sender_id' => $user->id,
            'beneficiary_id' => $beneficiary->id,
            'amount_sent' => $amount,
            'from_currency_id' => $fromCurrencyId,
            'to_currency_id' => $toCurrencyId,
            'exchange_rate' => $exchangeRate,
            'fee' => $fee,
            'total_paid' => $totalPaid,
            'amount_received' => $amountReceived,
            'payout_method' => $payout,
            'status' => 'pending',
            'reference_code' => Str::upper(Str::random(10)),
        ]);

        // Deduct money from user immediately for cash_pickup to reserve funds
        if ($payout === 'cash_pickup') {
            if (!$user->withdraw($totalPaid)) {
                $transaction->delete();
                return back()->withErrors(['amount_sent' => 'Insufficient wallet balance.'])->withInput();
            }
        }

        app(\App\Http\Controllers\NotificationController::class)->notifyAdmins(
            'new_transaction',
            'New Transaction Created',
            'Transaction #' . $transaction->id . ' created by ' .
                ($transaction->sender->first_name ?? 'Unknown') . ' ' .
                ($transaction->sender->last_name ?? ''),
            $transaction->id
        );

        try {
            if ($transaction->payout_method === 'cash_pickup') {
                $countryId = $transaction->beneficiary?->country_id ?? null;
                app(\App\Http\Controllers\NotificationController::class)->notifyAgents(
                    'new_transfer_for_agent',
                    'New cash pickup available',
                    "A new cash pickup transfer is awaiting assignment.",
                    $transaction->id,
                    $countryId
                );
            }
        } catch (\Throwable $e) {
            \Log::warning('Failed to notify agents about new pending transaction', ['error' => $e->getMessage(), 'transaction_id' => $transaction->id]);
        }

        return redirect()->route('user.dashboard')->with('success', 'Transaction created! Reference: ' . $transaction->reference_code);
    }

}

