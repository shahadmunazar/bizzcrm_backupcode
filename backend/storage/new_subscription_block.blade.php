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
                                            <i class="ti ti-box me-2 text-primary fs-4"></i> {{ $subscription->plan->name }} Plan
                                            <span class="ms-auto me-3">
                                                @if($subscription->status == 'cancelled')
                                                    <span class="badge bg-warning">Cancelled (Ends on {{ $subscription->next_billing_at?->format('M d, Y') }})</span>
                                                @elseif($subscription->starts_at && $subscription->starts_at->isFuture())
                                                    <span class="badge bg-info">On Trial</span>
                                                @else
                                                    <span class="badge bg-success">Active</span>
                                                @endif
                                            </span>
                                        </button>
                                    </h2>
                                    <div id="collapse_crm" class="accordion-collapse collapse show" aria-labelledby="heading_crm" data-bs-parent="#subscriptionAccordion">
                                        <div class="accordion-body p-4">
                                            <div class="active_plan_info bg-light p-3 rounded mb-4">
                                                <div class="row align-items-center">
                                                    <div class="col-md-6">
                                                        <h5 class="fw-bold text-dark mb-1">{{ $subscription->plan->name }} Plan</h5>
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
                                            <i class="ti ti-brand-whatsapp me-2 text-success fs-4"></i> {{ $whatsappSubscription->plan->name }} Plan
                                            <span class="ms-auto me-3">
                                                @if($whatsappSubscription->status == 'cancelled')
                                                    <span class="badge bg-warning">Cancelled (Ends on {{ $whatsappSubscription->next_billing_at?->format('M d, Y') }})</span>
                                                @else
                                                    <span class="badge bg-success">Active</span>
                                                @endif
                                            </span>
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
