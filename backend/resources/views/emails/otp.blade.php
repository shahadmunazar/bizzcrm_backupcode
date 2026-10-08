@extends('emails.layouts.master')
@section('title', 'Your OTP Code')
@section('content')
    <h2 style="color: #2c3e50;">🔐 Your One-Time Password</h2>
    <p style="font-size: 16px; color: #555;">
        Use the OTP below to complete your verification process.
    </p>
    <div style="margin: 20px 0;">
        <span style="display: inline-block; font-size: 32px; font-weight: bold; color: #2d89ef; background: #eef6ff; padding: 15px 30px; border-radius: 6px; letter-spacing: 4px;">
            {{ $otp }}
        </span>
    </div>
    <p style="font-size: 15px; color: #333;">
        This OTP is valid until: <strong style="color: #e74c3c;">
            {{ \Carbon\Carbon::parse($otpExpiresAt)->format('h:i A, d M Y') }}
        </strong>
    </p>
    <p style="font-size: 13px; color: #999; margin-top: 20px;">
        Please do not share this code with anyone. If you did not request this OTP, please ignore this message.
    </p>
@endsection
