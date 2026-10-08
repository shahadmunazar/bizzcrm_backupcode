<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Account Deletion Verification Code</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8f9fa; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <h2 style="color: #333333; text-align: center; margin-bottom: 20px;">Verify Your Email</h2>
        
        <p style="color: #555555; line-height: 1.6;">
            We received a request to delete your account. To verify that you made this request, please use the following 6-digit verification code:
        </p>

        <div style="background-color: #f4f6f9; padding: 15px; text-align: center; border-radius: 6px; margin: 30px 0;">
            <span style="font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #0d6efd;">
                {{ $otp }}
            </span>
        </div>

        <p style="color: #555555; line-height: 1.6;">
            This code will expire in 15 minutes.
        </p>

        <p style="color: #555555; line-height: 1.6; margin-top: 30px; font-size: 14px;">
            If you did not request this, please ignore this email or contact support immediately as your account may be compromised.
        </p>

        <hr style="border: none; border-top: 1px solid #eeeeee; margin: 30px 0;">

        <p style="color: #999999; font-size: 12px; text-align: center;">
            &copy; {{ date('Y') }} BizzCRM. All rights reserved.
        </p>
    </div>
</body>
</html>
