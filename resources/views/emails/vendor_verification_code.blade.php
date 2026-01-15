<!DOCTYPE html>
<html>
<head>
    <title>Vendor Login Verification Code</title>
</head>
<body>
    <h1>Vendor Login Verification</h1>
    <p>Dear {{ $vendor->vendor_name }},</p>
    <p>Your verification code for logging into your vendor dashboard is:</p>
    <h2>{{ $code }}</h2>
    <p>This code will expire in 10 minutes. Please use it to complete your login.</p>
    <p>If you did not request this code, please ignore this email.</p>
    <p>Best regards,<br>Kiln Enterprise</p>
</body>
</html>