<!DOCTYPE html>
<html>
<head>
    <title>Reset Your Password</title>
</head>
<body>
<h1>Hello,</h1>
<p>You are receiving this email because we received a password reset request for your account associated
    with {{ $email }}.</p>

<p>Click the button below to reset your password:</p>
<a href="{{ $url }}"
   style="display: inline-block; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none;">Reset
    Password</a>

<p>If you did not request a password reset, no further action is required.</p>

<p>Thanks, <br> {{ config('app.name') }}</p>
</body>
</html>
