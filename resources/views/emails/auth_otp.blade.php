<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your One-Time Password</title>
    <style>
        /* Basic Reset */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        table { border-collapse: collapse !important; }
        body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; }

        /* Main Styles */
        body {
            background-color: #0f172a;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #0f172a;
            padding: 20px;
            text-align: center;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
        }
        .content {
            background-color: #0f172a;
            padding: 30px 20px;
            text-align: center;
        }
        .otp-code {
            font-size: 36px;
            font-weight: bold;
            color: #313131;
            margin: 30px 0;
            letter-spacing: 5px;
            padding: 15px;
            background-color: #f0f0f0;
            border-radius: 5px;
            display: inline-block;
        }
        .footer {
            background-color: #0f172a;
            padding: 20px;
            text-align: center;
            border-bottom-left-radius: 5px;
            border-bottom-right-radius: 5px;
        }
        .footer p {
            font-size: 12px;
            color: #999999;
        }
    </style>
</head>
<body style="background-color: #fff; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="background-color: #fff;">
                <div class="container" style="max-width: 600px; margin: 0 auto; padding: 20px;">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td class="header" style="background-color: #0f172a; padding: 20px; text-align: center; border-top-left-radius: 5px; border-top-right-radius: 5px;">
                                <h1 style="font-size: 28px; color: #fff; margin: 0;">DailyAnswer</h1>
                            </td>
                        </tr>
                        <tr>
                            <td class="content" style="background-color: #0f172a; padding: 30px 20px; text-align: center;">
                                <h1 style="font-size: 24px; color: #fff; margin-bottom: 20px;">Your One-Time Password</h1>
                                <p style="font-size: 16px; color: #fff; line-height: 1.5;">{{ $message_text }}</p>
                                <div class="otp-code" style="font-size: 36px; font-weight: bold; color: #313131; margin: 30px 0; letter-spacing: 5px; padding: 15px; background-color: #ffffff; border-radius: 5px; display: inline-block;">
                                    {{ $otp }}
                                </div>
                                <p style="font-size: 16px; color: #fff; line-height: 1.5;">This code will expire in 10 minutes.</p>
                                <p style="font-size: 14px; color: #999999; line-height: 1.5;">If you did not request this, please ignore this email.</p>
                            </td>
                        </tr>
                        <tr>
                            <td class="footer" style="background-color: #0f172a; padding: 20px; text-align: center; border-bottom-left-radius: 5px; border-bottom-right-radius: 5px;">
                                <p style="font-size: 12px; color: #999999;">&copy; {{ date('Y') }} DailyAnswer. All rights reserved.</p>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
