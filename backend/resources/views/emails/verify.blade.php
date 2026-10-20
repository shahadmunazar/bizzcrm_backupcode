@extends('emails.layouts.master')
@section('title', 'Verify Your Email')
@section('content')
    <h2 style="font-size: 22px; color: #333; margin-bottom: 20px;">👋 Welcome to {{ config('app.name') }}, {{ $name }}!</h2>
    <p style="color: #555; font-size: 16px; line-height: 1.6;">
        Your CRM account has been successfully created. Please verify your email to complete the registration.
    </p>

    <div style="background-color: #f9f9f9; border-radius: 8px; padding: 20px; margin: 25px 0; border-left: 4px solid {{ env('APP_THEME_COLOR', '#E41F07') }};">
        <p style="color: #333; margin: 5px 0;"><strong>Name:</strong> {{ $name }}</p>
        <p style="color: #333; margin: 5px 0;"><strong>Email:</strong> {{ $email }}</p>
        <p style="color: #333; margin: 5px 0;"><strong>Temporary Password:</strong> {{ $password }}</p>
    </div>

    <table cellpadding="0" cellspacing="0" width="100%" style="margin: 30px 0;">
        <tr>
            <td align="center">
                <a href="{{ $verificationUrl ?? url('/verify-email/' . $token) }}"
                   style="display: inline-block; padding: 14px 30px; background-color: {{ env('APP_THEME_COLOR', '#E41F07') }}; color: #fff; text-decoration: none; font-weight: bold; border-radius: 6px; font-size: 16px; transition: background-color 0.3s ease;">
                    Verify My Email
                </a>
            </td>
        </tr>
    </table>

    <p style="color: #555; font-size: 14px; line-height: 1.6;">
        Please change your password after your first login for better security.<br>
        If you did not request this account, please ignore this email.
    </p>
@endsection
