<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AgentProfile;

class AgentDashboardController extends Controller
{
    
    public function index()
    {
        $user = Auth::user();

        $agentProfile = $user->agentProfile;

        if (!$agentProfile) {
            $agentProfile = new AgentProfile([
                'cash_balance' => 0.00,
                'total_cash_in' => 0.00,
                'total_cash_out' => 0.00,
                'business_name' => null,
                'store_name' => null,
                'business_registration_number' => null,
                'tax_id' => null,
                'business_description' => null,
                'is_approved' => false,
                'commission_rate' => 0,
                'country_id' => null,
                'currency_id' => null,
            ]);
        }

        $agent = $agentProfile;

        $agentCommissionTotal = 0;
        if ($agentProfile && $agentProfile->id && class_exists(\App\Models\Commission::class)) {
            $agentCommissionTotal = \App\Models\Commission::where('agent_id', $agentProfile->id)->sum('commission_amount');
        }

        return view('agent.dashboard', compact('user', 'agent', 'agentProfile', 'agentCommissionTotal'));
    }
}
