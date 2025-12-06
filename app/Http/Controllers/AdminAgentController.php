<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AgentProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\AgentApproved;
use App\Mail\AgentRejected;

class AdminAgentController extends Controller
{
    public function approve($id)
    {
        Log::info('AdminAgentController@approve called', ['id' => $id, 'user_id' => auth()->id()]);
        $profile = AgentProfile::with('user')->findOrFail($id);

        DB::transaction(function() use ($profile) {
            $profile->is_approved = 1;
            $profile->save();

            if ($profile->user) {
                $user = $profile->user;
                $user->role = 'agent';
                $user->status = 'active';
                $user->save();
            }
        });

        Log::info('Agent profile approved', ['profile_id' => $profile->id, 'user_id' => $profile->user ? $profile->user->id : null]);

        if ($profile->user) {
            if (class_exists(\App\Models\Notification::class)) {
                try {
                    \App\Models\Notification::create([
                        'user_id' => $profile->user->id,
                        'type' => 'agent_approved',
                        'title' => 'Agent approved',
                        'message' => 'Your agent profile has been approved. You can now accept transfer requests.',
                        'related_transaction_id' => null,
                        'is_read' => false,
                    ]);
                } catch (\Throwable $e) {
                    \Log::warning('Failed to create in-app notification for agent approval', ['error' => $e->getMessage(), 'profile_id' => $profile->id]);
                }
            }

            if (filter_var($profile->user->email, FILTER_VALIDATE_EMAIL)) {
                try {
                    Mail::to($profile->user->email)->send(new AgentApproved($profile->user, $profile));
                } catch (\Exception $e) {
                    Log::error('Mail send failed for AgentApproved', ['error' => $e->getMessage(), 'profile_id' => $profile->id]);
                }
            }
        }

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Agent approved and user updated successfully.',
                'profile' => [
                    'id' => $profile->id,
                    'is_approved' => $profile->is_approved,
                ],
                'user' => $profile->user ? [
                    'id' => $profile->user->id,
                    'role' => $profile->user->role,
                    'status' => $profile->user->status,
                ] : null,
            ]);
        }

        return redirect()->back()->with('status', 'Agent approved and user updated successfully.');
    }

    public function reject($id)
    {
        Log::info('AdminAgentController@reject called', ['id' => $id, 'user_id' => auth()->id()]);
        $profile = AgentProfile::with('user')->findOrFail($id);

        DB::transaction(function() use ($profile) {
            $profile->is_approved = -1;
            $profile->save();

            if ($profile->user) {
                $user = $profile->user;
                if ($user->role !== 'admin') {
                    $user->role = 'user';
                }
                $user->status = 'suspended';  
                $user->save();
            }
        });

        Log::info('Agent profile rejected', ['profile_id' => $profile->id, 'user_id' => $profile->user ? $profile->user->id : null]);

        if ($profile->user && filter_var($profile->user->email, FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::to($profile->user->email)->send(new AgentRejected($profile->user, $profile));
            } catch (\Exception $e) {
                Log::error('Mail send failed for AgentRejected', ['error' => $e->getMessage(), 'profile_id' => $profile->id]);
            }
        }

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Agent rejected and user updated.',
                'profile' => [
                    'id' => $profile->id,
                    'is_approved' => $profile->is_approved,
                ],
                'user' => $profile->user ? [
                    'id' => $profile->user->id,
                    'role' => $profile->user->role,
                    'status' => $profile->user->status,
                ] : null,
            ]);
        }

        return redirect()->back()->with('status', 'Agent rejected and user updated.');
    }
}
