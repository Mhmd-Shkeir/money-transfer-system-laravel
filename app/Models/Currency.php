<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    public $timestamps = false;
    protected $fillable = ['code','name','symbol'];

    public function exchangeRatesFrom() {
        return $this->hasMany(ExchangeRate::class, 'from_currency_id');
    }

    public function exchangeRatesTo() {
        return $this->hasMany(ExchangeRate::class, 'to_currency_id');
    }

    public function countries() {
        return $this->hasMany(Country::class);
    }

    
    public function getDisplay()
    {
        return "{$this->code} ({$this->symbol})";
    }

    
    public function formatAmount($amount)
    {
        return $this->symbol . number_format($amount, 2, '.', ',');
    }

  
    public function getCode()
    {
        return $this->code;
    }

   
    public function getSymbol()
    {
        return $this->symbol;
    }

    
    public function getFullName()
    {
        return "{$this->name} ({$this->code})";
    }

   
    public function isMajor()
    {
        $majorCurrencies = ['USD', 'EUR', 'GBP', 'JPY', 'CHF', 'CAD', 'AUD'];
        return in_array($this->code, $majorCurrencies);
    }
}
