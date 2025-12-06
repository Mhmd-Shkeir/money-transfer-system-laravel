<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\User;
use App\Models\AgentProfile;
use App\Models\AdminCommission;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalRevenue = Transaction::sum('fee');
        $totalUsers = User::count();
        $monthTransfers = Transaction::whereMonth('created_at', now()->month)->count();
        $activeAgents = User::where('role', 'agent')->count();

        $recentUsers = User::latest()->take(8)->get();

        $pendingAgents = AgentProfile::where('is_approved', 0)->with('user')->get();

        $adminCommissionTotal = 0;
        if (class_exists(AdminCommission::class)) {
            $adminCommissionTotal = AdminCommission::sum('amount');
        }

        return view('admin.dashboard', compact(
            'totalRevenue', 'totalUsers', 'monthTransfers', 'activeAgents', 'recentUsers', 'pendingAgents', 'adminCommissionTotal'
        ));
    }
}
