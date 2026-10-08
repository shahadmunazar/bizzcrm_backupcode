@extends('layouts.frontend.main')
@section('content')
<section class="sec_pad">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5 text-center">
                        <h2 class="f_p f_700 t_color3 mb_20">Confirm Subscription</h2>
                        <p class="f_400 f_size_16 l_height26 mb_20">You are subscribing to the <strong>{{ $plan->name }}</strong> plan ({{ ucfirst($plan->type) }}).</p>
                        
                        @if($plan->trial_period_days > 0)
                            <div class="alert alert-warning border-0 small mb-4">
                                <i class="ti ti-clock-filled me-1"></i>
                                <strong>Trial Period:</strong> Your first {{ $plan->trial_period_days }} days are free! 
                                <br>
                                <span class="text-muted">A nominal <strong>₹1</strong> will be charged now for verification. Full payment of ₹{{ number_format($plan->price) }} will be automatically deducted after the trial ends.</span>
                            </div>
                        @endif

                        <div class="price_info mb-4">
                            <h3 class="f_p f_700 f_size_30">
                                @if($plan->currency == 'INR') ₹ @elseif($plan->currency == 'USD') $ @else {{ $plan->currency }} @endif{{ number_format($plan->price) }}
                                <sub class="f_size_16">/{{ $plan->plan_type == 'topup' ? 'One Time' : ($plan->type == 'monthly' ? 'mo' : 'yr') }}</sub>
                            </h3>
                        </div>

                        <div class="otp_info bg-soft-info p-3 rounded mb-4 text-start">
                            <p class="f_size_14 mb-0">
                                <i class="ti ti-info-circle-filled text-info me-2"></i>
                                To save your card for future payments, please verify your number with the OTP sent to <strong>{{ $user->mobile ?? 'your registered number' }}</strong>.
                            </p>
                        </div>

                        <button id="rzp-button1" class="price_btn btn_hover" style="width: 100%; border: none;">Pay Now with Razorpay</button>
                        
                        <p class="mt-4 text-muted f_size_14 italic">Redirecting to secure payment gateway...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<form action="{{ isset($box_slug) ? box_route('box.checkout.callback', ['box_slug' => $box_slug]) : route('checkout.callback') }}" method="POST" id="razorpay-form" style="display: none;">
    @csrf
    <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
    <input type="hidden" name="razorpay_subscription_id" id="razorpay_subscription_id">
    <input type="hidden" name="razorpay_signature" id="razorpay_signature">
</form>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    var options = {
        "key": "{{ $razorpay_key }}",
        "subscription_id": "{{ $subscription_id }}",
        "name": "ZipZapGo",
        "description": "{{ $plan->name }} Subscription",
        "image": "{{ asset('assets/img/logo-small.png') }}",
        "handler": function (response){
            document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
            document.getElementById('razorpay_subscription_id').value = response.razorpay_subscription_id;
            document.getElementById('razorpay_signature').value = response.razorpay_signature;
            document.getElementById('razorpay-form').submit();
        },
        "prefill": {
            "name": "{{ $user->name }}",
            "email": "{{ $user->email }}",
            "contact": "{{ $user->mobile }}"
        },
        "theme": {
            "color": "#5e2ced"
        }
    };
    var rzp1 = new Razorpay(options);
    document.getElementById('rzp-button1').onclick = function(e){
        rzp1.open();
        e.preventDefault();
    }
    
    // Auto-open on load
    window.onload = function() {
        rzp1.open();
    };
</script>

<style>
    .bg-soft-info {
        background-color: rgba(13, 202, 240, 0.1);
        border: 1px solid rgba(13, 202, 240, 0.2);
        color: #084298;
    }
</style>
@endsection
