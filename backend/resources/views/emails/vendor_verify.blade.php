@extends('emails.layouts.master')
@section('title', 'Verify Your Vendor Account')
@section('content')
    <div style="text-align: center; margin-bottom: 25px;">
        <span style="background-color: #e0e7ff; color: #4338ca; padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;">Vendor Partner Program</span>
    </div>

    <h2 style="font-size: 22px; color: #1f2937; margin-bottom: 15px; text-align: center;">
        Welcome to {{ config('app.name') }} as a Vendor Partner, {{ $name }}!
    </h2>

    <p style="color: #4b5563; font-size: 15px; line-height: 1.6; text-align: center;">
        Your official <strong>Vendor Account</strong> has been created successfully. To complete your setup and access your Vendor Panel, please verify your email address below.
    </p>

    <div style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin: 25px 0; border-left: 4px solid {{ env('APP_THEME_COLOR', '#4f46e5') }};">
        <h4 style="margin-top: 0; margin-bottom: 12px; color: #111827; font-size: 16px;">Vendor Account Details</h4>
        <p style="color: #374151; margin: 6px 0; font-size: 14px;"><strong>Account Type:</strong> Vendor Partner</p>
        <p style="color: #374151; margin: 6px 0; font-size: 14px;"><strong>Contact Person:</strong> {{ $name }}</p>
        @if(!empty($companyName))
            <p style="color: #374151; margin: 6px 0; font-size: 14px;"><strong>Company:</strong> {{ $companyName }}</p>
        @endif
        <p style="color: #374151; margin: 6px 0; font-size: 14px;"><strong>Email:</strong> {{ $email }}</p>
        <p style="color: #374151; margin: 6px 0; font-size: 14px;"><strong>Temporary Password:</strong> <code style="background-color: #e5e7eb; padding: 2px 6px; border-radius: 4px; font-size: 14px;">{{ $password }}</code></p>
        @if(!empty($referralCode))
            <p style="color: #374151; margin: 6px 0; font-size: 14px;"><strong>Your Vendor Referral Code:</strong> <strong style="color: #4f46e5;">{{ $referralCode }}</strong></p>
        @else
            <p style="color: #6b7280; margin: 6px 0; font-size: 13px;"><em>Note: Your Vendor Referral Code will be generated after your account is approved by the Super Admin.</em></p>
        @endif
    </div>

    <table cellpadding="0" cellspacing="0" width="100%" style="margin: 30px 0;">
        <tr>
            <td align="center">
                <a href="{{ $verificationUrl }}"
                   style="display: inline-block; padding: 14px 32px; background-color: {{ env('APP_THEME_COLOR', '#4f46e5') }}; color: #ffffff; text-decoration: none; font-weight: bold; border-radius: 6px; font-size: 16px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    Verify Vendor Email
                </a>
            </td>
        </tr>
    </table>

    <div style="background-color: #f3f4f6; border-radius: 6px; padding: 15px; margin: 20px 0; font-size: 13px; color: #6b7280; line-height: 1.5;">
        <strong style="color: #374151;">Important Instructions:</strong>
        <ul style="margin: 8px 0 0 20px; padding: 0;">
            <li>Once you click <strong>Verify Vendor Email</strong>, you will be redirected to the <a href="{{ $vendorLoginUrl }}" style="color: #4f46e5; text-decoration: underline;">Vendor Login Portal</a>.</li>
            <li>Vendor accounts must always be accessed via the <strong>Vendor Login</strong> system, not the regular user login.</li>
            <li>For security reasons, please change your password after logging into your vendor dashboard.</li>
        </ul>
    </div>

    <p style="color: #9ca3af; font-size: 12px; line-height: 1.5; text-align: center; margin-top: 25px;">
        If you did not apply for a vendor account with {{ config('app.name') }}, please disregard this email.
    </p>
@endsection
