<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    protected $fillable = [
        'transaction_id','user_id','reason','status','resolution_notes'
    ];

    public function transaction() {
        return $this->belongsTo(Transaction::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
