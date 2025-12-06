<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Currency;
use App\Services\ExchangeRateService;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $totalSent = Transaction::where('sender_id', $user->id)
            ->whereMonth('created_at', now()->month)
            ->sum('amount_sent') ?? 0;

        $transactionsCount = Transaction::where('sender_id', $user->id)->count();
        $beneficiariesCount = $user->beneficiaries()->count();
        $savedFees = Transaction::where('sender_id', $user->id)->sum('fee') ?? 0;

        $recent = Transaction::where('sender_id', $user->id)->latest()->limit(5)->get();

        $walletBalanceDisplay = $user->getBalanceInPreferredCurrency();
        
        $currencies = Currency::all();
        
        return view('user.dashboard', compact('totalSent','transactionsCount','beneficiariesCount','savedFees','recent','walletBalanceDisplay','currencies'));
    }
    
  
    public function updateCurrency(Request $request)
    {
        $request->validate([
            'currency_id' => 'required|exists:currencies,id'
        ]);

        $user = Auth::user();
        $newCurrencyId = $request->currency_id;
        
        if ($user->currency_id == $newCurrencyId) {
            return redirect()->back()->with('success', 'Currency preference updated successfully!');
        }
        
        $currentCurrency = $user->getPreferredCurrency();
        $newCurrency = Currency::find($newCurrencyId);
        
        if (!$currentCurrency || !$newCurrency) {
            return redirect()->back()->with('error', 'Invalid currency selection.');
        }
        
        $user->currency_id = $newCurrencyId;

        if (!$user->save()) {
            return redirect()->back()->with('error', 'Failed to update currency preference.');
        }

        return redirect()->back()->with('success', 'Currency preference updated successfully!');
    }
}
