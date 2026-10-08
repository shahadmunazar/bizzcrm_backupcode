<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Vendor Account Approved</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; font-size: 16px; line-height: 1.5; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; background-color: #f3f4f6; color: #374151; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f3f4f6; padding-bottom: 60px; padding-top: 40px; }
        .main { background: #ffffff; margin: 0 auto; width: 100%; max-width: 600px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); overflow: hidden; }
        .header { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); padding: 30px 40px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; letter-spacing: 0.5px; }
        .content { padding: 40px; }
        h2 { color: #111827; font-size: 20px; font-weight: 700; margin-top: 0; margin-bottom: 16px; }
        p { margin-top: 0; margin-bottom: 24px; font-size: 16px; color: #4b5563; }
        .referral-box { background-color: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 10px; padding: 24px; text-align: center; margin-bottom: 30px; }
        .referral-label { display: block; font-size: 12px; text-transform: uppercase; font-weight: 700; color: #64748b; letter-spacing: 1px; margin-bottom: 12px; }
        .referral-code { font-family: 'Monaco', 'Consolas', monospace; font-size: 28px; font-weight: 800; color: #4f46e5; letter-spacing: 2px; margin: 0; }
        .btn-container { text-align: center; margin-bottom: 30px; }
        .btn { display: inline-block; background-color: #4f46e5; color: #ffffff; text-decoration: none; font-weight: 600; padding: 14px 28px; border-radius: 8px; text-align: center; font-size: 16px; transition: background-color 0.2s; }
        .footer { padding: 30px 40px; background-color: #f9fafb; text-align: center; border-top: 1px solid #e5e7eb; }
        .footer p { margin: 0; font-size: 14px; color: #6b7280; }
        @media only screen and (max-width: 620px) {
            .main { width: 100% !important; border-radius: 0 !important; }
            .content, .header, .footer { padding-left: 20px !important; padding-right: 20px !important; }
            .referral-code { font-size: 24px; }
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
                            <h2>Welcome aboard, {{ $vendorName }}! 🎉</h2>
                            <p>Great news! Your vendor application has been carefully reviewed and <strong>officially approved</strong> by our team.</p>
                            <p>You now have full access to the platform. You can log in to your dashboard to manage your business, view analytics, and start thriving in our network.</p>
                            
                            <!-- Referral Box -->
                            <div class="referral-box">
                                <span class="referral-label">Your Unique Referral Code</span>
                                <h3 class="referral-code">{{ $referralCode }}</h3>
                            </div>
                            
                            <p>Share this code with other businesses. When they join our platform using your code, you'll earn rewards and commissions on their sales!</p>
                            
                            <!-- Call to Action -->
                            <div class="btn-container">
                                <!-- Adjust URL if you have a specific vendor login route via env or config, or hardcode absolute URL for emails -->
                                <a href="{{ url('/vendor-login') }}" class="btn">Log in to your Dashboard</a>
                            </div>
                            
                            <p style="margin-bottom: 0;">If you have any questions or need help getting started, our support team is always here for you.</p>
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
