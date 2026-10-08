@extends('emails.layouts.master')
@section('title', 'Subscription Cancelled')
@section('content')
    <h2 style="font-size: 20px; color: #333;">Subscription Cancelled</h2>
    <p style="color: #555; font-size: 16px;">We're sorry to see you go.</p>
    
    <p style="color: #555; font-size: 16px;">Hello <strong>{{ $user->name }}</strong>,</p>
    <p style="color: #555; font-size: 16px;">This email confirms that your subscription to the <strong>{{ $plan->name }}</strong> has been cancelled as per your request.</p>
    
    <div style="background: #fef2f2; border-radius: 8px; padding: 24px; margin: 24px 0; border: 1px solid #fee2e2;">
        <div style="font-size: 20px; font-weight: 700; color: #991b1b; margin-bottom: 8px;">{{ $plan->name }}</div>
        <div style="font-size: 16px; font-weight: 600; color: #b91c1c;">
            Access Ends On: {{ $subscription->next_billing_at?->format('F d, Y') ?? $subscription->ends_at?->format('F d, Y') }}
        </div>
    </div>

    <p style="color: #555; font-size: 16px;">You will continue to have full access to your premium features until the end of your current billing cycle on <strong>{{ $subscription->next_billing_at?->format('F d, Y') ?? $subscription->ends_at?->format('F d, Y') }}</strong>. After this date, no further payments will be deducted.</p>
    
    @if($reason)
        <p style="color: #555; font-size: 16px;"><strong>Reason for cancellation:</strong><br>
        <em style="color: #999;">"{{ $reason }}"</em></p>
    @endif

    <p style="color: #555; font-size: 16px;">We'd love to have you back! If you change your mind, you can always renew your subscription from your dashboard.</p>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ url('/subscription-plan') }}" style="display: inline-block; padding: 12px 24px; background-color: {{ env('APP_THEME_COLOR', '#E41F07') }}; color: #fff; text-decoration: none; font-weight: bold; border-radius: 5px;">View Plans</a>
    </div>
@endsection
