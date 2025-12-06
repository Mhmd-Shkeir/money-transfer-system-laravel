<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Str;
use App\Mail\TransferAssignedToAgent;
use App\Mail\TransferCompletedForSender;
use Illuminate\Support\Facades\DB;

class AgentTransactionController extends Controller
{
   
    public function incomingRequests(\Illuminate\Http\Request $request)
    {
        $agent = Auth::user();
        $agentProfile = $agent->agentProfile;

        if (!$agentProfile || !$agentProfile->is_approved) {
            return redirect()->route('agent.dashboard')
                ->with('error', 'Your agent profile must be approved to view transfer requests.');
        }

        $ref = trim($request->query('ref', ''));

        if (!empty($ref)) {
            $refUpper = Str::upper($ref);
            $tx = Transaction::where('transaction_type', 'transfer')
                ->where('status', 'pending')
                ->where(function ($query) {
                    $query->where('payout_method', 'cash_pickup')
                          ->orWhereNull('payout_method');
                })
                ->whereRaw('UPPER(reference_code) = ?', [$refUpper])
                ->with(['beneficiary', 'sender'])
                ->first();

            $items = $tx ? collect([$tx]) : collect();
            $perPage = 10;
            $incomingRequests = new \Illuminate\Pagination\LengthAwarePaginator(
                $items->forPage(1, $perPage),
                $items->count(),
                $perPage,
                1,
                [
                    'path' => url()->current(),
                    'query' => $request->query(),
                ]
            );
        } else {
            $items = collect();
            $perPage = 10;
            $incomingRequests = new \Illuminate\Pagination\LengthAwarePaginator(
                $items->forPage(1, $perPage),
                0,
                $perPage,
                1,
                [
                    'path' => url()->current(),
                ]
            );
        }

        return view('agent.incoming-requests', compact('incomingRequests', 'agentProfile'));
    }

  
    public function acceptRequest($transactionId)
    {
        $agent = Auth::user();
        $agentProfile = $agent->agentProfile;

        if (! $agentProfile) {
            return redirect()->back()->with('error', 'Agent profile not found.');
        }

        $transaction = Transaction::findOrFail($transactionId);

        if ($transaction->status !== 'pending') {
            return redirect()->back()->with('error', 'This request cannot be accepted.');
        }

        $sender = $transaction->sender;
        if (! $sender) {
            return redirect()->back()->with('error', 'Sender account not found.');
        }

        $chargeAmount = floatval($transaction->total_paid ?? $transaction->amount_sent ?? 0);

        try {
            DB::beginTransaction();

            // For cash_pickup, money was already withdrawn when transaction was created
            // For other methods, withdraw now when agent accepts
            if ($chargeAmount > 0 && $transaction->payout_method !== 'cash_pickup') {
                $withdrawn = $sender->withdraw($chargeAmount);
                if (! $withdrawn) {
                    DB::rollBack();
                    return redirect()->back()->withErrors(['sender' => 'Sender has insufficient funds to complete this request.']);
                }
            }

            $transaction->agent_id = $agentProfile->id;
            $transaction->status = 'processing';
            $transaction->save();

            if ($chargeAmount > 0) {
                // Credit the agent when they accept the transfer. For cash_pickup the funds
                // were reserved at transaction creation so we credit the agent now so their
                // cash balance reflects the assigned transfer.
                $agentProfile->creditCash($chargeAmount, $transaction->id, $transaction->reference_code ?? null);
            }

            try {
                if (class_exists(\App\Models\Notification::class)) {
                    \App\Models\Notification::create([
                        'user_id' => $agent->id,
                        'type' => 'app',
                        'title' => 'Transfer assigned to you',
                            'message' => "You accepted the transfer. Sender was charged {$chargeAmount}.",
                        'related_transaction_id' => $transaction->id,
                    ]);
                }
            } catch (\Throwable $e) {
                \Log::warning('Failed to create accept notification', ['error' => $e->getMessage(), 'transaction_id' => $transaction->id]);
            }

            DB::commit();

            \Log::info('Agent acceptRequest: assigned transaction', [
                'agent_profile_id' => $agentProfile->id,
                'transaction_id' => $transaction->id,
                'payout_method' => $transaction->payout_method,
                'amount_charged' => number_format($chargeAmount, 2),
            ]);

            return redirect()->back()->with('success', 'Transfer assigned successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Failed to accept request', ['error' => $e->getMessage(), 'transaction_id' => $transaction->id]);
            return redirect()->back()->with('error', 'Failed to accept request: ' . $e->getMessage());
        }
    }


    public function rejectRequest($transactionId)
    {
        $agent = Auth::user();
        $agentProfile = $agent->agentProfile;

        if (!$agentProfile) {
            return redirect()->back()->with('error', 'Agent profile not found.');
        }

        $transaction = Transaction::findOrFail($transactionId);

        if ($transaction->status !== 'pending') {
            return redirect()->back()->with('error', 'This request cannot be rejected.');
        }

        try {
            DB::beginTransaction();

            // If this was a cash_pickup, the money was withdrawn at creation to reserve it.
            // When an agent rejects the request we must refund the sender.
            if ($transaction->payout_method === 'cash_pickup') {
                $refundAmount = $transaction->total_paid ?? $transaction->amount_sent ?? 0;
                $sender = $transaction->sender;
                if ($refundAmount > 0 && $sender) {
                    $sender->deposit($refundAmount);
                }
            }

            $transaction->agent_id = null;
            $transaction->status = 'cancelled';
            $transaction->save();

            try {
                if (class_exists(\App\Models\Notification::class)) {
                    \App\Models\Notification::create([
                        'user_id' => $agent->id,
                        'type' => 'app',
                        'title' => 'Transfer declined',
                        'message' => 'You declined the transfer request #' . ($transaction->id ?? '') . '.',
                        'related_transaction_id' => $transaction->id,
                    ]);
                }
            } catch (\Throwable $e) {
                \Log::warning('Failed to create reject notification', ['error' => $e->getMessage(), 'transaction_id' => $transaction->id]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Failed to reject and refund transaction', ['error' => $e->getMessage(), 'transaction_id' => $transaction->id]);
            return redirect()->back()->with('error', 'Failed to decline request: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Transfer request declined and sender refunded.');
    }


    public function cashIn(Request $request)
    {
        $agent = Auth::user();
        $agentProfile = $agent->agentProfile;

        if (!$agentProfile) {
            return redirect()->back()->with('error', 'Agent profile not found.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $transaction = Transaction::create([
            'sender_id' => $agent->id,
            'agent_id' => $agentProfile->id,
            'amount_sent' => $validated['amount'],
            'amount_received' => $validated['amount'],
            'total_paid' => $validated['amount'],
            'payout_method' => 'cash_pickup',
            'status' => 'completed',
            'transaction_type' => 'cash_in',
            'cash_reference' => $validated['reference'] ?? 'CASH-IN-' . Str::upper(Str::random(8)),
            'reference_code' => Str::upper(Str::random(10)),
            'customer_name' => $validated['customer_name'],
            'notes' => $validated['notes'] ?? null,
        ]);

            $creditAmount = $transaction->amount_sent ?? 0;
            if ($creditAmount > 0) {
                $agentProfile->creditCash($creditAmount, $transaction->id, $transaction->reference_code ?? null);
            }

        return redirect()->back()
            ->with('success', "Cash-in recorded: {$validated['amount']} received. Reference: " . $transaction->cash_reference);
    }

 
public function cashOut(Request $request)
{
    $agent = Auth::user();
    $agentProfile = $agent->agentProfile;

    if (!$agentProfile) {
        return redirect()->back()->with('error', 'Agent profile not found.');
    }

    $validated = $request->validate([
        'transaction_id' => 'nullable|exists:transactions,id',
        'amount' => 'required|numeric|min:0.01',
        'recipient_name' => 'required|string|max:255',
        'recipient_phone' => 'nullable|string|max:20',
        'reference' => 'nullable|string|max:100',
        'notes' => 'nullable|string|max:500',
    ]);

    if ($agentProfile->cash_balance < $validated['amount']) {

        if (class_exists(\App\Models\Notification::class)) {
            \App\Models\Notification::create([
                'user_id' => $agent->id,
                'type' => 'app',
                'title' => 'Payout Failed',
                'message' => 'Payout failed: insufficient agent cash balance.',
            ]);
        }

        return redirect()->back()
            ->withErrors(['amount' => 'Insufficient cash balance. Available: ' . $agentProfile->cash_balance]);
    }

 
    if ($request->input('transaction_id')) {

        $transaction = Transaction::findOrFail($validated['transaction_id']);

        if ($transaction->status === 'completed') {
            return redirect()->back()->with('info', 'This transaction is already completed.');
        }

        if ($transaction->agent_id !== $agentProfile->id || $transaction->status !== 'processing') {

            if (class_exists(\App\Models\Notification::class)) {
                \App\Models\Notification::create([
                    'user_id' => $agent->id,
                    'type' => 'app',
                    'title' => 'Payout Failed',
                    'message' => 'Invalid transaction assignment for payout.',
                    'related_transaction_id' => $transaction->id,
                ]);
            }

            return redirect()->back()->with('error', 'Invalid transaction for payout.');
        }

        $recipientUser = null;

        if ($transaction->beneficiary) {
            $benef = $transaction->beneficiary;

            if (!empty($benef->email)) {
                $recipientUser = User::where('email', $benef->email)->first();
            }

            if (!$recipientUser && !empty($benef->phone)) {
                $recipientUser = User::where('phone', $benef->phone)->first();
            }
        }

     
        $recipientVerified = (
            $recipientUser &&
            ($recipientUser->status ?? '') === 'active' &&
            ($recipientUser->is_phone_verified ?? 0) &&
            ($recipientUser->is_email_verified ?? 0)
        );

       
        if (!$recipientVerified) {

            if (class_exists(\App\Models\Notification::class)) {
                \App\Models\Notification::create([
                    'user_id' => $agent->id,
                    'type' => 'app',
                    'title' => 'Payout Failed',
                    'message' => !$recipientUser
                        ? 'Transfer payout blocked: recipient not found.'
                        : 'Transfer payout blocked: recipient MUST have active status, verified phone, AND verified email.',
                    'related_transaction_id' => $transaction->id,
                ]);
            }

            return redirect()->back()->withErrors([
                'recipient' => 'Recipient must have active status, verified phone, AND verified email before payout.'
            ]);
        }

      

        $transaction->status = 'completed';
        $transaction->completed_at = now();
        $transaction->notes = $validated['notes'] ?? $transaction->notes;
        $transaction->save();

        $depositAmount = $transaction->amount_received ?? ($transaction->amount_sent ?? 0);

        if ($depositAmount > 0) {
            $recipientUser->deposit($depositAmount);
        }

        $recipientBeneficiary = \App\Models\Beneficiary::firstOrCreate(
            [
                'user_id' => $recipientUser->id,
                'phone' => $recipientUser->phone,
            ],
            [
                'name' => $recipientUser->first_name . ' ' . $recipientUser->last_name,
                'email' => $recipientUser->email,
                'country_id' => $transaction->beneficiary?->country_id ?? null,
            ]
        );

        Transaction::create([
            'sender_id' => $recipientUser->id,
            'beneficiary_id' => $recipientBeneficiary->id,
            'amount_sent' => 0,
            'from_currency_id' => $transaction->from_currency_id,
            'to_currency_id' => $transaction->to_currency_id,
            'exchange_rate' => $transaction->exchange_rate,
            'fee' => 0,
            'total_paid' => 0,
            'amount_received' => $depositAmount,
            'payout_method' => $transaction->payout_method,
            'status' => 'completed',
            'reference_code' => $transaction->reference_code . '-R',
            'completed_at' => now(),
        ]);

        $agentProfile->debitCash($validated['amount'], $transaction->id, $transaction->reference_code);

        return redirect()->back()
            ->with('success', 'Payout completed successfully.');
    }

 

    $recipientUser = null;

    if (!empty($validated['recipient_phone'])) {
        $recipientUser = User::where('phone', $validated['recipient_phone'])->first();
    }

    if (!empty($validated['recipient_phone']) && $validated['recipient_phone'] === ($agent->phone ?? '')) {
        return redirect()->back()->withErrors(['recipient_phone' => 'You cannot cash-out to your own phone.']);
    }

    $recipientVerified = (
        $recipientUser &&
        ($recipientUser->status ?? '') === 'active' &&
        ($recipientUser->is_phone_verified ?? 0) &&
        ($recipientUser->is_email_verified ?? 0)
    );

    if (!$recipientVerified) {

        if (class_exists(\App\Models\Notification::class)) {
            \App\Models\Notification::create([
                'user_id' => $agent->id,
                'type' => 'app',
                'title' => 'Cash-out Failed',
                'message' => !$recipientUser
                    ? 'Cash-out failed: recipient not found.'
                    : 'Cash-out failed: recipient must have ACTIVE status, VERIFIED phone, and VERIFIED email.',
            ]);
        }

        return redirect()->back()->withErrors([
            'recipient' => 'Recipient must have active status, verified phone, and verified email to receive funds.'
        ]);
    }

 
    $transaction = Transaction::create([
        'sender_id' => $agent->id,
        'agent_id' => $agentProfile->id,
        'amount_sent' => $validated['amount'],
        'amount_received' => $validated['amount'],
        'total_paid' => $validated['amount'],
        'payout_method' => 'mobile_wallet',
        'status' => 'completed',
        'transaction_type' => 'cash_out',
        'cash_reference' => $validated['reference'] ?? 'CASH-OUT-' . Str::upper(Str::random(8)),
        'reference_code' => Str::upper(Str::random(10)),
        'recipient_name' => $validated['recipient_name'],
        'notes' => $validated['notes'] ?? null,
    ]);

    $recipientUser->deposit($validated['amount']);

    $recipientBeneficiary = \App\Models\Beneficiary::firstOrCreate(
        [
            'user_id' => $recipientUser->id,
            'phone' => $recipientUser->phone,
        ],
        [
            'name' => $validated['recipient_name'],
            'email' => $recipientUser->email,
            'country_id' => null,
        ]
    );

    Transaction::create([
        'sender_id' => $recipientUser->id,
        'beneficiary_id' => $recipientBeneficiary->id,
        'amount_sent' => 0,
        'from_currency_id' => $transaction->from_currency_id,
        'to_currency_id' => $transaction->to_currency_id,
        'exchange_rate' => $transaction->exchange_rate,
        'fee' => 0,
        'total_paid' => 0,
        'amount_received' => $validated['amount'],
        'payout_method' => 'mobile_wallet',
        'status' => 'completed',
        'reference_code' => $transaction->reference_code . '-R',
        'completed_at' => now(),
    ]);

    $agentProfile->debitCash($validated['amount'], $transaction->id, $transaction->reference_code);

    return redirect()->back()
        ->with('success', "Cash-out completed successfully.");
}



    public function cashBalance()
    {
        $agent = Auth::user();
        $agentProfile = $agent->agentProfile;

        if (!$agentProfile) {
            return redirect()->route('agent.dashboard')
                ->with('error', 'Agent profile not found.');
        }

        $cashTransactions = Transaction::where('agent_id', $agentProfile->id)
            ->whereIn('transaction_type', ['cash_in', 'cash_out', 'transfer'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('agent.cash-balance', compact('agentProfile', 'cashTransactions'));
    }

    public function transactions()
    {
        $agent = Auth::user();
        $agentProfile = $agent->agentProfile;

        if (! $agentProfile) {
            return redirect()->route('agent.dashboard')->with('error', 'Agent profile not found.');
        }

        $transactions = Transaction::where('agent_id', $agentProfile->id)
            ->whereIn('transaction_type', ['cash_in', 'cash_out', 'transfer'])
            ->with(['sender','beneficiary'])
            ->orderByDesc('created_at')
            ->paginate(15);

        $totalTransactions = Transaction::where('agent_id', $agentProfile->id)->count();
        $totalVolume = Transaction::where('agent_id', $agentProfile->id)->sum('total_paid');

        $pendingCount = Transaction::where('transaction_type', 'transfer')
            ->where('status', 'pending')
            ->where(function ($query) {
                $query->where('payout_method', 'cash_pickup')
                      ->orWhereNull('payout_method');
            })
            ->count();

        return view('agent.transactions', compact('agentProfile','transactions','totalTransactions','totalVolume','pendingCount'));
    }
}
