<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use App\Models\Beneficiary;
use App\Models\User;

class DebugNotificationController extends Controller
{
    
    public function notify(Request $request)
    {
        if (!app()->environment('local')) {
            abort(403, 'Not available');
        }

        $request->validate([
            'sender_id' => 'required|exists:users,id',
            'beneficiary_id' => 'required|exists:beneficiaries,id',
            'amount' => 'nullable|numeric',
            'from_currency_id' => 'nullable|exists:currencies,id',
            'to_currency_id' => 'nullable|exists:currencies,id',
        ]);

        $sender = User::find($request->input('sender_id'));
        $beneficiary = Beneficiary::with('country')->find($request->input('beneficiary_id'));
        $amount = $request->input('amount', 100);

        $fromCurrencyId = $request->input('from_currency_id') ?: ($sender->currency_id ?: DB::table('currencies')->value('id'));
        $toCurrencyId = $request->input('to_currency_id') ?: ($beneficiary->country?->currency_id ?: $fromCurrencyId);

        $transaction = null;
        DB::transaction(function() use ($sender, $beneficiary, $amount, $fromCurrencyId, $toCurrencyId, &$transaction) {
            $transaction = Transaction::create([
                'sender_id' => $sender->id,
                'beneficiary_id' => $beneficiary->id,
                'amount_sent' => $amount,
                'from_currency_id' => $fromCurrencyId,
                'to_currency_id' => $toCurrencyId,
                'exchange_rate' => 1,
                'fee' => 0,
                'total_paid' => $amount,
                'amount_received' => $amount,
                'payout_method' => 'cash_pickup',
                'status' => 'pending',
                'reference_code' => Str::upper(Str::random(10)),
            ]);

            app(\App\Http\Controllers\NotificationController::class)->notifyAdmins(
                'new_transaction',
                'New Transaction Created',
                'Transaction #' . $transaction->id . ' created by ' . ($transaction->sender->first_name ?? 'Unknown'),
                $transaction->id
            );

            try {
                $countryId = $beneficiary->country_id ?? null;
                app(\App\Http\Controllers\NotificationController::class)->notifyAgents(
                    'new_transfer_for_agent',
                    'New cash pickup available',
                    "A new cash pickup transfer is awaiting assignment.",
                    $transaction->id,
                    $countryId
                );
            } catch (\Throwable $e) {
                Log::warning('Debug notify: failed to notify agents', ['error' => $e->getMessage()]);
            }
        });

        $notifications = \App\Models\Notification::where('related_transaction_id', $transaction->id)->get();

        return response()->json([
            'transaction' => $transaction,
            'notifications' => $notifications,
        ]);
    }

    public function list($userId)
    {
        if (!app()->environment('local')) {
            abort(403, 'Not available');
        }

        $notifications = \App\Models\Notification::where('user_id', $userId)->orderByDesc('created_at')->get();
        return response()->json(['user_id' => $userId, 'notifications' => $notifications]);
    }

    public function me(Request $request)
    {
        if (!app()->environment('local')) {
            abort(403, 'Not available');
        }

        if (!auth()->check()) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        $user = auth()->user();
        $notifications = \App\Models\Notification::where('user_id', $user->id)->orderByDesc('created_at')->get();
        return response()->json(['user_id' => $user->id, 'notifications' => $notifications]);
    }
}
