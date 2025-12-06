<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'sender_id', 'beneficiary_id', 'agent_id', 'payment_method_id',
        'from_currency_id', 'to_currency_id', 'amount_sent', 'exchange_rate',
        'fee', 'total_paid', 'amount_received', 'payout_method', 'status',
        'reference_code', 'fake_payment_id', 'created_at', 'completed_at',
        'transaction_type', 'cash_reference', 'notes', 
        'recipient_name', 'customer_name', 'offer_id', 'discount_amount',
    ];


    public function sender() {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function beneficiary() {
        return $this->belongsTo(Beneficiary::class);
    }

    public function fromCurrency() {
        return $this->belongsTo(\App\Models\Currency::class, 'from_currency_id');
    }

    public function toCurrency() {
        return $this->belongsTo(\App\Models\Currency::class, 'to_currency_id');
    }

    public function agent() {
        return $this->belongsTo(AgentProfile::class, 'agent_id');
    }

    public function paymentMethod() {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function offer() {
        return $this->belongsTo(Offer::class);
    }

    public function commissions() {
        return $this->hasMany(Commission::class);
    }

    public function disputes() {
        return $this->hasMany(Dispute::class);
    }

    public function refundRequests() {
        return $this->hasMany(RefundRequest::class);
    }

    public function notifications() {
        return $this->hasMany(Notification::class, 'related_transaction_id');
    }
}
