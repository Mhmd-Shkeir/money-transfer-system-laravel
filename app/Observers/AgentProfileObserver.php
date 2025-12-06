<?php

namespace App\Observers;

use App\Models\AgentProfile;
use App\Models\User;

class AgentProfileObserver
{
    public function created(AgentProfile $profile)
    {
        // Ensure the linked user exists and has role 'agent'
        if ($profile->user) {
            $user = $profile->user;
            if ($user->role !== 'agent') {
                $user->role = 'agent';
                $user->save();
            }

            // If agent profile has initial cash_balance, mirror to user wallet
            $initial = $profile->cash_balance ?? 0;
            if ($initial > 0) {
                try {
                    $user->deposit($initial);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('AgentProfileObserver: failed to deposit initial cash to user', ['agent_profile_id' => $profile->id, 'user_id' => $user->id, 'error' => $e->getMessage()]);
                }
            }
        }
    }

    public function updated(AgentProfile $profile)
    {
        // If user_id changed, ensure roles/consistency
        if ($profile->wasChanged('user_id')) {
            if ($profile->user) {
                $u = $profile->user;
                if ($u->role !== 'agent') {
                    $u->role = 'agent';
                    $u->save();
                }
            }
        }
    }

    public function deleted(AgentProfile $profile)
    {
        // Optionally revert user role on delete — keep as-is for safety
    }
}
