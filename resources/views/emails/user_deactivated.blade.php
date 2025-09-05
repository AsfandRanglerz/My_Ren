<!-- <!DOCTYPE html>
<html>

<head>
    <title>Account Deactivation Notification</title>
</head>

<body>
    <h2>Account Deactivation Notice</h2>
    <p>Dear {{ $name }},</p>

    <p>Your account has been deactivated by the administrator.</p>

    @if (!empty($reason))
        <p><strong>Reason:</strong> {{ $reason }}</p>
    @endif

    <p>If you believe this is a mistake or have any questions, please contact our support team.</p>

    <p>Best regards,<br>
        {{ config('app.name') }}</p>
</body>

</html> -->
<!-- @component('mail::message')
    We have received reset password request, please click below button to reset password.
@component('mail::button', ['url' => $detail['url']])
Reset Password
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent -->
<!DOCTYPE html>
<html>

<head>
    <title>Account Deactivation Notification</title>
</head>

<body style="font-family: Arial, sans-serif; background: #f9f9f9; padding: 20px;">
    <div
        style="max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">

        <div style="text-align:center;">
            <img src="{{ $data['logo'] ?? asset('public/admin/assets/img/logo.png') }}" alt="My Ren Logo"
                style="margin-bottom: 25px; height: 100px;">
            <h2>Account Deactivation Notice</h2>
        </div>

        <p>Your account has been deactivated by the administrator.</p>

        @if (!empty($reason))
        <p><strong>Reason:</strong> {{ $reason }}</p>
        @endif

        <p>If you believe this is a mistake or have any questions, please contact our support team.</p>


        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $detail['url'] }}"
                style="background-color: #d881fb; 
          color: white; 
          padding: 12px 24px; 
          border-radius: 6px; 
          text-decoration: none; 
          font-size: 16px; 
          display: inline-block;">
                Reset Password
            </a>

        </div>
        <hr style="margin: 30px 0;">

        <p style="font-size: 14px; color: #888;">
            Thanks,<br>
            <strong>{{ config('app.name') }}</strong>
        </p>
    </div>
</body>

</html>