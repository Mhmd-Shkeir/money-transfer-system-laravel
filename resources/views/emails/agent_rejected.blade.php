<div style="font-family: Arial, Helvetica, sans-serif; color: #222;">
    <h2>Hello {{ $user->first_name ?? $user->name ?? 'User' }},</h2>
    <p>We reviewed your agent application and, unfortunately, it was not approved at this time.</p>

    @if($profile)
        <p>Store: <strong>{{ $profile->store_name }}</strong></p>
    @endif

    <p>If you believe this is a mistake or would like feedback, please contact support.</p>

    <p>Best regards,<br/>The Team</p>
</div>
