<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;

class AdminExchangeRateController extends Controller
{
    public function index()
    {
        $rates = ExchangeRate::with(['fromCurrency', 'toCurrency'])->get();
        $currencies = Currency::all();

        return view('admin.rates', compact('rates', 'currencies'));
    }

    public function store(Request $request)
{
    $request->validate([
        'from_currency_id' => 'required|exists:currencies,id',
        'to_currency_id'   => 'required|exists:currencies,id',
        'rate'             => 'required|numeric|min:0.0000001',
    ]);

    // Prevent same currency → same currency
    if ($request->from_currency_id == $request->to_currency_id) {
        return back()->with('error', 'Cannot create rate for the same currency.');
    }

    // Check if this rate already exists
    $existing = ExchangeRate::where('from_currency_id', $request->from_currency_id)
        ->where('to_currency_id', $request->to_currency_id)
        ->first();

    if ($existing) {
        return back()->with('error', 'This exchange rate already exists.');
    }

    // Create main rate
    $rate = ExchangeRate::create([
        'from_currency_id' => $request->from_currency_id,
        'to_currency_id'   => $request->to_currency_id,
        'rate'             => $request->rate,
    ]);

    // ============================
    //   AUTO-CREATE REVERSE RATE
    // ============================
    $reverseExists = ExchangeRate::where('from_currency_id', $request->to_currency_id)
        ->where('to_currency_id', $request->from_currency_id)
        ->first();

    if (!$reverseExists) {
        ExchangeRate::create([
            'from_currency_id' => $request->to_currency_id,
            'to_currency_id'   => $request->from_currency_id,
            'rate'             => 1 / $request->rate,
        ]);
    }

    return back()->with('success', 'Rate and auto-reverse rate added successfully.');
}

    public function update(Request $request, $id)
{
    $request->validate([
        'rate' => 'required|numeric|min:0.0000001',
    ]);

    $rate = ExchangeRate::findOrFail($id);

    // Update main rate
    $rate->update([
        'rate' => $request->rate,
    ]);

    // ==============================
    //  UPDATE REVERSE RATE ALSO
    // ==============================

    $reverse = ExchangeRate::where('from_currency_id', $rate->to_currency_id)
        ->where('to_currency_id', $rate->from_currency_id)
        ->first();

    if ($reverse) {
        $reverse->update([
            'rate' => 1 / $request->rate,
        ]);
    }

    return back()->with('success', 'Rate updated (reverse updated automatically).');
}


    public function destroy($id)
    {
        ExchangeRate::findOrFail($id)->delete();
        return back()->with('success', 'Rate deleted.');
    }
    public function storeCurrency(Request $request)
{
    $request->validate([
        'code'   => 'required|string|max:3|unique:currencies,code',
        'name'   => 'required|string|max:255',
        'symbol' => 'nullable|string|max:5',
    ]);

    Currency::create([
        'code'   => strtoupper($request->code),
        'name'   => $request->name,
        'symbol' => $request->symbol,
    ]);

    return back()->with('success', 'Currency added successfully.');
}

public function destroyCurrency($id)
{
    Currency::findOrFail($id)->delete();
    return back()->with('success', 'Currency deleted.');
}

}
