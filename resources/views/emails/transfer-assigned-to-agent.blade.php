@component('mail::message')
# Transfer Request Assigned

Hello {{ $agent->business_name ?? $agent->user->first_name }},

A new transfer request has been assigned to you and is ready for payout.

@component('mail::panel')
**Transfer Details:**
- Reference Code: {{ $transaction->reference_code }}
- Sender: {{ $transaction->sender->first_name ?? 'N/A' }} {{ $transaction->sender->last_name ?? '' }}
- Beneficiary: {{ $transaction->beneficiary->name ?? 'N/A' }}
- Amount: {{ number_format($transaction->amount_sent, 2) }}
- Status: Processing
@endcomponent

Please log in to your agent dashboard to process and complete this payout.

@component('mail::button', ['url' => url('/agent/requests')])
View Pending Requests
@endcomponent

Thanks,<br>
{{ config('app.name') }} Team
@endcomponent
