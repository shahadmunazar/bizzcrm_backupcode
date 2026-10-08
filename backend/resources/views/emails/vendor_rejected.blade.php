<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Vendor Application Update</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; font-size: 16px; line-height: 1.5; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; background-color: #f3f4f6; color: #374151; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f3f4f6; padding-bottom: 60px; padding-top: 40px; }
        .main { background: #ffffff; margin: 0 auto; width: 100%; max-width: 600px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); overflow: hidden; }
        .header { background: #1f2937; padding: 30px 40px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; letter-spacing: 0.5px; }
        .content { padding: 40px; }
        h2 { color: #111827; font-size: 20px; font-weight: 700; margin-top: 0; margin-bottom: 16px; }
        p { margin-top: 0; margin-bottom: 24px; font-size: 16px; color: #4b5563; }
        .info-box { background-color: #fef2f2; border-left: 4px solid #ef4444; padding: 16px 20px; margin-bottom: 30px; border-radius: 0 8px 8px 0; }
        .info-box p { margin: 0; color: #991b1b; font-size: 15px; }
        .btn-container { text-align: center; margin-bottom: 30px; }
        .btn { display: inline-block; background-color: #4b5563; color: #ffffff; text-decoration: none; font-weight: 600; padding: 14px 28px; border-radius: 8px; text-align: center; font-size: 16px; transition: background-color 0.2s; }
        .footer { padding: 30px 40px; background-color: #f9fafb; text-align: center; border-top: 1px solid #e5e7eb; }
        .footer p { margin: 0; font-size: 14px; color: #6b7280; }
        @media only screen and (max-width: 620px) {
            .main { width: 100% !important; border-radius: 0 !important; }
            .content, .header, .footer { padding-left: 20px !important; padding-right: 20px !important; }
        }
    </style>
</head>
<body>
    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table class="main" cellpadding="0" cellspacing="0" role="presentation">
                    <!-- Header -->
                    <tr>
                        <td class="header">
                            <h1>{{ config('app.name') }}</h1>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td class="content">
                            <h2>Hello {{ $vendorName }},</h2>
                            <p>Thank you for taking the time to apply as a vendor and for your interest in joining our platform.</p>
                            
                            <div class="info-box">
                                <p>After carefully reviewing your application and business details, we regret to inform you that we are unable to approve your vendor account at this time.</p>
                            </div>
                            
                            <p>We receive many applications and unfortunately, we cannot accept all of them. This decision is based on our current platform requirements and onboarding criteria.</p>
                            
                            @if(!empty($remarks))
                            <div style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 15px; margin-bottom: 24px;">
                                <p style="margin: 0; font-size: 14px; font-weight: bold; color: #4b5563; margin-bottom: 8px;">Additional Remarks from Reviewer:</p>
                                <p style="margin: 0; font-size: 14px; color: #111827; font-style: italic;">"{{ $remarks }}"</p>
                            </div>
                            @endif

                            <p>We appreciate your understanding and wish you the best of luck with your business endeavors.</p>
                            
                            <div class="btn-container">
                                <a href="{{ url('/contact-us') }}" class="btn">Contact Support</a>
                            </div>
                            
                            <p style="margin-bottom: 0; font-size: 14px;">If you believe this decision was made in error or if you have additional information to provide, please feel free to reach out to our support team.</p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td class="footer">
                            <p>Best regards,<br><strong>The {{ config('app.name') }} Team</strong></p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
