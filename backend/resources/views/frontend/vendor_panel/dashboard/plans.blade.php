@extends('layouts.vendor')

@section('title', 'Subscription Plans')

@section('content')
<style>
    .plan-card {
        border: 2px solid #e5e7eb;
        border-radius: 16px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: #fff;
        position: relative;
        overflow: hidden;
    }
    .plan-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 24px -10px rgba(0, 0, 0, 0.15);
        border-color: #6366f1;
    }
    .plan-card.popular {
        border-color: #4f46e5;
        box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.2);
    }
    .plan-card.popular::before {
        content: 'POPULAR';
        position: absolute;
        top: 15px;
        right: -30px;
        background: #4f46e5;
        color: white;
        padding: 4px 35px;
        font-size: 10px;
        font-weight: 700;
        transform: rotate(45deg);
        letter-spacing: 0.5px;
    }
    .plan-price {
        font-size: 2.25rem;
        font-weight: 800;
        color: #111827;
    }
    .feature-item {
        font-size: 0.9rem;
        color: #4b5563;
        display: flex;
        align-items: center;
        margin-bottom: 0.75rem;
    }
    .feature-item i {
        color: #10b981;
        margin-right: 0.5rem;
        font-size: 1.1rem;
    }
</style>

<div class="page-wrapper">
    <div class="content">
        <!-- Notification Alert Container -->
        <div id="vendorAlertContainer" class="mb-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="ti ti-check me-2 fs-18"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="ti ti-alert-circle me-2 fs-18"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(!$activeSubscription)
                <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center justify-content-between p-3" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-alert-triangle fs-24 me-3 text-warning"></i>
                        <div>
                            <h6 class="mb-1 fw-bold text-dark">No Active Subscription Plan</h6>
                            <p class="mb-0 text-muted small">You are currently on a limited tier. Upgrade to unlock advanced features, unlimited referrals, and higher commission rates.</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-success border-0 shadow-sm d-flex align-items-center justify-content-between p-3" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-circle-check-filled fs-24 me-3 text-success"></i>
                        <div>
                            <h6 class="mb-1 fw-bold text-dark">Active Subscription: {{ $activeSubscription->plan->name ?? 'Standard' }} ({{ ucfirst($activeSubscription->plan->type ?? '') }})</h6>
                            <p class="mb-0 text-muted small">Your plan is active until {{ $activeSubscription->ends_at ? \Carbon\Carbon::parse($activeSubscription->ends_at)->format('M d, Y') : 'Next Renewal' }}.</p>
                        </div>
                    </div>
                    <span class="badge bg-success text-white px-3 py-2">Status: Active</span>
                </div>
            @endif
        </div>

        <div class="text-center mb-5">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill small fw-semibold text-uppercase mb-2">Upgrade & Grow</span>
            <h2 class="fw-bold mb-2">Simple, Transparent Subscription Plans</h2>
            <p class="text-muted">Choose the ideal plan to scale your vendor operations and maximize performance.</p>

            <!-- Billing Toggle -->
            <div class="d-inline-flex align-items-center p-1 bg-light border rounded-pill mt-3">
                <button type="button" class="btn btn-sm rounded-pill px-4 py-2 fw-semibold btn-primary" id="btnMonthly" onclick="toggleBilling('monthly')">Monthly Plans</button>
                <button type="button" class="btn btn-sm rounded-pill px-4 py-2 fw-semibold text-muted" id="btnYearly" onclick="toggleBilling('yearly')">Yearly Plans (Save 15%)</button>
            </div>
        </div>

        <!-- Monthly Plans Grid -->
        <div id="monthlyPlansContainer" class="row g-4 justify-content-center">
            @forelse($monthlyPlans as $plan)
                @php
                    $isCurrent = $activeSubscription && $activeSubscription->plan_id == $plan->id;
                    $isPopular = strtolower($plan->name) === 'starter' || strtolower($plan->name) === 'professional';
                @endphp
                <div class="col-lg-4 col-md-6">
                    <div class="card plan-card h-100 p-4 {{ $isPopular ? 'popular' : '' }}">
                        <div class="card-body p-0 d-flex flex-column">
                            <div class="mb-4">
                                <h4 class="fw-bold text-dark mb-1">{{ $plan->name }}</h4>
                                <p class="text-muted small mb-3">Billed Monthly</p>
                                <div class="d-flex align-items-baseline">
                                    <span class="fs-4 fw-bold me-1 text-muted">₹</span>
                                    <span class="plan-price">{{ number_format($plan->price, 2) }}</span>
                                    <span class="text-muted ms-1 small">/month</span>
                                </div>
                            </div>

                            <hr class="opacity-10 my-3">

                            <div class="features-list mb-4 flex-grow-1">
                                <div class="feature-item"><i class="ti ti-check"></i> Vendor Dashboard Access</div>
                                <div class="feature-item"><i class="ti ti-check"></i> Unique Referral Code & Links</div>
                                <div class="feature-item"><i class="ti ti-check"></i> Unlimited Customer Referrals</div>
                                <div class="feature-item"><i class="ti ti-check"></i> Commission Tracking & Reports</div>
                                <div class="feature-item"><i class="ti ti-check"></i> Email Invitation System</div>
                                <div class="feature-item"><i class="ti ti-check"></i> Standard Support</div>
                            </div>

                            @if($isCurrent)
                                <button class="btn btn-outline-success w-100 py-2.5 rounded-pill fw-bold" disabled>
                                    <i class="ti ti-check me-1"></i> Current Plan
                                </button>
                            @else
                                <a href="{{ route('checkout.subscribe', ['planId' => $plan->id]) }}" class="btn {{ $isPopular ? 'btn-primary' : 'btn-outline-primary' }} w-100 py-2.5 rounded-pill fw-bold">
                                    Select {{ $plan->name }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <p class="text-muted">No monthly plans available at this moment.</p>
                </div>
            @endforelse
        </div>

        <!-- Yearly Plans Grid -->
        <div id="yearlyPlansContainer" class="row g-4 justify-content-center" style="display: none;">
            @forelse($yearlyPlans as $plan)
                @php
                    $isCurrent = $activeSubscription && $activeSubscription->plan_id == $plan->id;
                    $isPopular = strtolower($plan->name) === 'starter' || strtolower($plan->name) === 'professional';
                @endphp
                <div class="col-lg-4 col-md-6">
                    <div class="card plan-card h-100 p-4 {{ $isPopular ? 'popular' : '' }}">
                        <div class="card-body p-0 d-flex flex-column">
                            <div class="mb-4">
                                <h4 class="fw-bold text-dark mb-1">{{ $plan->name }}</h4>
                                <p class="text-muted small mb-3">Billed Annually</p>
                                <div class="d-flex align-items-baseline">
                                    <span class="fs-4 fw-bold me-1 text-muted">₹</span>
                                    <span class="plan-price">{{ number_format($plan->price, 2) }}</span>
                                    <span class="text-muted ms-1 small">/year</span>
                                </div>
                            </div>

                            <hr class="opacity-10 my-3">

                            <div class="features-list mb-4 flex-grow-1">
                                <div class="feature-item"><i class="ti ti-check"></i> Vendor Dashboard Access</div>
                                <div class="feature-item"><i class="ti ti-check"></i> Unique Referral Code & Links</div>
                                <div class="feature-item"><i class="ti ti-check"></i> Unlimited Customer Referrals</div>
                                <div class="feature-item"><i class="ti ti-check"></i> Commission Tracking & Reports</div>
                                <div class="feature-item"><i class="ti ti-check"></i> Priority Vendor Partner Support</div>
                                <div class="feature-item"><i class="ti ti-check"></i> Annual Discount Applied</div>
                            </div>

                            @if($isCurrent)
                                <button class="btn btn-outline-success w-100 py-2.5 rounded-pill fw-bold" disabled>
                                    <i class="ti ti-check me-1"></i> Current Plan
                                </button>
                            @else
                                <a href="{{ route('checkout.subscribe', ['planId' => $plan->id]) }}" class="btn {{ $isPopular ? 'btn-primary' : 'btn-outline-primary' }} w-100 py-2.5 rounded-pill fw-bold">
                                    Select {{ $plan->name }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <p class="text-muted">No yearly plans available at this moment.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleBilling(period) {
        if (period === 'monthly') {
            $('#monthlyPlansContainer').show();
            $('#yearlyPlansContainer').hide();
            $('#btnMonthly').removeClass('text-muted').addClass('btn-primary');
            $('#btnYearly').removeClass('btn-primary').addClass('text-muted');
        } else {
            $('#monthlyPlansContainer').hide();
            $('#yearlyPlansContainer').show();
            $('#btnYearly').removeClass('text-muted').addClass('btn-primary');
            $('#btnMonthly').removeClass('btn-primary').addClass('text-muted');
        }
    }
</script>
@endsection
