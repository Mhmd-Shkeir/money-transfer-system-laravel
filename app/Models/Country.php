<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = ['name','iso_code','currency_id','region'];

    public function currency() {
        return $this->belongsTo(Currency::class);
    }

    public function beneficiaries() {
        return $this->hasMany(Beneficiary::class);
    }
}
