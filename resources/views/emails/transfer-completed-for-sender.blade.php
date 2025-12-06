@component('mail::message')
# Transfer Completed

Hello {{ $user->first_name }} {{ $user->last_name }},

Your transfer has been completed and the funds have been paid out to the beneficiary.

@component('mail::panel')
**Transfer Details:**
- Reference Code: {{ $transaction->reference_code }}
- Beneficiary: {{ $transaction->beneficiary->name ?? 'N/A' }}
- Amount Sent: {{ number_format($transaction->amount_sent, 2) }}
- Amount Received: {{ number_format($transaction->amount_received, 2) }}
- Payout Agent: {{ $agent->business_name ?? 'Local Agent' }}
- Completed At: {{ $transaction->completed_at ? $transaction->completed_at->format('M d, Y H:i') : 'Recently' }}
@endcomponent

Your transfer has been successfully processed. Thank you for using our service.

@component('mail::button', ['url' => url('/dashboard')])
View Dashboard
@endcomponent

Thanks,<br>
{{ config('app.name') }} Team
@endcomponent
