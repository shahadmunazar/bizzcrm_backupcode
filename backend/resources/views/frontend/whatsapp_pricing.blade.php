@extends('layouts.frontend.main')
@section('title', 'WhatsApp Plans')
@section('description', 'Choose the perfect WhatsApp plan for your business engagement.')
@push('styles')
<style>
    .wa-plan-card {
        border-radius: 12px;
        background: #fff;
        padding: 30px;
        position: relative;
        transition: transform 0.3s ease;
    }
    
    .wa-plan-card:hover {
        transform: translateY(-5px);
    }

    /* Normal Plan Styling (Green) */
    .wa-plan-normal {
        border: 2px solid #28a745;
    }
    .wa-plan-normal .discount-badge {
        border: 1px solid #ff9800;
        color: #ff9800;
        border-radius: 20px;
        padding: 4px 12px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-block;
        margin-bottom: 15px;
    }
    .wa-plan-normal .plan-title {
        color: #28a745;
        font-weight: 700;
        font-size: 1.5rem;
    }
    .wa-plan-normal .plan-subtitle {
        color: #28a745;
        font-size: 0.9rem;
        margin-bottom: 15px;
    }
    .wa-plan-normal .btn-offer {
        background-color: #28a745;
        color: white;
        border-radius: 8px;
        font-weight: 600;
        padding: 10px 0;
        width: 100%;
        border: none;
    }
    .wa-plan-normal .btn-offer:hover {
        background-color: #218838;
    }

    /* Highlighted Plan Styling (Orange) */
    .wa-plan-highlight {
        border: 2px solid #ff9800;
    }
    .wa-plan-highlight .discount-badge {
        border: 1px solid #ff9800;
        background-color: #ff9800;
        color: #fff;
        border-radius: 20px;
        padding: 4px 12px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-block;
        margin-bottom: 15px;
    }
    .wa-plan-highlight .plan-title {
        color: #ff9800;
        font-weight: 700;
        font-size: 1.5rem;
    }
    .wa-plan-highlight .plan-subtitle {
        color: #ff9800;
        font-size: 0.9rem;
        margin-bottom: 15px;
    }
    .wa-plan-highlight .btn-offer {
        background-color: #ff9800;
        color: white;
        border-radius: 8px;
        font-weight: 600;
        padding: 10px 0;
        width: 100%;
        border: none;
    }
    .wa-plan-highlight .btn-offer:hover {
        background-color: #e68a00;
    }

    /* Common Shared Styles */
    .price-block {
        margin-bottom: 20px;
    }
    .original-price {
        text-decoration: line-through;
        color: #888;
        font-size: 1.2rem;
        margin-right: 10px;
    }
    .current-price {
        font-size: 2.5rem;
        font-weight: 700;
        color: #333;
    }
    .gst-text {
        font-size: 0.9rem;
        color: #666;
        font-weight: 500;
    }
    .billing-cycle {
        font-size: 0.8rem;
        color: #888;
        display: block;
        margin-top: 5px;
    }

    .msg-cost-box {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        font-size: 0.85rem;
        color: #555;
    }

    .feature-list {
        list-style: none;
        padding-left: 0;
        margin-top: 25px;
    }
    .feature-list li {
        margin-bottom: 12px;
        font-size: 0.9rem;
        color: #333;
        display: flex;
        align-items: flex-start;
    }
    .feature-list li i.fa-check-circle {
        color: #28a745; /* green check */
        margin-right: 10px;
        margin-top: 3px;
        font-size: 1rem;
    }
    
    /* Highlighted features like AiSensy screenshot */
    .highlight-text {
        background-color: #e6f3ff;
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<section class="breadcrumb_area">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb_content text-center">
                    <h2 class="breadcrumb_title">WhatsApp Plans</h2>
                    <ul class="breadcrumb_menu list-unstyled">
                        <li><a href="{{ url('/') }}">Home</a></li>
                        <li>WhatsApp Plans</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="pricing_area section_padding">
    <div class="container">
        <div class="row justify-content-center">
            @foreach($whatsappPlans as $plan)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="wa-plan-card {{ $plan->is_highlighted ? 'wa-plan-highlight' : 'wa-plan-normal' }}">
                        @if($plan->discount_badge)
                            <div class="discount-badge">{{ $plan->discount_badge }}</div>
                        @endif
                        
                        <h3 class="plan-title">
                            {{ $plan->name }} 
                            @if($plan->savings_text)
                                <span style="font-size: 1.1rem; color: inherit;">{{ $plan->savings_text }}</span>
                            @endif
                        </h3>
                        
                        @if($plan->description)
                            <p class="plan-subtitle">{{ $plan->description }}</p>
                        @endif

                        <div class="price-block">
                            @if($plan->original_price)
                                <span class="original-price">₹{{ number_format($plan->original_price, 0) }}</span>
                            @endif
                            <span class="current-price">
                                @if($plan->is_highlighted)
                                    <span style="color:#ff9800;">₹{{ number_format($plan->price, 0) }}</span>
                                @else
                                    <span style="color:#28a745;">₹{{ number_format($plan->price, 0) }}</span>
                                @endif
                            </span>
                            <span class="gst-text">(+ GST)</span>
                            <span class="billing-cycle">{{ $plan->billing_cycle }}</span>
                        </div>

                        <div class="msg-cost-box">
                            <div class="fw-bold mb-1">Messaging (India): <i class="fa fa-info-circle text-warning"></i></div>
                            <div>Marketing: {{ $plan->marketing_msg_cost }}</div>
                            <div>Utility: {{ $plan->utility_msg_cost }}</div>
                        </div>

                        <button class="btn-offer">{{ $plan->button_text }}</button>

                        <ul class="feature-list">
                            @if(is_array($plan->features))
                                @foreach($plan->features as $feature)
                                    @if(trim($feature) !== '')
                                        <li>
                                            <i class="fa fa-check-circle"></i>
                                            <span>{!! str_replace(['Unlimited Free Service Conversations'], ['<span class="highlight-text">Unlimited Free Service Conversations</span>'], e($feature)) !!}</span>
                                        </li>
                                    @endif
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
