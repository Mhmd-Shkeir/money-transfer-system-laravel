<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Offer;

class AdminOfferController extends Controller
{
    public function index()
    {
        $offers = Offer::orderBy('created_at', 'desc')->get();
        return view('admin.offers.index', compact('offers'));
    }

    public function create()
    {
        return view('admin.offers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'min_amount' => 'required|numeric|min:0',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'is_active' => 'sometimes',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        Offer::create($data);

        return redirect()->route('admin.offers.index')->with('success', 'Offer created successfully.');
    }
}
