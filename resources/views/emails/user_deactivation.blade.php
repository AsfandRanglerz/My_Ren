<!DOCTYPE html>
<html>

<head>
    <title>Account Deactivation Notification</title>
</head>

<body style="font-family: Arial, sans-serif; background: #f9f9f9; padding: 20px;">
    <div
        style="max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">

        <div style="text-align:center;">
            <img src="{{ asset('public/admin/assets/img/logo.png') }}" alt="{{ config('app.name') }} Logo"
                style="margin-bottom: 25px; height: 100px;">
            <h2>Account Deactivation</h2>
        </div>

        <p>Dear Dear {{ $user->name ?? 'User' }},</p>
        <p>Your account has been <strong>deactivated</strong> by the administrator.</p>

        @if (!empty($reason))
        <p><strong>Reason:</strong> {{ $reason }}</p>
        @endif

        <p>If you believe this is a mistake or have any questions, please contact our support team.</p>
        <p style="font-size: 14px; color: #888;">
            Thanks,<br>
            <strong>{{ config('app.name') }}</strong>
        </p>
    </div>
</body>

</html>
