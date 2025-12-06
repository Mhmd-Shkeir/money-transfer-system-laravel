<?php

namespace App\Http\Controllers;

use App\Models\RefundRequest;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RefundRequestController extends Controller
{
    /**
     * Store a new refund request
     */
    public function store(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'reason' => 'required|string|min:10|max:1000',
        ]);

        $user = Auth::user();
        $transaction = Transaction::findOrFail($request->input('transaction_id'));

        // Verify the user is the sender
        if ($transaction->sender_id !== $user->id) {
            return back()->withErrors(['transaction' => 'You are not authorized to request a refund for this transaction.']);
        }

        // Verify transaction is pending and cash_pickup
        if ($transaction->status !== 'pending') {
            return back()->withErrors(['transaction' => 'Only pending transactions can be refunded.']);
        }

        if ($transaction->payout_method !== 'cash_pickup') {
            return back()->withErrors(['transaction' => 'Refunds are only available for cash pickup transactions.']);
        }

        // Check if a refund request already exists
        $existingRequest = RefundRequest::where('transaction_id', $transaction->id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return back()->withErrors(['transaction' => 'A refund request for this transaction is already pending.']);
        }

        // Create refund request
        $refundRequest = RefundRequest::create([
            'transaction_id' => $transaction->id,
            'user_id' => $user->id,
            'reason' => $request->input('reason'),
            'status' => 'pending',
        ]);

        // Notify admins
        try {
            app(NotificationController::class)->notifyAdmins(
                'refund_request',
                'New Refund Request',
                'User ' . $user->first_name . ' ' . $user->last_name . ' requested a refund for transaction #' . $transaction->id,
                $refundRequest->id
            );
        } catch (\Throwable $e) {
            Log::warning('Failed to notify admins about refund request', ['error' => $e->getMessage(), 'refund_request_id' => $refundRequest->id]);
        }

        return back()->with('success', 'Your refund request has been submitted. Admins will review it shortly.');
    }

    /**
     * Show admin dashboard for refund requests
     */
    public function adminIndex()
    {
        $pendingRequests = RefundRequest::with(['transaction', 'user'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(15);

        $resolvedRequests = RefundRequest::with(['transaction', 'user', 'reviewer'])
            ->whereIn('status', ['approved', 'rejected'])
            ->latest()
            ->paginate(15);

        return view('admin.refund_requests.index', compact('pendingRequests', 'resolvedRequests'));
    }

    /**
     * Show details of a specific refund request
     */
    public function show($id)
    {
        $refundRequest = RefundRequest::with(['transaction', 'user', 'reviewer'])->findOrFail($id);

        return view('admin.refund_requests.show', compact('refundRequest'));
    }

    /**
     * Approve a refund request
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $refundRequest = RefundRequest::findOrFail($id);

        if ($refundRequest->status !== 'pending') {
            return back()->withErrors(['status' => 'This refund request has already been reviewed.']);
        }

        try {
            DB::transaction(function () use ($refundRequest, $request) {
                $transaction = $refundRequest->transaction;
                $user = $refundRequest->user;

                // Update refund request status
                $refundRequest->update([
                    'status' => 'approved',
                    'reviewed_by' => Auth::id(),
                    'reviewed_at' => Carbon::now(),
                    'admin_notes' => $request->input('admin_notes'),
                ]);

                // Update transaction status to cancelled
                $transaction->update([
                    'status' => 'cancelled',
                ]);

                // If money was already deducted for cash pickup, return it to the sender
                if ($transaction->payout_method === 'cash_pickup') {
                    $sender = $transaction->sender;
                    $refundAmount = floatval($transaction->total_paid ?? $transaction->amount_sent ?? 0);
                    if ($sender && $refundAmount > 0) {
                        $credited = $sender->deposit($refundAmount);
                        if (! $credited) {
                            throw new \Exception('Failed to credit sender wallet');
                        }
                    }
                }

                // Notify the user
                app(NotificationController::class)->notifyUser(
                    $user->id,
                    'refund_approved',
                    'Your Refund Request Approved',
                    'Your refund request for transaction #' . $transaction->id . ' has been approved and cancelled. The amount has been returned to your wallet.',
                    $transaction->id
                );
            });
        } catch (\Exception $e) {
            Log::error('Failed to approve refund request', ['error' => $e->getMessage(), 'refund_request_id' => $id]);
            return back()->withErrors(['error' => 'Failed to process refund. Please try again.']);
        }

        return back()->with('success', 'Refund request approved. Transaction has been cancelled.');
    }

    /**
     * Reject a refund request
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => 'required|string|min:10|max:500',
        ]);

        $refundRequest = RefundRequest::findOrFail($id);

        if ($refundRequest->status !== 'pending') {
            return back()->withErrors(['status' => 'This refund request has already been reviewed.']);
        }

        $refundRequest->update([
            'status' => 'rejected',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => Carbon::now(),
            'admin_notes' => $request->input('admin_notes'),
        ]);

        // Notify the user
        try {
            $transaction = $refundRequest->transaction;
            app(NotificationController::class)->notifyUser(
                $refundRequest->user_id,
                'refund_rejected',
                'Your Refund Request Rejected',
                'Your refund request for transaction #' . $transaction->id . ' has been rejected. Reason: ' . $request->input('admin_notes'),
                $transaction->id
            );
        } catch (\Throwable $e) {
            Log::warning('Failed to notify user about refund rejection', ['error' => $e->getMessage(), 'refund_request_id' => $id]);
        }

        return back()->with('success', 'Refund request rejected.');
    }
}
