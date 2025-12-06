<div style="font-family: Arial, Helvetica, sans-serif; color: #222;">
    <h2>Hello {{ $user->first_name ?? $user->name ?? 'User' }},</h2>
    <p>Good news — your agent application has been approved by our team.</p>

    @if($profile)
        <p>Store: <strong>{{ $profile->store_name }}</strong></p>
    @endif

    <p>You can now log in and access your agent dashboard.</p>

    <p>Best regards,<br/>The Team</p>
</div>
