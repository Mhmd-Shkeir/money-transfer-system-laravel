<?php

namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use App\Models\Currency;
use App\Services\ExchangeRateService;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'first_name','last_name','email','password_hash','phone','country',
        'address','date_of_birth','national_id','role','status',
        'is_email_verified','is_phone_verified','social_provider',
        'wallet_balance','total_deposits','total_withdrawals', 
        'currency_id', 
        'last_login','verification_token','token_expires_at' 
    ];

    protected $hidden = ['password_hash'];

    protected $casts = [
        'is_email_verified' => 'boolean',
        'token_expires_at' => 'datetime',
        'last_login' => 'datetime',  
        'date_of_birth' => 'date',
        'wallet_balance' => 'decimal:2', 
        'total_deposits' => 'decimal:2',
        'total_withdrawals' => 'decimal:2'
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }
    
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
    
    public function getPreferredCurrency()
    {
        return $this->currency ?: Currency::where('code', 'USD')->first();
    }
    
    public function getBalanceInPreferredCurrency(): float
    {
        $baseCurrency = Currency::where('code', 'USD')->first();
        $preferredCurrency = $this->getPreferredCurrency();
        
        if (!$baseCurrency) {
            return (float) $this->wallet_balance;
        }
        
        if ($preferredCurrency && $baseCurrency->id === $preferredCurrency->id) {
            return (float) $this->wallet_balance;
        }
        
        if ($preferredCurrency) {
            $exchangeService = new ExchangeRateService();
            $rate = $exchangeService->getRateByIds($baseCurrency->id, $preferredCurrency->id);
            
            if ($rate) {
                return (float) $this->wallet_balance * $rate;
            }
        }
        
        return (float) $this->wallet_balance;
    }
    
    public function getBalanceInCurrency($currencyCode): float
    {
        $baseCurrency = Currency::where('code', 'USD')->first();
        $targetCurrency = Currency::where('code', $currencyCode)->first();
        
        if (!$baseCurrency || !$targetCurrency) {
            return (float) $this->wallet_balance;
        }
        
        if ($baseCurrency->id === $targetCurrency->id) {
            return (float) $this->wallet_balance;
        }
        
        $exchangeService = new ExchangeRateService();
        $rate = $exchangeService->getRateByIds($baseCurrency->id, $targetCurrency->id);
        
        if ($rate) {
            return (float) $this->wallet_balance * $rate;
        }
        
        return (float) $this->wallet_balance;
    }

    public function getWalletBalance(): float
    {
        return (float) $this->wallet_balance;
    }

    public function hasSufficientBalance(float $amount): bool
    {
        return $this->getWalletBalance() >= $amount;
    }

    public function deposit(float $amount): bool
    {
        $old = $this->wallet_balance;
        $this->wallet_balance += $amount;
        $this->total_deposits += $amount;
        $saved = $this->save();
        try {
            \Illuminate\Support\Facades\Log::info('User deposit', [
                'user_id' => $this->id,
                'amount' => $amount,
                'old_balance' => $old,
                'new_balance' => $this->wallet_balance,
            ]);
        } catch (\Throwable $e) {
        }
        return $saved;
    }

    public function withdraw(float $amount): bool
    {
        if (!$this->hasSufficientBalance($amount)) {
            return false;
        }
        $old = $this->wallet_balance;
        $this->wallet_balance -= $amount;
        $this->total_withdrawals += $amount;
        $saved = $this->save();
        try {
            \Illuminate\Support\Facades\Log::info('User withdraw', [
                'user_id' => $this->id,
                'amount' => $amount,
                'old_balance' => $old,
                'new_balance' => $this->wallet_balance,
            ]);
        } catch (\Throwable $e) {
        }
        return $saved;
    }

    public function transfer(float $amount, User $recipient): bool
    {
        if (!$this->hasSufficientBalance($amount)) {
            return false;
        }

        return \DB::transaction(function () use ($amount, $recipient) {
            $this->wallet_balance -= $amount;
            $this->total_withdrawals += $amount;
            
            $recipient->wallet_balance += $amount;
            $recipient->total_deposits += $amount;
            
            return $this->save() && $recipient->save();
        });
    }
    
    public function agentProfile() {
        return $this->hasOne(AgentProfile::class);
    }

    public function paymentMethods() {
        return $this->hasMany(PaymentMethod::class);
    }

    public function beneficiaries() {
        return $this->hasMany(Beneficiary::class);
    }

    public function transactions() {
        return $this->hasMany(Transaction::class, 'sender_id');
    }

    public function reviews() {
        return $this->hasMany(Review::class);
    }

    public function notifications() {
        return $this->hasMany(Notification::class);
    }

    public function disputes() {
        return $this->hasMany(Dispute::class);
    }
}
