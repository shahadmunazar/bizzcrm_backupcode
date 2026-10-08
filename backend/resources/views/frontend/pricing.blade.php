@extends('layouts.frontend.main')
@section('content')
@section('title', 'Pricing')
@section('description', 'Choose the perfect plan for your business needs. Simple, transparent pricing with no hidden fees.')
<section class="breadcrumb_area">
    <img class="breadcrumb_shap" src="{{ asset('assets/frontend/main/img/breadcrumb/banner_bg.png') }}" alt="">
    <div class="container">
        <div class="breadcrumb_content text-center">
            <h1 class="f_p f_700 f_size_50 w_color l_height50 mb_20">Our Pricing </h1>
            <p class="f_400 w_color f_size_16 l_height26">Choose the perfect plan for your business needs.</p>
        </div>
    </div>
</section>

<section class="pricing_area_two sec_pad">
    <div class="container custom_container p0">
        <div class="sec_title text-center mb_70">
            <h2 class="f_p f_size_30 l_height50 f_600 t_color2">Simple, Transparent Pricing</h2>
            <p class="f_400 f_size_16 l_height30 mb-0">No hidden fees. Cancel anytime.</p>
        </div>

        <ul class="nav nav-pills justify-content-center mb-5 category-tabs" id="categoryTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active category-link" id="crm-category-tab" data-bs-toggle="pill" href="#crm-category" role="tab" aria-controls="crm-category" aria-selected="true" style="border-radius: 30px; padding: 10px 30px; font-weight: 600;">CRM Software</a>
            </li>
            <li class="nav-item ms-3">
                <a class="nav-link category-link" id="whatsapp-category-tab" data-bs-toggle="pill" href="#whatsapp-category" role="tab" aria-controls="whatsapp-category" aria-selected="false" style="border-radius: 30px; padding: 10px 30px; font-weight: 600;">WhatsApp API</a>
            </li>
        </ul>

        <div class="tab-content" id="categoryTabsContent">
            <!-- CRM Category -->
            <div class="tab-pane fade show active" id="crm-category" role="tabpanel" aria-labelledby="crm-category-tab">

                <ul class="nav nav-tabs price_tab price_tab_two mt_30" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active active_hover" id="monthly-tab" data-bs-toggle="tab" href="#monthly" role="tab" aria-controls="monthly" aria-selected="true">
                            Billed Monthly
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="yearly-tab" data-bs-toggle="tab" href="#yearly" role="tab" aria-controls="yearly" aria-selected="false">
                            Billed Yearly
                        </a>
                    </li>
                </ul>

                <div class="tab-content price_content price_content_two">
            <!-- Monthly Tab -->
            <div class="tab-pane fade show active" id="monthly" role="tabpanel" aria-labelledby="monthly-tab">
                <!-- Pricing Cards (Monthly) -->
                <div class="row justify-content-center mb_70">
                    @foreach($monthlyPlans as $plan)
                    <div class="col-lg-4 col-sm-6">
                        <div class="price_item">
                            @if($plan->name == 'Starter')
                                <div class="tag"><span>Popular</span></div>
                            @endif
                            <h5 class="f_p f_size_20 f_600 t_color2 mt_30">{{ $plan->name }}</h5>
                            <p>{{ $plan->description }}</p>
                            <div class="price f_700 f_size_40 t_color2">
                                @if($plan->currency == 'INR') ₹ @elseif($plan->currency == 'USD') $ @elseif($plan->currency == 'EUR') € @else {{ $plan->currency }} @endif{{ number_format($plan->price) }}
                                <sub class="f_size_16 f_400">/{{ $plan->type == 'monthly' ? 'mo' : 'yr' }}</sub>
                            </div>
                            
                            @if($plan->trial_period_days > 0)
                                <div class="badge bg-soft-success text-success mb-3">
                                    <i class="ti-gift me-1"></i> {{ $plan->trial_period_days }} Days Free Trial
                                </div>
                            @endif

                            <ul class="list-unstyled p_list">
                                @php $count = 0; @endphp
                                @foreach($plan->features ?? [] as $featureName => $value)
                                    @if($value && $value !== '❌' && $count < 6)
                                        <li><i class="ti-check"></i> {{ $featureName }}{{ ($value !== '✅' && $value !== '') ? ': ' . $value : '' }}</li>
                                        @php $count++; @endphp
                                    @endif
                                @endforeach
                            </ul>
                            <a href="{{ Auth::check() && Auth::user()->box_id ? box_route('checkout.subscribe', ['planId' => $plan->id, 'box_slug' => \App\Models\Box::find(Auth::user()->box_id)->slug ?? 'default']) : route('login') }}" class="price_btn btn_hover">Subscribe Now</a>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Detailed Comparison Table (Monthly) -->
                @if($monthlyPlans->count() > 0)
                <div class="row">
                    <div class="col-lg-12">
                        <div class="sec_title text-center mb_50">
                            <h3 class="f_p f_size_24 l_height40 f_600 t_color2">Detailed Feature Comparison</h3>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped comparison-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-start" style="width: 25%;">Feature</th>
                                        @foreach($monthlyPlans as $plan)
                                            <th class="text-center" style="width: 25%;">{{ $plan->name }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($masterFeatures as $feature)
                                    <tr>
                                        <td class="text-start f_600">{{ $feature->name }}</td>
                                        @foreach($monthlyPlans as $plan)
                                            <td class="text-center">
                                                @php
                                                    $displayValue = $plan->features[$feature->name] ?? '❌';
                                                @endphp
                                                @if($displayValue == '✅')
                                                    <i class="ti-check text-success"></i>
                                                @elseif($displayValue == '❌')
                                                    <i class="ti-close text-danger"></i>
                                                @else
                                                    {{ $displayValue }}
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Yearly Tab -->
            <div class="tab-pane fade" id="yearly" role="tabpanel" aria-labelledby="yearly-tab">
                <!-- Pricing Cards (Yearly) -->
                <div class="row justify-content-center mb_70">
                    @foreach($yearlyPlans as $plan)
                    <div class="col-lg-4 col-sm-6">
                        <div class="price_item">
                            @if($plan->name == 'Starter')
                                <div class="tag"><span>Popular</span></div>
                            @endif
                            <h5 class="f_p f_size_20 f_600 t_color2 mt_30">{{ $plan->name }}</h5>
                            <p>{{ $plan->description }}</p>
                            <div class="price f_700 f_size_40 t_color2">
                                @if($plan->currency == 'INR') ₹ @elseif($plan->currency == 'USD') $ @elseif($plan->currency == 'EUR') € @else {{ $plan->currency }} @endif{{ number_format($plan->price) }}
                                <sub class="f_size_16 f_400">/yr</sub>
                            </div>
                            
                            @if($plan->trial_period_days > 0)
                                <div class="badge bg-soft-success text-success mb-3">
                                    <i class="ti-gift me-1"></i> {{ $plan->trial_period_days }} Days Free Trial
                                </div>
                            @endif

                            <ul class="list-unstyled p_list">
                                @php $count = 0; @endphp
                                @foreach($plan->features ?? [] as $featureName => $value)
                                    @if($value && $value !== '❌' && $count < 6)
                                        <li><i class="ti-check"></i> {{ $featureName }}{{ ($value !== '✅' && $value !== '') ? ': ' . $value : '' }}</li>
                                        @php $count++; @endphp
                                    @endif
                                @endforeach
                            </ul>
                            <a href="{{ Auth::check() && Auth::user()->box_id ? box_route('checkout.subscribe', ['planId' => $plan->id, 'box_slug' => \App\Models\Box::find(Auth::user()->box_id)->slug ?? 'default']) : route('login') }}" class="price_btn btn_hover">Subscribe Now</a>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Detailed Comparison Table (Yearly) -->
                @if($yearlyPlans->count() > 0)
                <div class="row">
                    <div class="col-lg-12">
                        <div class="sec_title text-center mb_50">
                            <h3 class="f_p f_size_24 l_height40 f_600 t_color2">Detailed Feature Comparison</h3>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped comparison-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-start" style="width: 25%;">Feature</th>
                                        @foreach($yearlyPlans as $plan)
                                            <th class="text-center" style="width: 25%;">{{ $plan->name }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($masterFeatures as $feature)
                                    <tr>
                                        <td class="text-start f_600">{{ $feature->name }}</td>
                                        @foreach($yearlyPlans as $plan)
                                            <td class="text-center">
                                                @php
                                                    $displayValue = $plan->features[$feature->name] ?? '❌';
                                                @endphp
                                                @if($displayValue == '✅')
                                                    <i class="ti-check text-success"></i>
                                                @elseif($displayValue == '❌')
                                                    <i class="ti-close text-danger"></i>
                                                @else
                                                    {{ $displayValue }}
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div> <!-- End of price_content_two -->
    </div> <!-- End of CRM Category -->
    
    <!-- WhatsApp Category -->
    <div class="tab-pane fade" id="whatsapp-category" role="tabpanel" aria-labelledby="whatsapp-category-tab">
        <div class="row justify-content-center mt-5">
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
                                @foreach($plan->features as $featureName => $value)
                                    @if($value && $value !== '❌')
                                        <li>
                                            <i class="fa fa-check-circle"></i>
                                            <span>{!! str_replace(['Unlimited Free Service Conversations'], ['<span class="highlight-text">Unlimited Free Service Conversations</span>'], e($featureName)) !!}{{ ($value !== '✅' && $value !== '') ? ': ' . $value : '' }}</span>
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
</div> <!-- End of categoryTabsContent -->

    </div>
</section>

<style>
    .comparison-table th, .comparison-table td {
        vertical-align: middle;
        padding: 15px;
    }
    .comparison-table .text-start {
        text-align: left;
    }
    .comparison-table .ti-check {
        font-weight: bold;
    }
    .price_item .p_list li i {
        padding-right: 10px;
    }
    .nav-tabs.price_tab_two {
        display: flex;
        justify-content: center;
        border-bottom: none;
        margin-bottom: 50px;
        flex-wrap: nowrap; /* Force single row */
        gap: 20px;
    }
    .nav-tabs.price_tab_two .nav-item {
        margin-bottom: 0;
        display: inline-block;
    }
    .nav-tabs.price_tab_two .nav-link {
        border: 1px solid #e1e1e1;
        border-radius: 40px;
        padding: 10px 30px;
        color: #6a7695;
        cursor: pointer;
        white-space: nowrap;
        display: block;
        background: #fff;
    }
    .nav-tabs.price_tab_two .nav-link.active,
    .nav-tabs.price_tab_two .nav-link:hover {
        background: #5e2ced;
        color: #fff;
        border-color: #5e2ced;
    }
    .bg-soft-success {
        background-color: rgba(40, 167, 69, 0.1);
        color: #28a745;
        border: 1px solid rgba(40, 167, 69, 0.2);
    }
    .badge {
        padding: 5px 12px;
        font-weight: 500;
        border-radius: 4px;
        font-size: 13px;
    }

    .category-tabs .nav-link {
        color: #6a7695;
        border: 1px solid #e1e1e1;
    }
    .category-tabs .nav-link.active {
        background-color: #5e2ced !important;
        color: #fff !important;
        border-color: #5e2ced;
    }

    /* WhatsApp Card Styles */
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
        color: #28a745;
        margin-right: 10px;
        margin-top: 3px;
        font-size: 1rem;
    }
    
    .highlight-text {
        background-color: #e6f3ff;
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: 600;
    }
</style>
@endsection
