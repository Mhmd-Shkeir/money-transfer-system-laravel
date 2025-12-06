<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AgentPayoutRequest extends Model
{
    use HasFactory;

    protected $table = 'agent_payout_requests';

    protected $fillable = [
        'agent_profile_id',
        'user_id',
        'amount',
        'currency_code',
        'status',
        'approved_by',
        'approved_at',
        'agent_note',
        'admin_note',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    
    public function agentUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function agentProfile()
    {
        return $this->belongsTo(AgentProfile::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
