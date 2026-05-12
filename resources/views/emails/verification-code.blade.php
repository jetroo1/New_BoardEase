<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your BoardEase Verification Code</title>
</head>
<body style="margin:0;padding:0;background:#edfaff;font-family:Arial,Helvetica,sans-serif;color:#102236;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#edfaff;padding:28px 14px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:520px;background:#ffffff;border:1px solid #bae6fd;border-radius:14px;overflow:hidden;box-shadow:0 18px 44px rgba(8,47,73,0.12);">
                    <tr>
                        <td style="padding:26px 28px;background:linear-gradient(135deg,#075985,#06b6d4);color:#ffffff;">
                            <div style="font-size:24px;font-weight:800;letter-spacing:0;">BoardEase</div>
                            <div style="margin-top:4px;font-size:11px;font-weight:700;letter-spacing:1.3px;text-transform:uppercase;color:#cffafe;">Boarding House Finder & Reservation</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px 28px;">
                            <h1 style="margin:0 0 10px;font-size:22px;line-height:1.25;color:#102236;">Verify your email</h1>
                            <p style="margin:0 0 22px;font-size:15px;line-height:1.6;color:#64748b;">
                                Use this code to {{ $purpose === 'register' ? 'finish creating your BoardEase account' : 'sign in to your BoardEase account' }}.
                            </p>

                            <div style="padding:18px;border:1px solid #bae6fd;border-radius:12px;background:#f0f9ff;text-align:center;">
                                <div style="font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#075985;">Your verification code</div>
                                <div style="margin-top:8px;font-size:36px;line-height:1;font-weight:800;letter-spacing:8px;color:#0f2741;">{{ $code }}</div>
                            </div>

                            <p style="margin:22px 0 0;font-size:14px;line-height:1.6;color:#64748b;">
                                This code expires in {{ $expiresIn }}. If you did not request this code, you can safely ignore this email.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:18px 28px;background:#f8fdff;border-top:1px solid #e0f2fe;color:#64748b;font-size:12px;line-height:1.5;">
                            © 2026 BoardEase. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
