@extends('emails.layouts.master')
@section('title', 'Password Reset Successfully')
@section('content')
    <h2 style="font-size: 20px; color: #333;">Hello {{ $name }},</h2>
    <p style="color: #555; font-size: 16px;">
        Your password has been successfully reset.
    </p>
    <p style="color: #555; font-size: 16px;">
        If you did not perform this action, please contact our support team immediately to secure your account.
    </p>
    <table cellpadding="0" cellspacing="0" width="100%" style="margin: 30px 0;">
        <tr>
            <td align="center">
                <a href="{{ url('/login') }}"
                   style="display: inline-block; padding: 12px 24px; background-color: {{ env('APP_THEME_COLOR', '#E41F07') }}; color: #fff; text-decoration: none; font-weight: bold; border-radius: 5px;">
                    Login Now
                </a>
            </td>
        </tr>
    </table>
    <p style="color: #999; font-size: 14px;">
        Thank you for using {{ $appName ?? config('app.name') }}!
    </p>
    <p style="color: #999; font-size: 14px;">– The {{ $appName ?? config('app.name') }} Team</p>
@endsection
