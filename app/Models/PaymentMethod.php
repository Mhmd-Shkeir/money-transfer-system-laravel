<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'user_id','type','provider','account_holder','account_number',
        'iban','swift_code','currency','is_verified','nickname'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function transactions() {
        return $this->hasMany(Transaction::class);
    }
}
