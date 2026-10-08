@extends('emails.layouts.master')
@section('title', 'Subscription Expiry Reminder')
@section('content')
    <h2 style="font-size: 20px; color: #333;">Subscription Expiring Soon!</h2>
    <p style="color: #555; font-size: 16px;">Your plan will expire in {{ $daysLeft }} days</p>

    <p style="color: #555; font-size: 16px;">Hello <strong>{{ $user->name }}</strong>,</p>
    <p style="color: #555; font-size: 16px;">This is a friendly reminder that your subscription to the <strong>{{ $plan->name }}</strong> is set to expire soon.</p>
    
    <div style="background: #fffbeb; border-radius: 8px; padding: 24px; margin: 24px 0; border: 1px solid #fde68a;">
        <div style="font-size: 20px; font-weight: 700; color: #92400e; margin-bottom: 8px;">{{ $plan->name }}</div>
        <div style="font-size: 18px; font-weight: 600; color: #b45309;">
            Expires On: {{ $subscription->next_billing_at?->format('F d, Y') ?? $subscription->ends_at?->format('F d, Y') }}
        </div>
    </div>

    <p style="color: #b45309; font-weight: 600;">To ensure uninterrupted access to your team and premium features, please make sure your payment method is up to date or renew your subscription.</p>
    
    <p style="color: #999; font-size: 14px; margin-top: 10px;">If you do not want to continue the service, please cancel your plan at least <strong>2 days</strong> before the expiry date to avoid any automatic charges.</p>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ url('/subscription-plan') }}" style="display: inline-block; padding: 12px 24px; background-color: #f59e0b; color: #ffffff; text-decoration: none; font-weight: bold; border-radius: 5px;">Renew or Upgrade Now</a>
    </div>
@endsection
