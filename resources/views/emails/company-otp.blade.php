<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Password Reset OTP</title>
<style>
body { font-family: Arial, sans-serif; background-color: #f5f7fa; margin:0; padding:0; }
.container { background: #ffffff; max-width: 600px; margin: 40px auto; padding: 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
h2 { color: #2d3748; }
p { color: #4a5568; font-size: 16px; line-height: 1.5; }
.otp { font-size: 28px; font-weight: bold; color: #4a6cf7; margin: 20px 0; }
.footer { margin-top: 30px; font-size: 12px; color: #a0aec0; }
</style>
</head>
<body>
<div class="container">
    <h2>Hello {{ $companyName }},</h2>
    <p>You requested to reset your password. Use the following One-Time Password (OTP) to proceed:</p>
    <div class="otp">{{ $otp }}</div>
    <p>This OTP is valid for 5 minutes. Please do not share it with anyone.</p>
    <p class="footer">If you did not request this, please ignore this email.</p>
</div>
</body>
</html>
