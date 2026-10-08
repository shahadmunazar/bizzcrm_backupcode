        <div class="tab-content w-100">
            <!-- Account usage Tab -->
            <div class="tab-pane fade show active" id="account-usage" role="tabpanel">
                <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold mb-0">Current Subscription Status</h5>
                            @php $primarySub = $subscription ?? $whatsappSubscription; @endphp
                            @if(!$primarySub)
                                <span class="badge bg-secondary">No Active Plan</span>
                            @endif
                        </div>

                        @if($primarySub)
                            <div class="accordion" id="subscriptionAccordion">
                                @if($subscription)
                                <div class="accordion-item border-0 shadow-sm mb-3 rounded overflow-hidden">
                                    <h2 class="accordion-header" id="heading_crm">
                                        <button class="accordion-button bg-light fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_crm" aria-expanded="true" aria-controls="collapse_crm">
                                            <div class="d-flex align-items-center w-100 me-3">
                                                <i class="ti ti-box me-2 text-primary fs-4"></i> 
                                                <span class="me-auto">{{ $subscription->plan->name }}</span>
                                                @if($subscription->status == 'cancelled')
                                                    <span class="badge bg-warning">Cancelled (Ends on {{ $subscription->next_billing_at?->format('M d, Y') }})</span>
                                                @elseif($subscription->starts_at && $subscription->starts_at->isFuture())
                                                    <span class="badge bg-info">On Trial</span>
                                                @else
                                                    <span class="badge bg-success">Active</span>
                                                @endif
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse_crm" class="accordion-collapse collapse show" aria-labelledby="heading_crm" data-bs-parent="#subscriptionAccordion">
                                        <div class="accordion-body p-4">
                                            <div class="active_plan_info bg-light p-3 rounded mb-4">
                                                <div class="row align-items-center">
                                                    <div class="col-md-6">
                                                        <h5 class="fw-bold text-dark mb-1">{{ $subscription->plan->name }}</h5>
                                                        <p class="mb-0 text-muted small">{{ $subscription->plan->description }}</p>
                                                    </div>
                                                    <div class="col-md-6 text-md-end">
                                                        <h4 class="fw-bold mb-0">
                                                            @if($subscription->plan->currency == 'INR') ₹ @else $ @endif{{ number_format($subscription->plan->price) }}
                                                            <sub class="fs-7 fw-normal">/{{ $subscription->plan->type == 'monthly' ? 'mo' : 'yr' }}</sub>
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>

                                            @if($subscription->starts_at && $subscription->starts_at->isFuture())
                                            <div class="alert alert-info border-0 shadow-sm mt-0 mb-4 d-flex align-items-start">
                                                <i class="ti ti-info-circle-filled fs-4 me-3 text-info"></i>
                                                <div>
                                                    <h6 class="fw-bold mb-1">You are currently on a free trial!</h6>
                                                    <p class="mb-0 small">Your recurring plan amount of <strong>@if($subscription->plan->currency == 'INR') ₹ @else $ @endif{{ number_format($subscription->plan->price) }}</strong> will be automatically deducted on <strong>{{ $subscription->starts_at->format('F d, Y') }}</strong>. If you do not wish to continue, please <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#cancelModal" class="fw-bold text-decoration-underline text-danger">cancel at least one day before</a> to avoid being charged.</p>
                                                </div>
                                            </div>
                                            @endif

                                            <div class="details_list mt-2">
                                                <div class="row mb-3">
                                                    <div class="col-sm-4 text-muted">Subscription ID:</div>
                                                    <div class="col-sm-8 fw-bold text-primary">{{ $subscription->razorpay_subscription_id }}</div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-sm-4 text-muted">Billing Cycle:</div>
                                                    <div class="col-sm-8 text-capitalize">{{ $subscription->plan->type }}</div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-sm-4 text-muted">Started On:</div>
                                                    <div class="col-sm-8">{{ $subscription->starts_at?->format('F d, Y') ?? 'N/A' }}</div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-sm-4 text-muted">Next Billing Date:</div>
                                                    <div class="col-sm-8 text-primary fw-bold">{{ $subscription->next_billing_at?->format('F d, Y') ?? 'N/A' }}</div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-sm-4 text-muted">Users Usage:</div>
                                                    <div class="col-sm-8 fw-bold">
                                                        {{ $usedUsersCount }} / {{ $totalUsersCapacity > 0 ? $totalUsersCapacity : 'Unlimited' }} Users
                                                        <span class="ms-2 badge {{ $totalUsersCapacity > 0 && $usedUsersCount >= $totalUsersCapacity ? 'bg-danger' : 'bg-primary' }} small">
                                                            {{ $totalUsersCapacity > 0 ? number_format(min(($usedUsersCount / $totalUsersCapacity) * 100, 100), 1) . '%' : '0%' }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="row mb-3 {{ ($razorpaySubscription && isset($razorpaySubscription['payment_method'])) ? '' : 'd-none' }}" id="payment-method-row">
                                                    <div class="col-sm-4 text-muted">Payment Method:</div>
                                                    <div class="col-sm-8 text-uppercase fw-bold" id="payment-method-text">{{ $paymentDetails ?? ($razorpaySubscription['payment_method'] ?? '') }}</div>
                                                </div>
                                            </div>

                                            @if($subscription->status != 'cancelled')
                                            <div class="mt-4 pt-3 border-top text-end">
                                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelModal" data-sub-id="{{ $subscription->id }}">
                                                    <i class="ti ti-x me-1"></i> Cancel CRM Plan
                                                </button>
                                            </div>
                                            @else 
                                            <div class="mt-4 p-3 bg-soft-warning rounded">
                                                <p class="mb-0 small text-dark">
                                                    <i class="ti ti-info-circle-filled me-1"></i> 
                                                    Your subscription was cancelled on <strong>{{ $subscription->cancelled_at?->format('M d, Y') }}</strong>. 
                                                    You will continue to have access to premium features until <strong>{{ $subscription->next_billing_at?->format('M d, Y') }}</strong>.
                                                </p>
                                                @if($subscription->cancellation_reason)
                                                    <p class="mt-2 mb-0 small text-muted italic">"{{ $subscription->cancellation_reason }}"</p>
                                                @endif
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($whatsappSubscription)
                                <div class="accordion-item border-0 shadow-sm mb-3 rounded overflow-hidden">
                                    <h2 class="accordion-header" id="heading_wa">
                                        <button class="accordion-button {{ !$subscription ? '' : 'collapsed' }} bg-light fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_wa" aria-expanded="{{ !$subscription ? 'true' : 'false' }}" aria-controls="collapse_wa">
                                            <div class="d-flex align-items-center w-100 me-3">
                                                <i class="ti ti-brand-whatsapp me-2 text-success fs-4"></i> 
                                                <span class="me-auto">{{ $whatsappSubscription->plan->name }}</span>
                                                @if($whatsappSubscription->status == 'cancelled')
                                                    <span class="badge bg-warning">Cancelled (Ends on {{ $whatsappSubscription->next_billing_at?->format('M d, Y') }})</span>
                                                @else
                                                    <span class="badge bg-success">Active</span>
                                                @endif
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse_wa" class="accordion-collapse collapse {{ !$subscription ? 'show' : '' }}" aria-labelledby="heading_wa" data-bs-parent="#subscriptionAccordion">
                                        <div class="accordion-body p-4">
                                            <div class="active_plan_info bg-light p-3 rounded mb-4">
                                                <div class="row align-items-center">
                                                    <div class="col-md-6">
                                                        <h5 class="fw-bold text-dark mb-1">{{ $whatsappSubscription->plan->name }} <span class="badge bg-soft-success text-success small">WhatsApp</span></h5>
                                                        <p class="mb-0 text-muted small">{{ $whatsappSubscription->plan->description }}</p>
                                                    </div>
                                                    <div class="col-md-6 text-md-end">
                                                        <h4 class="fw-bold mb-0">
                                                            @if($whatsappSubscription->plan->currency == 'INR') ₹ @else $ @endif{{ number_format($whatsappSubscription->plan->price) }}
                                                            <sub class="fs-7 fw-normal">/{{ $whatsappSubscription->plan->type == 'monthly' ? 'mo' : 'yr' }}</sub>
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="details_list mt-2">
                                                <div class="row mb-3">
                                                    <div class="col-sm-4 text-muted">Subscription ID:</div>
                                                    <div class="col-sm-8 fw-bold text-success">{{ $whatsappSubscription->razorpay_subscription_id }}</div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-sm-4 text-muted">Billing Cycle:</div>
                                                    <div class="col-sm-8 text-capitalize">{{ $whatsappSubscription->plan->type }}</div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-sm-4 text-muted">Started On:</div>
                                                    <div class="col-sm-8">{{ $whatsappSubscription->starts_at?->format('F d, Y') ?? 'N/A' }}</div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-sm-4 text-muted">Next Billing Date:</div>
                                                    <div class="col-sm-8 text-success fw-bold">{{ $whatsappSubscription->next_billing_at?->format('F d, Y') ?? 'N/A' }}</div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-sm-4 text-muted">WhatsApp Messages:</div>
                                                    <div class="col-sm-8 fw-bold">
                                                        {{ number_format($usedWhatsappMessagesCount) }} / Unlimited
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-sm-4 text-muted">WhatsApp Agents:</div>
                                                    <div class="col-sm-8 fw-bold">
                                                        {{ $usedWhatsAppAgentsCount }} / {{ $whatsappAgentLimit > 0 ? $whatsappAgentLimit : 'Unlimited' }} Agents
                                                        <span class="ms-2 badge {{ $whatsappAgentLimit > 0 && $usedWhatsAppAgentsCount >= $whatsappAgentLimit ? 'bg-danger' : 'bg-success' }} small">
                                                            {{ $whatsappAgentLimit > 0 ? number_format(min(($usedWhatsAppAgentsCount / $whatsappAgentLimit) * 100, 100), 1) . '%' : '0%' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            @if($whatsappSubscription->status != 'cancelled')
                                            <div class="mt-4 pt-3 border-top text-end">
                                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelModal" data-sub-id="{{ $whatsappSubscription->id }}">
                                                    <i class="ti ti-x me-1"></i> Cancel WhatsApp Plan
                                                </button>
                                            </div>
                                            @else 
                                            <div class="mt-4 p-3 bg-soft-warning rounded">
                                                <p class="mb-0 small text-dark">
                                                    <i class="ti ti-info-circle-filled me-1"></i> 
                                                    Your subscription was cancelled on <strong>{{ $whatsappSubscription->cancelled_at?->format('M d, Y') }}</strong>. 
                                                    You will continue to have access to premium features until <strong>{{ $whatsappSubscription->next_billing_at?->format('M d, Y') }}</strong>.
                                                </p>
                                                @if($whatsappSubscription->cancellation_reason)
                                                    <p class="mt-2 mb-0 small text-muted italic">"{{ $whatsappSubscription->cancellation_reason }}"</p>
                                                @endif
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>

                            @if($activeTopups->count() > 0)
                                <h6 class="text-muted small mb-3 uppercase fw-bold mt-4">Active Topup Plans</h6>
                                @foreach($activeTopups as $topup)
                                    <div class="active_plan_info bg-light p-3 rounded mb-3 border-start border-4 border-info shadow-sm">
                                        <div class="row align-items-center">
                                            <div class="col-md-6">
                                                <h6 class="fw-bold text-dark mb-1">{{ $topup->plan->name }} <span class="badge bg-soft-info text-info small">Topup</span></h6>
                                                <p class="mb-0 text-muted small">{{ $topup->plan->description }}</p>
                                            </div>
                                            <div class="col-md-6 text-md-end">
                                                <h5 class="fw-bold mb-0">
                                                    @if($topup->plan->currency == 'INR') ₹ @else $ @endif{{ number_format($topup->plan->price) }}
                                                    <sub class="fs-7 fw-normal">/{{ $topup->plan->type == 'monthly' ? 'mo' : 'yr' }}</sub>
                                                </h5>
                                                <p class="mb-0 small text-muted">Started: {{ $topup->starts_at?->format('M d, Y') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        @else
                            <div class="text-center py-5 bg-light rounded border border-dashed">
                                <div class="mb-3">
                                    <i class="ti ti-box" style="font-size: 3rem; color: #a1a1b5;"></i>
                                </div>
                                <h5 class="fw-bold text-dark">You don't have an active subscription</h5>
                                <p class="text-muted">Choose a plan below to unlock premium features and start managing your business.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div> <!-- col-lg-12 -->
        </div> <!-- row -->
            </div> <!-- End Account usage Tab -->
            
            <!-- Plan Usage Tab -->
            <div class="tab-pane fade" id="plan-usage" role="tabpanel">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="fw-bold mb-0">Plan Usage & Limits</h5>
                                    <span class="badge bg-soft-info text-info">Box ID: {{ Auth::user()->box_id }}</span>
                                </div>

                                <div class="usage-container bg-light p-4 rounded mb-4">
                                    <div class="row align-items-center mb-4">
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center">
                                                <div class="usage-icon bg-primary-soft p-3 rounded-circle me-3">
                                                    <i class="ti ti-users fs-3 text-primary"></i>
                                                </div>
                                                <div>
                                                    <h5 class="fw-bold mb-0">Users Usage</h5>
                                                    <p class="text-muted mb-0 small">Total users registered in your box</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 text-md-end">
                                            <h3 class="fw-bold mb-0">
                                                <span class="text-primary">{{ $usedUsersCount }}</span> 
                                                <span class="text-muted fs-5">/ {{ $totalUsersCapacity > 0 ? $totalUsersCapacity : 'Unlimited' }}</span>
                                            </h3>
                                        </div>
                                    </div>

                                    @php 
                                        $userPercent = $totalUsersCapacity > 0 ? min(100, ($usedUsersCount / $totalUsersCapacity) * 100) : 0;
                                        $progressClass = $userPercent > 90 ? 'bg-danger' : ($userPercent > 70 ? 'bg-warning' : 'bg-primary');
                                    @endphp
                                    
                                    <div class="progress rounded-pill mb-2" style="height: 12px;">
                                        <div class="progress-bar {{ $progressClass }}" role="progressbar" style="width: {{ $userPercent }}%" aria-valuenow="{{ $userPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between">
                                        <p class="text-muted small mb-0">{{ number_format($userPercent, 1) }}% Capacity Used</p>
                                        <p class="text-muted small mb-0">{{ $totalUsersCapacity > 0 ? ($totalUsersCapacity - $usedUsersCount) . ' seats remaining' : 'Infinite seats' }}</p>
                                    </div>
                                </div>

                                @if($whatsappSubscription)
                                <div class="usage-container bg-light p-4 rounded mb-4">
                                    <div class="row align-items-center mb-4">
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center">
                                                <div class="usage-icon bg-success-soft p-3 rounded-circle me-3">
                                                    <i class="ti ti-brand-whatsapp fs-3 text-success"></i>
                                                </div>
                                                <div>
                                                    <h5 class="fw-bold mb-0">WhatsApp Agents Usage</h5>
                                                    <p class="text-muted mb-0 small">Users allowed to act as WhatsApp Agents</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 text-md-end">
                                            <h3 class="fw-bold mb-0">
                                                <span class="text-success">{{ $usedWhatsAppAgentsCount }}</span> 
                                                <span class="text-muted fs-5">/ {{ $whatsappAgentLimit > 0 ? $whatsappAgentLimit : 'Unlimited' }}</span>
                                            </h3>
                                        </div>
                                    </div>

                                    @php 
                                        $waAgentPercent = $whatsappAgentLimit > 0 ? min(100, ($usedWhatsAppAgentsCount / $whatsappAgentLimit) * 100) : 0;
                                        $waProgressClass = $waAgentPercent > 90 ? 'bg-danger' : ($waAgentPercent > 70 ? 'bg-warning' : 'bg-success');
                                    @endphp
                                    
                                    <div class="progress rounded-pill mb-2" style="height: 12px;">
                                        <div class="progress-bar {{ $waProgressClass }}" role="progressbar" style="width: {{ $waAgentPercent }}%" aria-valuenow="{{ $waAgentPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between">
                                        <p class="text-muted small mb-0">{{ number_format($waAgentPercent, 1) }}% Capacity Used</p>
                                        <p class="text-muted small mb-0">{{ $whatsappAgentLimit > 0 ? ($whatsappAgentLimit - $usedWhatsAppAgentsCount) . ' seats remaining' : 'Infinite seats' }}</p>
                                    </div>
                                    
                                    <hr class="my-3">

                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <h6 class="fw-bold mb-0">WhatsApp Messages Usage</h6>
                                        </div>
                                        <div class="col-md-6 text-md-end">
                                            <h5 class="fw-bold mb-0">
                                                <span class="text-success">{{ number_format($usedWhatsappMessagesCount) }}</span> 
                                                <span class="text-muted fs-6">/ Unlimited</span>
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($totalUsersCapacity > 0 && $usedUsersCount >= $totalUsersCapacity)
                                    <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center">
                                        <i class="ti ti-alert-triangle-filled fs-4 me-3"></i>
                                        <div>
                                            <h6 class="fw-bold mb-1">User Limit Reached!</h6>
                                            <p class="mb-0 small">You have used all available user seats. Please <a href="#upgrade-plan" class="fw-bold text-decoration-none" onclick="$('#upgrade-plan-tab').click()">upgrade your plan</a> to add more users.</p>
                                        </div>
                                    </div>
                                @endif

                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <div class="card border border-light shadow-none">
                                            <div class="card-body p-3">
                                                <h6 class="text-muted small mb-2 uppercase fw-bold">Current Plan</h6>
                                                <p class="fw-bold mb-0 text-dark">
                                                    {{ $subscription->plan->name ?? 'No active plan' }}
                                                    @if($subscription && $subscription->status == 'cancelled')
                                                        <span class="text-danger small">(Cancelled)</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border border-light shadow-none">
                                            <div class="card-body p-3">
                                                <h6 class="text-muted small mb-2 uppercase fw-bold">Company Name</h6>
                                                <p class="fw-bold mb-0 text-dark">{{ $company->name ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- End Plan Usage Tab -->

            <div class="tab-pane fade" id="upgrade-plan" role="tabpanel">
        <div class="row mt-4 mb-3">
            <div class="col-12 text-center">
                <h3 class="fw-bold text-dark">{{ $subscription ? 'Upgrade or Change Plan' : 'Choose a Plan' }}</h3>
                <p class="text-muted">Select the best plan that fits your business needs.</p>
            </div>
        </div>

        <div class="d-flex justify-content-center mb-4">
            <ul class="nav nav-pills pricing-pills p-1 bg-light rounded-pill" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active rounded-pill px-4" id="monthly-tab" data-bs-toggle="tab" href="#monthly" role="tab">Monthly</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-pill px-4" id="yearly-tab" data-bs-toggle="tab" href="#yearly" role="tab">Yearly</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-pill px-4" id="topup-tab" data-bs-toggle="tab" href="#topup" role="tab">Topups</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-pill px-4" id="whatsapp-tab" data-bs-toggle="tab" href="#whatsapp" role="tab">WhatsApp Plans</a>
                </li>
            </ul>
        </div>

        <div class="tab-content price_content">
            <!-- Monthly -->
            <div class="tab-pane fade show active" id="monthly" role="tabpanel">
                <div class="row justify-content-center mb-5">
                    @foreach($monthlyPlans as $plan)
                    <div class="col-lg-4 col-md-6">
                        <div class="price_item_card {{ ($subscription && $subscription->plan_id == $plan->id) ? 'active' : '' }}">
                             @if($subscription && $subscription->plan_id == $plan->id)
                                @if($subscription->status == 'cancelled')
                                    <div class="active-tag bg-warning text-dark">Cancelled</div>
                                @else
                                    <div class="active-tag">Current Plan</div>
                                @endif
                            @elseif($plan->trial_period_days > 0)
                                <div class="active-tag bg-success text-white" style="background-color: #28a745 !important;">{{ $plan->trial_period_days }}-Day Trial</div>
                            @endif
                            <h5 class="fw-bold text-primary mb-3">{{ $plan->name }}</h5>
                            <p class="text-muted small mb-4">{{ $plan->description }}</p>
                            
                            @if($plan->trial_period_days > 0)
                                <div class="badge bg-soft-success text-success mb-3 p-2 w-100">
                                    <i class="ti ti-gift me-1"></i> {{ $plan->trial_period_days }} Days Free Trial
                                </div>
                            @endif

                            <div class="price-value mb-4">
                                <span class="h2 fw-bold mb-0">@if($plan->currency == 'INR') ₹ @else $ @endif{{ number_format($plan->price) }}</span>
                                <span class="text-muted">/mo</span>
                            </div>
                            <ul class="list-unstyled feature-list text-start mb-4">
                                @php $count = 0; @endphp
                                @foreach($plan->features ?? [] as $fName => $val)
                                    @if($val && $val !== '❌' && $count < 6)
                                        <li class="mb-2"><i class="ti ti-check-circle text-success me-2"></i> {{ $fName }}</li>
                                        @php $count++; @endphp
                                    @endif
                                @endforeach
                            </ul>
                            @if($subscription && $subscription->plan_id == $plan->id)
                                @if($subscription->status == 'cancelled')
                                    <button class="btn btn-warning w-100 rounded-pill disabled" disabled>Plan Cancelled</button>
                                @else
                                    <button class="btn btn-secondary w-100 rounded-pill disabled" disabled>Currently Active</button>
                                @endif
                            @else
                                <a href="{{ box_route('checkout.subscribe', ['planId' => $plan->id]) }}" class="btn btn-danger w-100 rounded-pill">
                                    @if($plan->trial_period_days > 0)
                                        Start {{ $plan->trial_period_days }}-Day Free Trial
                                    @else
                                        Subscribe Now
                                    @endif
                                </a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Yearly -->
            <div class="tab-pane fade" id="yearly" role="tabpanel">
                <div class="row justify-content-center mb-5">
                    @foreach($yearlyPlans as $plan)
                    <div class="col-lg-4 col-md-6">
                        <div class="price_item_card {{ ($subscription && $subscription->plan_id == $plan->id) ? 'active' : '' }}">
                             @if($subscription && $subscription->plan_id == $plan->id)
                                @if($subscription->status == 'cancelled')
                                    <div class="active-tag bg-warning text-dark">Cancelled</div>
                                @else
                                    <div class="active-tag">Current Plan</div>
                                @endif
                            @elseif($plan->trial_period_days > 0)
                                <div class="active-tag bg-success text-white" style="background-color: #28a745 !important;">{{ $plan->trial_period_days }}-Day Trial</div>
                            @endif
                            <h5 class="fw-bold text-primary mb-3">{{ $plan->name }}</h5>
                            <p class="text-muted small mb-4">{{ $plan->description }}</p>

                            @if($plan->trial_period_days > 0)
                                <div class="badge bg-soft-success text-success mb-3 p-2 w-100">
                                    <i class="ti ti-gift me-1"></i> {{ $plan->trial_period_days }} Days Free Trial
                                </div>
                            @endif

                            <div class="price-value mb-4">
                                <span class="h2 fw-bold mb-0">@if($plan->currency == 'INR') ₹ @else $ @endif{{ number_format($plan->price) }}</span>
                                <span class="text-muted">/yr</span>
                            </div>
                            <ul class="list-unstyled feature-list text-start mb-4">
                                @php $count = 0; @endphp
                                @foreach($plan->features ?? [] as $fName => $val)
                                    @if($val && $val !== '❌' && $count < 6)
                                        <li class="mb-2"><i class="ti ti-check-circle text-success me-2"></i> {{ $fName }}</li>
                                        @php $count++; @endphp
                                    @endif
                                @endforeach
                            </ul>
                            @if($subscription && $subscription->plan_id == $plan->id)
                                @if($subscription->status == 'cancelled')
                                    <button class="btn btn-warning w-100 rounded-pill disabled" disabled>Plan Cancelled</button>
                                @else
                                    <button class="btn btn-secondary w-100 rounded-pill disabled" disabled>Currently Active</button>
                                @endif
                            @else
                                <a href="{{ box_route('checkout.subscribe', ['planId' => $plan->id]) }}" class="btn btn-danger w-100 rounded-pill">
                                    @if($plan->trial_period_days > 0)
                                        Start {{ $plan->trial_period_days }}-Day Free Trial
                                    @else
                                        Subscribe Now
                                    @endif
                                </a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div> <!-- End price_content -->

            <!-- Topup -->
            <div class="tab-pane fade" id="topup" role="tabpanel">
                <div class="row justify-content-center mb-5">
                    @forelse($topupPlans as $plan)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="price_item_card">
                            <h5 class="fw-bold text-primary mb-3">{{ $plan->name }}</h5>
                            <p class="text-muted small mb-4">{{ $plan->description }}</p>
                            <div class="price-value mb-4">
                                <span class="h2 fw-bold mb-0">@if($plan->currency == 'INR') ₹ @else $ @endif{{ number_format($plan->price) }}</span>
                                <span class="text-muted">/{{ $plan->type == 'monthly' ? 'mo' : 'yr' }}</span>
                            </div>
                            <ul class="list-unstyled feature-list text-start mb-4">
                                @php $count = 0; @endphp
                                @foreach($plan->features ?? [] as $fName => $val)
                                    @if($val && $val !== '❌' && $count < 6)
                                        <li class="mb-2"><i class="ti ti-check-circle text-success me-2"></i> {{ $fName }}</li>
                                        @php $count++; @endphp
                                    @endif
                                @endforeach
                            </ul>
                            @if($subscription && $subscription->status !== 'cancelled')
                                <a href="{{ box_route('checkout.subscribe', ['planId' => $plan->id]) }}" class="btn btn-danger w-100 rounded-pill">Buy Topup</a>
                            @else
                                <button class="btn btn-secondary w-100 rounded-pill disabled" disabled title="You need an active subscription first">Requires Active Subscription</button>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center text-muted">
                        No topup plans available at the moment.
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- WhatsApp Plans -->
            <div class="tab-pane fade" id="whatsapp" role="tabpanel">
                <div class="row justify-content-center mt-5 mb-5">
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
                                        <span class="original-price">@if($plan->currency == 'INR') ₹ @else $ @endif{{ number_format($plan->original_price, 0) }}</span>
                                    @endif
                                    <span class="current-price">
                                        @if($plan->is_highlighted)
                                            <span style="color:#ff9800;">@if($plan->currency == 'INR') ₹ @else $ @endif{{ number_format($plan->price, 0) }}</span>
                                        @else
                                            <span style="color:#28a745;">@if($plan->currency == 'INR') ₹ @else $ @endif{{ number_format($plan->price, 0) }}</span>
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

                                @if($whatsappSubscription && $whatsappSubscription->plan_id == $plan->id)
                                    @if($whatsappSubscription->status == 'cancelled')
                                        <button class="btn btn-warning w-100 rounded-pill disabled" disabled>Plan Cancelled</button>
                                    @else
                                        <button class="btn btn-secondary w-100 rounded-pill disabled" disabled>Currently Active</button>
                                    @endif
                                @else
                                    <a href="{{ box_route('checkout.subscribe', ['planId' => $plan->id]) }}" class="btn-offer w-100 d-block text-center mt-3 text-decoration-none">
                                        {{ $plan->button_text ?? 'Subscribe Now' }}
                                    </a>
                                @endif

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

        <!-- Detailed Feature Comparison -->
        <div class="row mt-5">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="fw-bold mb-0">Detailed Feature Comparison</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table comparison-table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Feature</th>
                                        @foreach($monthlyPlans as $plan)
                                            <th class="text-center">{{ $plan->name }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($masterFeatures as $feature)
                                    <tr>
                                        <td class="ps-4 fw-medium">{{ $feature->name }}</td>
                                        @foreach($monthlyPlans as $p)
                                            <td class="text-center">
                                                @php $val = $p->features[$feature->name] ?? '❌'; @endphp
                                                @if($val == '✅' || $val == 'checked' || $val == 'yes') 
                                                    <i class="ti ti-circle-check-filled text-success fs-5"></i>
                                                @elseif($val == '❌' || $val == 'no') 
                                                    <i class="ti ti-circle-x-filled text-danger fs-5"></i>
                                                @else 
                                                    <span class="badge bg-soft-info text-info">{{ $val }}</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td class="ps-4 fw-medium">Free Trial Period</td>
                                        @foreach($monthlyPlans as $p)
                                            <td class="text-center">
                                                @if($p->trial_period_days > 0)
                                                    <span class="badge bg-soft-success text-success fw-bold">{{ $p->trial_period_days }} Days Free</span>
                                                @else
                                                    <i class="ti ti-circle-x-filled text-danger fs-5"></i>
                                                <span class="text-muted smaller d-block">No Trial</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div> <!-- End table-responsive -->
                    </div> <!-- End card-body -->
                </div> <!-- End card -->
            </div> <!-- End col-lg-12 -->
                </div> <!-- End row mt-5 -->
            </div> <!-- End Upgrade Plan Tab -->

            <!-- Payment Methods Tab -->
            <div class="tab-pane fade" id="payment-methods" role="tabpanel">
                <div class="row">
                    <div class="col-12 px-2">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold mb-0">Payment methods</h5>
                            <button class="btn btn-dark rounded-pill">Add a new payment method</button>
                        </div>
                        <div class="card border border-light shadow-sm rounded-3">
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Type</th>
                                            <th>Details</th>
                                            <th>Status</th>
                                            <th>Expires on</th>
                                        </tr>
                                    </thead>
                                    <tbody id="payment-methods-tbody">
                                        @if($razorpaySubscription && isset($razorpaySubscription['payment_method']))
                                            @php $method = $razorpaySubscription['payment_method']; @endphp
                                            <tr>
                                                <td>
                                                    @if(strtolower($method) === 'upi')
                                                        <i class="ti ti-device-mobile fs-3 text-secondary"></i>
                                                    @else
                                                        <i class="ti ti-credit-card fs-3 text-secondary"></i>
                                                    @endif
                                                    <span class="badge bg-dark ms-2 text-uppercase">{{ $method }}</span>
                                                </td>
                                                <td class="text-uppercase">{{ $paymentDetails ?? ('Auto-debit via ' . strtoupper($method)) }}</td>
                                                <td><span class="text-success"><i class="ti ti-point-filled"></i> Active</span></td>
                                                <td>-</td>
                                            </tr>
                                        @else
                                            <tr><td colspan="4" class="text-center text-muted py-4">No saved payment methods</td></tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- End Payment Methods Tab -->

            <!-- Billing Information Tab -->
            <div class="tab-pane fade" id="billing-information" role="tabpanel">
                <div class="row">
                    <div class="col-lg-8 px-2">
                        <p class="text-muted small mb-4">Ensure that your billing address is accurate, as it will be used on invoices and cannot be modified after an invoice is generated. Billing information may differ from your account's profile information.</p>
                        <form>
                            @php
                                $names = explode(' ', $user->name, 2);
                                $firstName = $names[0] ?? '';
                                $lastName = $names[1] ?? '';
                            @endphp
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">First name</label>
                                    <input type="text" class="form-control" name="first_name" value="{{ $firstName }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Last name</label>
                                    <input type="text" class="form-control" name="last_name" value="{{ $lastName }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Company</label>
                                <input type="text" class="form-control" name="company_name" value="{{ $company->bill_name ?? $box->name ?? '' }}">
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-10">
                                    <label class="form-label fw-bold">Address</label>
                                    <input type="text" class="form-control" name="address" value="{{ $company->bill_address ?? $box->address ?? '' }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-bold">Postal code</label>
                                    <input type="text" class="form-control" name="postal_code" value="{{ $company->bill_pin_code ?? $box->pincode ?? '' }}">
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">City</label>
                                    <input type="text" class="form-control" name="city" value="{{ $company->city->name ?? $box->city->name ?? '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Country <span class="text-danger">*</span></label>
                                    <select class="form-select" name="country_id">
                                        <option selected>{{ $box->country->name ?? 'India' }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold d-block">GSTIN <span class="text-danger">*</span></label>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="gstinOptions" id="hasGstin" {{ !empty($company->gstin) ? 'checked' : '' }}>
                                    <label class="form-check-label text-dark" for="hasGstin">I have a GSTIN number</label>
                                </div>
                                <div class="ms-4 mb-3">
                                    <label class="form-label small text-muted">GSTIN number</label>
                                    <input type="text" class="form-control w-50" name="gstin" value="{{ $company->gstin ?? '' }}">
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gstinOptions" id="noGstin" {{ empty($company->gstin) ? 'checked' : '' }}>
                                    <label class="form-check-label text-muted" for="noGstin">I don't have GSTIN number</label>
                                </div>
                            </div>

                            <h5 class="fw-bold mb-3 mt-5">Billing notifications</h5>
                            <div class="mb-4">
                                <label class="form-label fw-bold">Invoice recipient Email</label>
                                <input type="email" class="form-control w-50" name="invoice_email" value="{{ $box->company_email ?? $user->email }}">
                            </div>
                            
                            <h5 class="fw-bold mb-3 mt-4">Billing notifications</h5>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" checked>
                                <label class="form-check-label fw-bold">Do you want to receive your invoices by email when you renew your plan?</label>
                            </div>
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox">
                                <label class="form-check-label text-muted">Receive an email when your sms credit falls below the threshold you set.</label>
                            </div>

                            <button type="button" class="btn btn-dark rounded-pill px-4 py-2 mt-2">Confirm billing information</button>
                        </form>
                    </div>
                </div>
            </div> <!-- End Billing Information Tab -->

            <!-- Invoices Tab -->
            <div class="tab-pane fade" id="invoices" role="tabpanel">
                <div class="card border border-light shadow-sm rounded-3">
                    <div class="table-responsive">
                        <table class="table mb-0 table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Invoice ID</th>
                                    <th>Plan</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="invoices-tbody">
                                @forelse($invoices as $inv)
                                    <tr>
                                        <td class="fw-bold text-dark">{{ $inv['id'] ?? '-' }}</td>
                                        <td>{{ $subscription->plan->name ?? '-' }}</td>
                                        <td>{{ isset($inv['created_at']) ? date('m/d/Y', $inv['created_at']) : '-' }}</td>
                                        <td>{{ isset($inv['currency']) && $inv['currency'] !== 'INR' ? '$' : '₹' }}{{ number_format(($inv['amount'] ?? 0) / 100, 2) }}</td>
                                        <td>
                                            @if(($inv['status'] ?? '') === 'paid')
                                                <span class="text-success"><i class="ti ti-point-filled"></i> Paid</span>
                                            @elseif(($inv['status'] ?? '') === 'issued')
                                                <span class="text-warning"><i class="ti ti-point-filled"></i> Issued</span>
                                            @else
                                                <span class="text-muted"><i class="ti ti-point-filled"></i> {{ ucfirst($inv['status'] ?? '') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($inv['short_url']))
                                                <a href="{{ $inv['short_url'] }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="ti ti-external-link"></i> View</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">No payment history found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> <!-- End Invoices Tab -->

            <!-- Credit Notes Tab -->
            <div class="tab-pane fade" id="credit-notes" role="tabpanel">
                <div class="card border border-light shadow-sm rounded-3">
                    <div class="table-responsive">
                        <table class="table mb-0 table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="refunds-tbody">
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <div class="spinner-border text-primary mb-2" role="status"></div>
                                        <div>Loading credit notes...</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> <!-- End Credit Notes Tab -->
        </div> <!-- End of Master Tab Content -->

<!-- Cancellation Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-soft-danger border-0">
                <h5 class="modal-title fw-bold text-danger">Cancel Subscription</h5>
                <button type="button" class="btn-close" data-bs-target="#cancelModal" data-bs-toggle="modal" aria-label="Close"></button>
            </div>
            <form id="cancelSubscriptionForm" action="{{ box_route('subscription.cancel') }}" method="POST">
                @csrf
                <input type="hidden" name="subscription_id" id="cancel_subscription_id" value="">
                <div class="modal-body p-4">
                    <p class="text-muted">We're sorry to see you go! Please tell us why you want to cancel your subscription so we can improve our service.</p>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Reason for Cancellation <span class="text-danger">*</span></label>
                        <select class="form-select" name="reason" required>
                            <option value="" disabled selected>Select a reason</option>
                            <option value="Too expensive">Too expensive</option>
                            <option value="Missing features">Missing features</option>
                            <option value="Technical issues">Technical issues</option>
                            <option value="Poor support">Poor support</option>
                            <option value="Switching to a competitor">Switching to a competitor</option>
                            <option value="I no longer need it">I no longer need it</option>
                            <option value="Other">Other (Please specify below)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <textarea class="form-control" name="other_reason" rows="3" placeholder="Additional feedback (optional)"></textarea>
                    </div>
                    <div class="alert alert-info mt-3 mb-0 small border-0">
                        <i class="ti ti-info-circle-filled me-1"></i> 
                        Your subscription will remain <strong>Active</strong> until the end of the current billing cycle.
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Keep Subscription</button>
                    <button type="submit" id="confirmCancelBtn" class="btn btn-danger rounded-pill px-4">
                        <span class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
                        Confirm Cancellation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
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
        color: white;
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
        color: white;
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
        margin-bottom: 10px;
    }
    .feature-list li i {
        color: #28a745;
        margin-right: 10px;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var cancelModal = document.getElementById('cancelModal');
        if(cancelModal) {
            cancelModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                if(button) {
                    var subId = button.getAttribute('data-sub-id');
                    document.getElementById('cancel_subscription_id').value = subId || '';
                }
            });
        }
    });

    // Fetch invoices and refunds asynchronously
    fetch('{{ route("subscription.invoices_refunds", ["box_slug" => request()->route("box_slug") ?? request("box_slug") ?? session("box_slug")]) }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const invTbody = document.getElementById('invoices-tbody');
                const refTbody = document.getElementById('refunds-tbody');
                if (invTbody) invTbody.innerHTML = data.invoices_html;
                if (refTbody) refTbody.innerHTML = data.refunds_html;
            }
        })
        .catch(err => {
            console.error('Failed to fetch invoices:', err);
            const invTbody = document.getElementById('invoices-tbody');
            if (invTbody) invTbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger py-4">Failed to load invoices. Please refresh the page.</td></tr>';
        });
</script>
