<!DOCTYPE html>
<html>
<head>
    <title>Verify Your Email</title>
</head>
<body>
    <h2>Hello {{ $user->first_name }},</h2>
    <p>Please verify your email address by clicking the button below:</p>
    
    <a href="{{ url('/verify-email/' . $user->verification_token) }}" 
       style="display: inline-block; padding: 10px 20px; background-color: #007bff; 
              color: white; text-decoration: none; border-radius: 5px;">
        Verify Email
    </a>

    <p>This verification link will expire in 2 hours.</p>
    
    <p>If you didn't create an account, no further action is required.</p>
</body>
</html>