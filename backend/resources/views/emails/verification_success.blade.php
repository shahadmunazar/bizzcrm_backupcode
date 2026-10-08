@extends('emails.layouts.master')
@section('title', 'Email Verified Successfully')
@section('content')
    <h2 style="font-size: 22px; color: #333; margin-bottom: 20px;">Welcome to the Family, {{ $name }}! 🚀</h2>
    <p style="color: #555; font-size: 16px; line-height: 1.6;">
        We're thrilled to have you on board! Your email address has been successfully verified, and your account is now fully active.
    </p>
    <p style="color: #555; font-size: 16px; line-height: 1.6;">
        {{ $appName ?? config('app.name') }} is designed to help you streamline your workflow and boost productivity. To get you off to a flying start, here are a few things you can do right now:
    </p>

    <div style="background-color: #f9f9f9; border-radius: 8px; padding: 20px; margin: 25px 0;">
        <h3 style="font-size: 18px; color: #333; margin-top: 0;">Get Started in 3 Easy Steps:</h3>
        <ul style="padding-left: 20px; color: #555; font-size: 15px; line-height: 1.8;">
            <li><strong>Complete Your Profile:</strong> Tell us a bit more about yourself to personalize your experience.</li>
            <li><strong>Set Up Your Workspace:</strong> Configure your preferences and tools to match your unique workflow.</li>
            <li><strong>Invite Your Team:</strong> Collaboration is key! Bring your teammates on board and start working together.</li>
        </ul>
    </div>

    <table cellpadding="0" cellspacing="0" width="100%" style="margin: 30px 0;">
        <tr>
            <td align="center">
                <a href="{{ url('/login') }}"
                   style="display: inline-block; padding: 14px 30px; background-color: {{ env('APP_THEME_COLOR', '#E41F07') }}; color: #fff; text-decoration: none; font-weight: bold; border-radius: 6px; font-size: 16px; transition: background-color 0.3s ease;">
                    Go to My Dashboard
                </a>
            </td>
        </tr>
    </table>
    <p style="color: #555; font-size: 15px; line-height: 1.6;">
        Need help getting started? Our support team is always here for you. Just reply to this email or visit our help center.
    </p>
    <p style="color: #333; font-size: 15px; margin-top: 30px; font-weight: bold;">
        Best Regards,<br>
        The {{ $appName ?? config('app.name') }} Team
    </p>
@endsection
