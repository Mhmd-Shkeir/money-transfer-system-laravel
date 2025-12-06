<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentLedger extends Model
{
    protected $table = 'agent_ledgers';

    protected $fillable = [
        'agent_profile_id',
        'user_id',
        'transaction_id',
        'amount',
        'type',
        'balance_after',
        'reference',
        'notes',
    ];

    public function agentProfile()
    {
        return $this->belongsTo(AgentProfile::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
