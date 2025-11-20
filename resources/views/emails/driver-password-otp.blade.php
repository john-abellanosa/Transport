<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Password Reset OTP</title>
    <meta name="x-apple-disable-message-reformatting">
    <meta name="color-scheme" content="light only">
    <meta name="supported-color-schemes" content="light only">
    <style>
        body,
        table,
        td,
        a {
            font-family: Arial, Helvetica, sans-serif;
            text-size-adjust: 100%;
        }

        img {
            border: 0;
            outline: none;
            text-decoration: none;
        }

        table {
            border-collapse: collapse !important;
        }

        body {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            background-color: #f5f7fb;
            color: #333333;
        }

        .container {
            width: 100%;
            max-width: 600px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 14px rgba(18, 60, 105, 0.06);
            overflow: hidden;
        }

        .header {
            padding: 22px 28px;
            background: #ffffff;
            border-bottom: 1px solid #eef2f7;
        }

        .brand {
            font-size: 16px;
            font-weight: 700;
            color: #1772BA;
            letter-spacing: 0.2px;
        }

        .content {
            padding: 28px;
        }

        h2 {
            margin: 0 0 12px;
            font-size: 20px;
            color: #1772BA;
        }

        p {
            margin: 0 0 12px;
            line-height: 1.55;
        }

        .otp-box {
            display: inline-block;
            background-color: #f1f6fe;
            color: #0f2f56;
            padding: 14px 18px;
            font-size: 26px;
            font-weight: 700;
            border-radius: 8px;
            letter-spacing: 8px;
            text-align: center;
            border: 2px solid #1772BA;
            margin: 20px 0;
        }

        .expiry-note {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 5px;
            color: #856404;
            margin: 20px 0;
            font-size: 14px;
            line-height: 1.5;
        }

        .note {
            margin-top: 16px;
            font-size: 13px;
            color: #57606a;
        }

        .footer {
            padding: 18px 28px;
            font-size: 12px;
            color: #97a3b3;
            text-align: center;
            border-top: 1px solid #eef2f7;
        }

        @media screen and (max-width: 600px) {
            .content {
                padding: 22px;
            }

            .header {
                padding: 18px 22px;
            }
        }

        .preheader {
            display: none !important;
            visibility: hidden;
            opacity: 0;
            color: transparent;
            height: 0;
            width: 0;
            overflow: hidden;
            mso-hide: all;
        }
    </style>
</head>

<body>

    <div class="preheader">Your OTP code for password reset.</div>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" align="center"
        style="background-color:#f5f7fb; padding: 24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" class="container" cellspacing="0" cellpadding="0" width="100%">
                    <tr>
                        <td class="header">
                            <div class="brand">Express Logistics</div>
                        </td>
                    </tr>
                    <tr>
                        <td class="content">
                            <h2>Password Reset Request</h2>
                            <p>Hello,</p>
                            <p>You requested to reset your password for your driver account. Use the following OTP to
                                verify your identity:</p>

                            <div class="otp-box">{{ $otp }}</div>

                            <div class="expiry-note">
                                <strong>Important:</strong> This OTP code will expire in
                                <strong>{{ $expires_in }}</strong>.
                                <br>If you didn’t request this password reset, you can safely ignore this message.
                            </div>

                            <p class="note">For security reasons, please do not share this code with anyone.</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="footer">
                            &copy; {{ date('Y') }} Express Logistics. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>

</html>
