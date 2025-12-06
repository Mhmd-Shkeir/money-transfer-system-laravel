<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Offer;

class OfferController extends Controller
{
    /**
     * Return the best applicable offer for a given amount.
     * Chooses the offer with the highest min_amount <= amount.
     */
    public function forAmount(Request $request)
    {
        $amount = floatval($request->query('amount', 0));

        if ($amount <= 0) {
            return response()->json(['success' => true, 'offer' => null]);
        }

        $offer = Offer::where('is_active', 1)
            ->where(function ($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->where('min_amount', '<=', $amount)
            ->orderBy('min_amount', 'desc')
            ->first();

        if (! $offer) {
            return response()->json(['success' => true, 'offer' => null]);
        }

        return response()->json([
            'success' => true,
            'offer' => [
                'id' => $offer->id,
                'name' => $offer->name,
                'description' => $offer->description,
                'min_amount' => (float) $offer->min_amount,
                'discount_percentage' => (float) $offer->discount_percentage,
            ],
        ]);
    }
}
