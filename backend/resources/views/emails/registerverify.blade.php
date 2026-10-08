@extends('emails.layouts.master')
@section('title', 'Email Verification - ' . config('app.name'))
@section('content')
    <h2 style="margin-top:0; font-size:22px; color:#333;">👋 Welcome to {{ config('app.name') }}, {{ $name }}!</h2>
    <p style="font-size:15px; line-height:1.6; color: #555;">
        Your CRM account has been successfully created. Please review your login details and verify your email address to activate your account.
    </p>
    <div style="margin-top:20px; padding:15px 20px; background:#f9f9f9; border-left:4px solid {{ env('APP_THEME_COLOR', '#E41F07') }}; border-radius:6px; color:#333;">
        <p><strong>Name:</strong> {{ $name }}</p>
        <p><strong>Email:</strong> {{ $email }}</p>
        <p><strong>Temporary Password:</strong> {{ $tempPassword }}</p>
        <p><strong>Token Expiry:</strong> {{ $tokenExpiresAt ? \Carbon\Carbon::parse($tokenExpiresAt)->format('d M Y, h:i A') : 'N/A' }}</p>
    </div>
    <div style="text-align:center; margin:30px 0;">
        <a href="{{ env('FRONTEND_URL', 'http://localhost:3001') }}/pages/auth/verify-email/{{ $remember_token }}"
           style="background:{{ env('APP_THEME_COLOR', '#E41F07') }}; color:#fff; padding:14px 28px; text-decoration:none; border-radius:5px; font-size:16px; font-weight:bold; display:inline-block;">
            Verify My Email
        </a>
    </div>
    <p style="text-align:center; color:#d9534f; font-size:14px;">
        This verification link is valid for 10 minutes only.
    </p>
    <p style="font-size:14px; margin-top:25px; color:#555;">
        Please remember to change your password after your first login for better security.<br>
        If you did not request this account, please ignore this message.
    </p>
@endsection
