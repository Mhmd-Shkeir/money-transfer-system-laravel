<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{

    public $timestamps = false;

    protected $fillable = [
        'from_currency_id','to_currency_id','rate','updated_at'
    ];

    public function fromCurrency() {
        return $this->belongsTo(Currency::class, 'from_currency_id');
    }

    public function toCurrency() {
        return $this->belongsTo(Currency::class, 'to_currency_id');
    }
}
