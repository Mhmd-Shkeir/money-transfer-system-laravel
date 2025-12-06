<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $fillable = [
        'name',
        'description',
        'min_amount',
        'discount_percentage',
        'is_active',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'discount_percentage' => 'float',
        'min_amount' => 'float',
    ];

    
    public static function getActiveOffers()
    {
        return self::where('is_active', true)
            ->where(function($query) {
                $query->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->get();
    }

   
    public function qualifies($amount)
    {
        return $amount >= $this->min_amount;
    }

    public static function getBestOfferForAmount($amount)
    {
        return self::getActiveOffers()
            ->filter(function($offer) use ($amount) {
                return $offer->qualifies($amount);
            })
            ->sortByDesc('discount_percentage')
            ->first();
    }
}
