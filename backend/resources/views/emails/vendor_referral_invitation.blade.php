@extends('emails.layouts.master')
@section('title', 'Invitation to Join as a Vendor Partner')
@section('content')
    <div style="text-align: center; margin-bottom: 25px;">
        <span style="background-color: #e0e7ff; color: #4338ca; padding: 5px 14px; border-radius: 20px; font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;">Vendor Partner Invitation</span>
    </div>

    <h2 style="font-size: 22px; color: #1f2937; margin-bottom: 15px; text-align: center;">
        You've Been Invited to Join {{ config('app.name', 'ZipZapGo') }}!
    </h2>

    <p style="color: #4b5563; font-size: 15px; line-height: 1.6; text-align: center;">
        <strong>{{ $inviterName }}</strong> from <strong>{{ $companyName }}</strong> has personally invited you to register as a Vendor Partner on {{ config('app.name') }}.
    </p>

    @if(!empty($personalMessage))
    <div style="background-color: #f8fafc; border-left: 4px solid #4f46e5; border-radius: 6px; padding: 16px; margin: 25px 0;">
        <p style="margin: 0; color: #374151; font-size: 14px; font-style: italic; white-space: pre-line;">"{{ $personalMessage }}"</p>
    </div>
    @endif

    <div style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin: 25px 0; text-align: center;">
        <p style="color: #6b7280; font-size: 12px; text-transform: uppercase; font-weight: bold; margin-bottom: 8px; letter-spacing: 0.5px;">Official Vendor Referral Code</p>
        <span style="display: inline-block; background-color: #eef2ff; color: #4338ca; border: 1px dashed #6366f1; padding: 8px 20px; border-radius: 6px; font-size: 20px; font-weight: bold; letter-spacing: 2px; font-family: monospace;">{{ $referralCode }}</span>
        <p style="color: #6b7280; font-size: 13px; margin-top: 10px; margin-bottom: 0;">Use this referral code during registration to join under {{ $companyName }}'s partner network.</p>
    </div>

    <table cellpadding="0" cellspacing="0" width="100%" style="margin: 30px 0;">
        <tr>
            <td align="center">
                <a href="{{ $referralUrl }}"
                   style="display: inline-block; padding: 14px 34px; background-color: {{ env('APP_THEME_COLOR', '#4f46e5') }}; color: #ffffff; text-decoration: none; font-weight: bold; border-radius: 6px; font-size: 16px; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);">
                    Register Here
                </a>
            </td>
        </tr>
    </table>

    <div style="background-color: #f3f4f6; border-radius: 6px; padding: 15px; margin: 20px 0; font-size: 13px; color: #6b7280; line-height: 1.5;">
        <strong style="color: #374151;">Why Join Us?</strong>
        <ul style="margin: 8px 0 0 20px; padding: 0;">
            <li>Expand your reach with our comprehensive digital sales and client network.</li>
            <li>Real-time transaction tracking, commission analytics, and collaborative supplier tools.</li>
            <li>Fast onboarding and transparent supplier partner lifecycle.</li>
        </ul>
    </div>

    <p style="color: #9ca3af; font-size: 12px; line-height: 1.5; text-align: center; margin-top: 25px;">
        If you have questions about this invitation, please contact {{ $inviterName }} directly or our support team.
    </p>
@endsection
