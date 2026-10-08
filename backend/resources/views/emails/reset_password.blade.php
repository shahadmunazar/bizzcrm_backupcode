@extends('emails.layouts.master')
@section('title', 'Reset Your Password')
@section('content')
    <h2 style="font-size: 20px; color: #333;">Reset Your Password</h2>
    <p style="color: #555; font-size: 16px;">Hello,</p>
    <p style="color: #555; font-size: 16px;">
        You recently requested to reset your password for your {{ config('app.name') }} account. Click the button below to proceed.
    </p>
    <table cellpadding="0" cellspacing="0" width="100%" style="margin: 30px 0;">
        <tr>
            <td align="center">
                <a href="{{ env('FRONTEND_URL', 'http://localhost:3001') }}/pages/auth/reset-password?token={{ $token }}&email={{ urlencode($email) }}"
                   style="display: inline-block; padding: 12px 24px; background-color: {{ env('APP_THEME_COLOR', '#E41F07') }}; color: #fff; text-decoration: none; font-weight: bold; border-radius: 5px;">
                    Reset Password
                </a>
            </td>
        </tr>
    </table>
    <p style="color: #999; font-size: 14px;">
        This password reset link will expire in 10 minutes.
        If you did not request a password reset, please ignore this email.
    </p>
    <p style="color: #999; font-size: 14px;">– The {{ config('app.name') }} Team</p>
@endsection
