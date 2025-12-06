<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    protected $fillable = [
        'user_id','name','phone','email','country_id','bank_name',
        'bank_account','swift_code','iban','wallet_provider','wallet_id'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function country() {
        return $this->belongsTo(Country::class);
    }

    public function transactions() {
        return $this->hasMany(Transaction::class);
    }
}
