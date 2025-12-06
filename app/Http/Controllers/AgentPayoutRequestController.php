<?php

namespace App\Http\Controllers;

use App\Models\AgentPayoutRequest;
use App\Models\AgentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AgentPayoutRequestController extends Controller
{
    
    public function index()
    {
        $agent = Auth::user()->agentProfile;

        if (!$agent) {
            abort(403, "You must have an agent profile.");
        }

        $pending = AgentPayoutRequest::where('agent_profile_id', $agent->id)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $history = AgentPayoutRequest::with(['approvedBy'])
            ->where('agent_profile_id', $agent->id)
            ->whereIn('status', ['approved', 'rejected'])
            ->orderBy('approved_at', 'desc')
            ->paginate(10);

        return view('agent.payout_requests.index', compact('pending', 'history'));
    }



    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'agent_note' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $profile = $user->agentProfile;

        if (!$profile) {
            return back()->withErrors("You do not have an agent profile.");
        }

        $payout = AgentPayoutRequest::create([
            'agent_profile_id' => $profile->id,
            'user_id' => $user->id,
            'amount' => $request->amount,
            'currency_code' => $profile->currency?->code ?? 'USD',
            'agent_note' => $request->agent_note,
            'status' => 'pending',
        ]);

       
        app(NotificationController::class)->notifyAdmins(
            'payout_request',
            'New Payout Request',
            "Agent {$user->first_name} {$user->last_name} requested a payout of {$payout->amount} {$payout->currency_code}.",
            null
        );

        return back()->with('success', 'Payout request submitted successfully.');
    }




    public function adminIndex()
    {
        $requests = AgentPayoutRequest::with(['agentProfile', 'agentUser'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.payout_requests.index', compact('requests'));
    }



    public function approve($id, Request $request)
    {
        $request->validate([
            'admin_note' => 'nullable|string|max:500',
        ]);

        $payout = AgentPayoutRequest::with(['agentProfile', 'agentUser'])->findOrFail($id);

        if ($payout->status !== 'pending') {
            return back()->withErrors("This request is already processed.");
        }

        DB::transaction(function () use ($payout, $request) {

            $agentProfile = $payout->agentProfile;

            $agentProfile->creditCash(
                $payout->amount,
                null,
                "PayoutRequest#{$payout->id}"
            );

            $payout->update([
                'status' => 'approved',
                'admin_note' => $request->admin_note,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);
        });

       
        app(NotificationController::class)->notifyUser(
            $payout->user_id,
            'payout_approved',
            'Payout Approved',
            "Your payout request of {$payout->amount} {$payout->currency_code} has been approved.",
            null
        );

        return back()->with('success', 'Payout request approved. Agent balance has been updated.');
    }



    public function reject($id, Request $request)
    {
        $request->validate([
            'admin_note' => 'required|string|max:500',
        ]);

        $payout = AgentPayoutRequest::findOrFail($id);

        if ($payout->status !== 'pending') {
            return back()->withErrors("This request is already processed.");
        }

        $payout->update([
            'status' => 'rejected',
            'admin_note' => $request->admin_note,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

       
        app(NotificationController::class)->notifyUser(
            $payout->user_id,
            'payout_rejected',
            'Payout Rejected',
            "Your payout request of {$payout->amount} {$payout->currency_code} was rejected. Reason: {$request->admin_note}",
            null
        );

        return back()->with('success', 'Payout request rejected.');
    }
}
