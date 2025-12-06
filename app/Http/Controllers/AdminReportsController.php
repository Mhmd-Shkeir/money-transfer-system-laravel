<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Models\Compliance;
class AdminReportsController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalAgents = User::where('role', 'agent')->count();
        $totalClients = User::whereIn('role', ['user', 'client'])->count();
        $activeClients = User::whereIn('role', ['user', 'client'])->where('status', 'active')->count();
        $suspendedClients = $totalClients - $activeClients;
        $activeAgents = User::where('role', 'agent')->where('status', 'active')->count();
        $suspendedAgents = $totalAgents - $activeAgents;

        $totalTransactions = Transaction::count();
        $completedTransactions = Transaction::where('status', 'completed')->count();
        $pendingTransactions = Transaction::where('status', 'pending')->count();
        $totalRevenue = Transaction::sum('fee');

        $totalCurrencies = Currency::count();
        $totalRates = ExchangeRate::count();
        $totalCompliance = Compliance::count();

        $transactionsPerMonth = Transaction::selectRaw("MONTH(created_at) as month, COUNT(*) as total")
            ->groupBy('month')
            ->pluck('total', 'month');

        $revenuePerMonth = Transaction::selectRaw("MONTH(created_at) as month, SUM(fee) as total")
            ->groupBy('month')
            ->pluck('total', 'month');

        $currencyUsage = Transaction::selectRaw("from_currency_id, COUNT(*) as total")
            ->groupBy('from_currency_id')
            ->pluck('total', 'from_currency_id');

        $recentTransactions = Transaction::orderBy('created_at', 'desc')->take(6)->get();
        $recentCompliance = Compliance::orderBy('created_at', 'desc')->take(6)->get();

        $clients = User::whereIn('role', ['user', 'client'])->with('agentProfile')->orderBy('created_at', 'desc')
            ->paginate(15, ['*'], 'clients_page');

        $agentsList = User::where('role', 'agent')->with('agentProfile')->orderBy('created_at', 'desc')
            ->paginate(15, ['*'], 'agents_page');

        $topClients = User::whereIn('role', ['user', 'client'])->orderBy('created_at', 'desc')->take(5)->get();
        $topAgents = User::where('role', 'agent')->orderBy('created_at', 'desc')->take(5)->get();

        return view('admin.reports', compact(
            'totalUsers', 'totalClients', 'activeClients', 'suspendedClients', 'totalAgents', 'activeAgents', 'suspendedAgents', 'totalTransactions', 'completedTransactions',
            'pendingTransactions', 'totalRevenue', 'totalCurrencies', 'totalRates',
            'totalCompliance', 'transactionsPerMonth', 'revenuePerMonth',
            'currencyUsage', 'recentTransactions', 'recentCompliance', 'clients', 'agentsList', 'topClients', 'topAgents'
        ));
    }

    public function clients()
    {
        $users = User::whereIn('role', ['user', 'client'])->with('agentProfile')->orderBy('created_at', 'desc')->paginate(25);
        return view('admin.reports_users', ['users' => $users, 'title' => 'Clients']);
    }

   
    public function agents()
    {
        $users = User::where('role', 'agent')->with('agentProfile')->orderBy('created_at', 'desc')->paginate(25);
        return view('admin.reports_users', ['users' => $users, 'title' => 'Agents']);
    }
}
