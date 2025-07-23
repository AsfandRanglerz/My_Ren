<!DOCTYPE html>
<html>

<head>
    <title>Your OTP Code</title>
</head>

<body>
    <h2>OTP Verification</h2>
    <p>Dear {{ $name ?? 'User' }},</p>

    <p>Please use the following One-Time Password (OTP) to verify your account:</p>

    <h3>{{ $otp }}</h3>

    <p>Do not share this OTP with anyone.</p>

    <p>Regards,<br>{{ config('app.name') }}</p>
</body>

</html>
