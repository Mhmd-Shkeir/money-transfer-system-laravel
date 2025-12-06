<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferFee extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'from_currency_id','to_currency_id','percentage_fee','fixed_fee','effective_date'
    ];

    public function fromCurrency() {
        return $this->belongsTo(Currency::class, 'from_currency_id');
    }

    public function toCurrency() {
        return $this->belongsTo(Currency::class, 'to_currency_id');
    }
}
