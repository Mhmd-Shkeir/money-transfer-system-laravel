<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $fillable = [
        'agent_id','transaction_id','commission_amount','paid_out'
    ];

    public function agent() {
        return $this->belongsTo(AgentProfile::class);
    }

    public function transaction() {
        return $this->belongsTo(Transaction::class);
    }
}
