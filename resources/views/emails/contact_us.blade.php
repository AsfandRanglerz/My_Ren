<!DOCTYPE html>
<html>
<head>
    <title>New Contact Us Message - My Ren Solutions</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333;">
    <div style="text-align: center; margin-bottom: 30px;">
        <img src="{{ asset('public/admin/assets/img/logo.png') }}" 
     alt="{{ config('app.name') }} Logo" 
     style="height: 100px;">

    </div>

    <h2>New Contact Us Message</h2>

    <p>You have received a new message from your website's contact form.</p>

    <p><strong>Email:</strong> {{ $userEmail }}</p>

    <p><strong>Message:</strong><br>
    {{ $userMessage }}</p>

    <p style="margin: 30px 0; text-align: center;">
        <a href="mailto:{{ $userEmail }}" 
           style="display: inline-block; padding: 10px 20px; background-color: #00b7d2; color: #fff; text-decoration: none; border-radius: 5px;">
            Reply to {{ $userEmail }}
        </a>
    </p>

    <p>Thanks,<br>
    <strong>My Ren Solutins</strong></p>
</body>
</html>
