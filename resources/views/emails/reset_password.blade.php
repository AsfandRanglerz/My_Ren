<!DOCTYPE html>
<html>

<head>
    <title>Password Reset - MyRen</title>
</head>

<body style="font-family: Arial, sans-serif; background: #f9f9f9; padding: 20px;">
    <div
        style="max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">

        <div style="text-align:center;">
            <img src="{{ $data['logo'] ?? asset('public/admin/assets/img/logo.png') }}" alt="My Ren Logo Logo"
                style="margin-bottom: 25px; height: 100px;">
            <h2 style="color: #d881fb;">Reset Your Password</h2>
        </div>

        <p style="font-size: 16px; color: #333;">
            We have received a request to reset your password. Click the button below to proceed:
        </p>

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

        <p style="font-size: 14px; color: #555;">
            If you did not request this, please ignore this email
        </p>

        <hr style="margin: 30px 0;">

        <p style="font-size: 14px; color: #888;">
            Thanks,<br>
            <strong>{{ config('app.name') }}</strong>
        </p>
    </div>
</body>

</html>
