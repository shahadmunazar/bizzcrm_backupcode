@extends('emails.layouts.master')
@section('title', 'Subscription Confirmed')
@section('content')
    <h2 style="font-size: 20px; color: #333;">Welcome Aboard!</h2>
    <p style="color: #555; font-size: 16px;">Your subscription is now active.</p>

    <p style="color: #555; font-size: 16px;">Hello <strong>{{ $user->name }}</strong>,</p>
    <p style="color: #555; font-size: 16px;">Great news! We've successfully received your payment and activated your subscription. You now have full access to all the premium features included in the <strong>{{ $plan->name }}</strong>.</p>
    
    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 32px; margin: 24px 0;">
        <div style="text-transform: uppercase; font-size: 12px; font-weight: 700; color: #6366f1; letter-spacing: 0.05em; margin-bottom: 4px;">Active Plan</div>
        <div style="font-size: 24px; font-weight: 800; color: #0f172a; margin-bottom: 12px;">{{ $plan->name }}</div>
        <div style="font-size: 36px; font-weight: 800; color: #1e293b; display: flex; align-items: baseline;">
            @if($plan->currency == 'INR') ₹ @else {{ $plan->currency }} @endif{{ number_format($plan->price) }}
            <span style="font-size: 16px; font-weight: 400; color: #64748b; margin-left: 4px;">/{{ $plan->type == 'monthly' ? 'month' : 'year' }}</span>
        </div>
    </div>

    <div style="background: #f0f9ff; border-left: 4px solid #0ea5e9; padding: 16px; border-radius: 0 8px 8px 0; margin: 24px 0; color: #0369a1; font-size: 14px;">
        <strong>Recurring Billing:</strong> This is a recurring subscription. Your payment method will be automatically charged <strong>@if($plan->currency == 'INR') ₹ @else {{ $plan->currency }} @endif{{ number_format($plan->price) }}</strong> every <strong>{{ $plan->type == 'monthly' ? 'month' : 'year' }}</strong> from your start date until you choose to cancel.
    </div>

    <table style="width: 100%; border-collapse: collapse; margin-top: 24px;">
        <tr>
            <td style="color: #64748b; font-size: 13px; font-weight: 600; text-transform: uppercase; width: 40%; padding: 12px 0; border-bottom: 1px solid #f1f5f9;">Started On</td>
            <td style="color: #1e293b; font-size: 14px; font-weight: 500; text-align: right; padding: 12px 0; border-bottom: 1px solid #f1f5f9;">{{ $subscription->starts_at?->format('F d, Y') ?? now()->format('F d, Y') }}</td>
        </tr>
        @if($company ?? null)
        <tr>
            <td style="color: #64748b; font-size: 13px; font-weight: 600; text-transform: uppercase; width: 40%; padding: 12px 0; border-bottom: 1px solid #f1f5f9;">Company Account</td>
            <td style="color: #1e293b; font-size: 14px; font-weight: 500; text-align: right; padding: 12px 0; border-bottom: 1px solid #f1f5f9;">{{ $company->name }}</td>
        </tr>
        @endif
        <tr>
            <td style="color: #64748b; font-size: 13px; font-weight: 600; text-transform: uppercase; width: 40%; padding: 12px 0; border-bottom: 1px solid #f1f5f9;">Subscription ID</td>
            <td style="color: #1e293b; font-size: 14px; font-weight: 500; text-align: right; padding: 12px 0; border-bottom: 1px solid #f1f5f9;"><code style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px;">{{ $subscription->razorpay_subscription_id }}</code></td>
        </tr>
        <tr>
            <td style="color: #64748b; font-size: 13px; font-weight: 600; text-transform: uppercase; width: 40%; padding: 12px 0; border-bottom: 1px solid #f1f5f9;">Status</td>
            <td style="color: #1e293b; font-size: 14px; font-weight: 500; text-align: right; padding: 12px 0; border-bottom: 1px solid #f1f5f9;" style="color: #10b981; font-weight: 700;">● Active</td>
        </tr>
    </table>

    <div style="text-align: center; margin-top: 30px;">
        <a href="{{ url('/dashboard') }}" style="display: inline-block; padding: 12px 24px; background-color: {{ env('APP_THEME_COLOR', '#E41F07') }}; color: #fff; text-decoration: none; font-weight: bold; border-radius: 5px;">Launch Dashboard</a>
    </div>

    <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid #e2e8f0; text-align: center;">
        <a href="{{ url('/privacy-policy') }}" style="color: {{ env('APP_THEME_COLOR', '#E41F07') }}; text-decoration: none; margin: 0 12px; font-size: 13px; font-weight: 500;">Privacy Policy</a>
        <a href="{{ url('/terms-and-conditions') }}" style="color: {{ env('APP_THEME_COLOR', '#E41F07') }}; text-decoration: none; margin: 0 12px; font-size: 13px; font-weight: 500;">Terms & Conditions</a>
    </div>
@endsection
