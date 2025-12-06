<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminCommission extends Model
{
    protected $fillable = [
        'admin_user_id', 'transaction_id', 'amount'
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
