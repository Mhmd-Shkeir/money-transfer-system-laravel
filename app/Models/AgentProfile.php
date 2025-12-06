<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentProfile extends Model
{
    protected $fillable = [
        'user_id',
        'store_name',
        'address',
        'latitude',
        'longitude',
        'working_hours',
        'documents',
        'is_approved',
        'commission_rate',
        'cash_balance',
        'total_cash_in',
        'total_cash_out',
        'total_transactions',
        'business_name',
        'business_registration_number',
        'tax_id',
        'business_description',
        'office_address',
        'country_id',
        'currency_id'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public static function boot()
    {
        parent::boot();
        if (class_exists('\App\Observers\AgentProfileObserver')) {
            self::observe(\App\Observers\AgentProfileObserver::class);
        }
    }

    public function country() {
        return $this->belongsTo(Country::class);
    }

    public function currency() {
        return $this->belongsTo(Currency::class);
    }

    public function transactions() {
        return $this->hasMany(Transaction::class);
    }

    public function commissions() {
        return $this->hasMany(Commission::class);
    }

    public function businessHours() {
        return $this->hasMany(BusinessHours::class);
    }

  
    public function creditCash(float $amount, ?int $transactionId = null, ?string $reference = null): bool
    {
        if ($amount <= 0) {
            return false;
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($amount) {
            $this->cash_balance = ($this->cash_balance ?? 0) + $amount;
            $this->total_cash_in = ($this->total_cash_in ?? 0) + $amount;
            $this->total_transactions = ($this->total_transactions ?? 0) + 1;
            $saved = $this->save();

            try {
                if ($this->user) {
                    $this->user->deposit($amount);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('AgentProfile::creditCash - failed to deposit to user wallet', ['agent_profile_id' => $this->id, 'user_id' => $this->user->id ?? null, 'error' => $e->getMessage()]);
            }

            try {
                \App\Models\AgentLedger::create([
                    'agent_profile_id' => $this->id,
                    'user_id' => $this->user->id ?? null,
                    'transaction_id' => $transactionId,
                    'amount' => $amount,
                    'type' => 'credit',
                    'balance_after' => $this->cash_balance,
                    'reference' => $reference,
                    'notes' => 'creditCash',
                ]);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('AgentProfile::creditCash - failed to write ledger', ['agent_profile_id' => $this->id, 'error' => $e->getMessage()]);
            }

            return $saved;
        });
    }

   
    public function debitCash(float $amount, ?int $transactionId = null, ?string $reference = null): bool
    {
        if ($amount <= 0) {
            return false;
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($amount) {
            $this->cash_balance = max(0, ($this->cash_balance ?? 0) - $amount);
            $this->total_cash_out = ($this->total_cash_out ?? 0) + $amount;
            $this->total_transactions = ($this->total_transactions ?? 0) + 1;
            $saved = $this->save();

            $userDeducted = 0;

            try {
                if ($this->user) {
                    if ($this->user->withdraw($amount)) {
                        $userDeducted = $amount;
                    } else {
                        $old = $this->user->wallet_balance;
                        $userDeducted = min($old, $amount);
                        $this->user->wallet_balance = $old - $userDeducted;
                        $this->user->total_withdrawals = ($this->user->total_withdrawals ?? 0) + $userDeducted;
                        $this->user->save();
                        \Illuminate\Support\Facades\Log::warning('AgentProfile::debitCash - user wallet insufficient; partial sync applied', ['agent_profile_id' => $this->id, 'user_id' => $this->user->id ?? null, 'requested' => $amount, 'deducted' => $userDeducted]);
                    }
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('AgentProfile::debitCash - failed to withdraw from user wallet', ['agent_profile_id' => $this->id, 'user_id' => $this->user->id ?? null, 'error' => $e->getMessage()]);
            }

            try {
                \App\Models\AgentLedger::create([
                    'agent_profile_id' => $this->id,
                    'user_id' => $this->user->id ?? null,
                    'transaction_id' => $transactionId,
                    'amount' => $amount,
                    'type' => 'debit',
                    'balance_after' => $this->cash_balance,
                    'reference' => $reference,
                    'notes' => 'debitCash; user_deducted=' . ($userDeducted ?? 0),
                ]);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('AgentProfile::debitCash - failed to write ledger', ['agent_profile_id' => $this->id, 'error' => $e->getMessage()]);
            }

            return $saved;
        });
    }

    
    public function isOpen()
    {
        return BusinessHours::isOpenNow($this->id);
    }

    
    public function getFormattedBusinessHours()
    {
        return $this->businessHours()
            ->orderBy('day_of_week')
            ->get()
            ->map(function ($hours) {
                if ($hours->is_closed) {
                    return $hours->getDayName() . ': Closed';
                }
                return $hours->getDayName() . ': ' . $hours->opening_time . ' - ' . $hours->closing_time;
            })
            ->toArray();
    }
}
