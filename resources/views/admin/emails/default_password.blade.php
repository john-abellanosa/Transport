<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title></title>
    <meta name="x-apple-disable-message-reformatting">
    <meta name="color-scheme" content="light only">
    <meta name="supported-color-schemes" content="light only">
    <style> 
        body, table, td, a { font-family: Arial, Helvetica, sans-serif; text-size-adjust: 100%; }
        img { border: 0; outline: none; text-decoration: none; }
        table { border-collapse: collapse !important; }
        body { margin: 0 !important; padding: 0 !important; width: 100% !important; background-color: #f5f7fb; color: #333333; }
 
        .container { width: 100%; max-width: 600px; background-color: #ffffff; border-radius: 10px; box-shadow: 0 2px 14px rgba(18, 60, 105, 0.06); overflow: hidden; }
        .header { padding: 22px 28px; background: #ffffff; border-bottom: 1px solid #eef2f7; }
        .brand { font-size: 16px; font-weight: 700; color: #1772BA; letter-spacing: 0.2px; }
        .content { padding: 28px; }
        h2 { margin: 0 0 12px; font-size: 20px; color: #1772BA; }
        p { margin: 0 0 12px; line-height: 1.55; }
        .label { color: #6b7785; font-size: 13px; }
        .value { color: #1f2d3d; font-weight: 600; }

        .password {
            display: inline-block; background-color: #f1f6fe; color: #0f2f56;
            padding: 10px 14px; font-size: 18px; font-weight: 700; border-radius: 6px; letter-spacing: 1px;
        }

        .cta {
            margin-top: 18px;
        }
        .btn {
            display: inline-block; background-color: #1772BA; color: #ffffff !important; text-decoration: none;
            padding: 12px 18px; border-radius: 8px; font-weight: 600; font-size: 15px;
        }
        .btn:hover { opacity: 0.92; }

        .note {
            margin-top: 18px; font-size: 13px; color: #57606a;
        }
        .footer {
            padding: 18px 28px; font-size: 12px; color: #97a3b3; text-align: center; border-top: 1px solid #eef2f7;
        }
 
        @media screen and (max-width: 600px) {
            .content { padding: 22px; }
            .header { padding: 18px 22px; }
        }
 
        .preheader { display: none !important; visibility: hidden; opacity: 0; color: transparent; height: 0; width: 0; overflow: hidden; mso-hide: all; }
    </style>
</head>
<body> 

    <div class="preheader">Your account was created. Here is your default password and sign‑in link.</div>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" align="center" style="background-color:#f5f7fb; padding: 24px 12px;">
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
                            <h2>Hello {{ $name }},</h2>

                            <p>Your account has been successfully created.</p>

                            <p class="label">Email address</p>
                            <p class="value">{{ $email }}</p>

                            <p class="label" style="margin-top: 10px;">Default password</p>
                            <div class="password">{{ $password }}</div>

                            <p style="margin-top: 14px;">Please sign in and change your password immediately to keep your account secure.</p>

                            <div class="cta">
                                @php $loginUrl = $loginUrl ?? url('/company/login'); @endphp
                                <a href="{{ $loginUrl }}" class="btn" target="_blank" rel="noopener">Sign in</a>
                            </div>

                            <p class="note">
                                If the button doesn't work, copy and paste this link into your browser:<br>
                                <a href="{{ $loginUrl }}" style="color:#1772BA; text-decoration: underline;" target="_blank" rel="noopener">{{ $loginUrl }}</a>
                            </p>
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