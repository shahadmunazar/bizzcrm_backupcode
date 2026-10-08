<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{UserSubscription, BoxUser, Plan, Box, Coupon};
use App\Jobs\SendSubscriptionConfirmationJob;
use App\Services\RazorpayService;
use Illuminate\Support\Facades\{Auth, Log};
use Carbon\Carbon;

class CheckoutController extends Controller
{
    protected $razorpayService;

    public function __construct(RazorpayService $razorpayService)
    {
        $this->razorpayService = $razorpayService;
    }

    public function subscribeGlobal(Request $request, $planId)
    {
        $box_slug = Auth::check() && Auth::user()->box_id ? (\App\Models\Box::find(Auth::user()->box_id)->slug ?? 'default') : 'default';
        return $this->subscribe($request, $box_slug, $planId);
    }

    public function applyCouponGlobal(Request $request, $planId)
    {
        $box_slug = Auth::check() && Auth::user()->box_id ? (\App\Models\Box::find(Auth::user()->box_id)->slug ?? 'default') : 'default';
        return $this->applyCoupon($request, $box_slug, $planId);
    }

    public function processPaymentGlobal(Request $request, $planId)
    {
        $box_slug = Auth::check() && Auth::user()->box_id ? (\App\Models\Box::find(Auth::user()->box_id)->slug ?? 'default') : 'default';
        return $this->processPayment($request, $box_slug, $planId);
    }

    public function handleSuccessGlobal(Request $request) 
    {
        $box_slug = Auth::check() && Auth::user()->box_id ? (\App\Models\Box::find(Auth::user()->box_id)->slug ?? 'default') : 'default';
        return $this->handleSuccess($request, $box_slug);
    }

    public function subscribe(Request $request, $box_slug, $planId)
    {
        $plan = Plan::findOrFail($planId);
        if (!$plan->razorpay_plan_id) {
            return back()->with('error', 'This plan is not currently available for subscription.');
        }
        if (!Auth::check()) {
            return redirect()->route('login')->with('info', 'Please login to subscribe to a plan.');
        }
        $user = Auth::user();

        // Enforce topup rules: Must have an active base subscription to buy a topup plan
        if ($plan->plan_type === 'topup') {
            $activeSub = UserSubscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->whereHas('plan', function($q) {
                    $q->where('plan_type', '!=', 'topup');
                })->first();
            if (!$activeSub) {
                return back()->with('error', 'You must have an active subscription plan before purchasing a top-up.');
            }
        }

        return view('frontend.checkout.summary', [
            'plan' => $plan,
            'user' => $user,
            'box_slug' => $box_slug
        ]);
    }

    public function applyCoupon(Request $request, $box_slug, $planId)
    {
        $request->validate([
            'code' => 'required|string'
        ]);

        $plan = Plan::findOrFail($planId);
        $coupon = Coupon::where('code', $request->code)->where('status', 'active')->first();

        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Invalid or inactive coupon code.']);
        }

        if ($coupon->valid_until && Carbon::parse($coupon->valid_until)->isPast()) {
            return response()->json(['success' => false, 'message' => 'This coupon has expired.']);
        }

        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            return response()->json(['success' => false, 'message' => 'This coupon usage limit has been reached.']);
        }

        if ($coupon->user_type === 'new') {
            $hasSubscription = UserSubscription::where('user_id', Auth::id())->exists();
            if ($hasSubscription) {
                return response()->json(['success' => false, 'message' => 'This coupon is only valid for new users.']);
            }
        }

        $discount = 0;
        if ($coupon->type === 'percentage') {
            $discount = ($plan->price * $coupon->value) / 100;
        } else {
            $discount = $coupon->value;
        }

        $newPrice = max(0, $plan->price - $discount);

        return response()->json([
            'success' => true,
            'discount' => round($discount, 2),
            'new_price' => round($newPrice, 2),
            'message' => 'Coupon applied successfully!'
        ]);
    }

    public function processPayment(Request $request, $box_slug, $planId)
    {
        $plan = Plan::findOrFail($planId);
        $user = Auth::user();
        $box = Box::where('slug', $box_slug)->first();
        $boxId = $box ? $box->id : null;
        $boxUserId = null;

        if ($user && $boxId) {
            $boxUser = BoxUser::where('user_id', $user->id)->where('box_id', $boxId)->first();
            $boxUserId = $boxUser ? $boxUser->id : null;
        }

        $discount = 0;
        $couponApplied = false;

        if ($request->has('coupon_code') && !empty($request->coupon_code)) {
            $coupon = Coupon::where('code', $request->coupon_code)->where('status', 'active')->first();
            if ($coupon) {
                // Increment used count
                $coupon->increment('used_count');
                $couponApplied = true;
                if ($coupon->type === 'percentage') {
                    $discount = ($plan->price * $coupon->value) / 100;
                } else {
                    $discount = $coupon->value;
                }
            }
        }

        $firstPaymentAmount = max(0, $plan->price - $discount);

        try {
            $api = new \Razorpay\Api\Api(config('services.razorpay.key'), config('services.razorpay.secret'));
            
            Log::info("Attempting to create subscription/order for user {$user->id} on plan {$plan->id}");

            if ($couponApplied && $discount > 0) {
                // If full discount or partial, we can create a one-time order for first payment,
                // and a delayed subscription. Or just create a delayed subscription with a 1 Rs verification if 100% discount.
                
                // For BizzCRM, we will create a Subscription with start_at delayed by 1 cycle, and an upfront Add-on for the discounted price.
                // Because Razorpay requires a positive upfront amount to authorize.
                // Note: If 100% free, we charge 1 Rs verification fee.
                
                $upfrontAmount = $firstPaymentAmount > 0 ? $firstPaymentAmount : 1; 
                // in paise
                $upfrontPaise = round($upfrontAmount * 100);

                $subData = [
                    'plan_id' => $plan->razorpay_plan_id,
                    'customer_notify' => 1,
                    'total_count' => $plan->type === 'monthly' ? 120 : 10,
                    'start_at' => $plan->type === 'monthly' ? Carbon::now()->addMonth()->timestamp : Carbon::now()->addYear()->timestamp,
                    'addons' => [
                        [
                            'item' => [
                                'name' => 'First Cycle Payment',
                                'amount' => $upfrontPaise,
                                'currency' => $plan->currency ?: 'INR',
                                'description' => 'Discounted first cycle payment'
                            ]
                        ]
                    ],
                    'notes' => [
                        'user_id' => $user->id,
                        'plan_id' => $plan->id,
                        'box_id' => $boxId ?? '',
                        'box_user_id' => $boxUserId ?? '',
                        'coupon_applied' => $request->coupon_code ?? ''
                    ]
                ];

                Log::info("Delayed Subscription with Addon data: " . json_encode($subData));
                $subscription = $api->subscription->create($subData);
                Log::info("Subscription created: " . $subscription->id);

                return view('frontend.checkout.payment', [
                    'subscription_id' => $subscription->id,
                    'razorpay_key' => config('services.razorpay.key'),
                    'plan' => $plan,
                    'user' => $user,
                    'box_slug' => $box_slug,
                    'amount' => $upfrontAmount
                ]);

            } else {
                // Normal Subscription Logic
                $subData = [
                    'plan_id' => $plan->razorpay_plan_id,
                    'customer_notify' => 1,
                    'total_count' => $plan->type === 'monthly' ? 120 : 10,
                    'notes' => [
                        'user_id' => $user->id,
                        'plan_id' => $plan->id,
                        'box_id' => $boxId ?? '',
                        'box_user_id' => $boxUserId ?? ''
                    ]
                ];

                if ($plan->trial_period_days > 0) {
                    $subData['start_at'] = Carbon::now()->addDays($plan->trial_period_days)->timestamp;
                    $subData['addons'] = [
                        [
                            'item' => [
                                'name' => 'Trial Verification Fee',
                                'amount' => 100, 
                                'currency' => $plan->currency ?: 'INR',
                                'description' => 'One-time fee to verify your payment method for the trial'
                            ]
                        ]
                    ];
                }

                Log::info("Subscription data: " . json_encode($subData));
                $subscription = $api->subscription->create($subData);
                Log::info("Subscription created: " . $subscription->id);

                return view('frontend.checkout.payment', [
                    'subscription_id' => $subscription->id,
                    'razorpay_key' => config('services.razorpay.key'),
                    'plan' => $plan,
                    'user' => $user,
                    'box_slug' => $box_slug,
                    'amount' => $plan->price
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Razorpay Error: ' . $e->getMessage());
            return back()->with('error', 'Razorpay Error: ' . $e->getMessage());
        }
    }

    public function handleSuccess(Request $request, $box_slug)
    {
        $api = new \Razorpay\Api\Api(config('services.razorpay.key'), config('services.razorpay.secret'));   
        try {
            $attributes = [
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_subscription_id' => $request->razorpay_subscription_id,
                'razorpay_signature' => $request->razorpay_signature
            ];

            $api->utility->verifyPaymentSignature($attributes);
            
            $subscriptionData = $api->subscription->fetch($request->razorpay_subscription_id);
            $data = $subscriptionData->toArray();
            
            $planId = null;
            $userId = Auth::id();
            $boxId = null;
            $boxUserId = null;

            if (!empty($data['notes']['plan_id'])) {
                $planId = $data['notes']['plan_id'];
            }

            if (!$userId && !empty($data['notes']['user_id'])) {
                $userId = $data['notes']['user_id'];
            }
            if (!$planId && !empty($data['plan_id'])) {
                $plan = Plan::where('razorpay_plan_id', $data['plan_id'])->first();
                $planId = $plan?->id;
            }

            if (!empty($data['notes']['box_id'])) {
                $boxId = $data['notes']['box_id'];
            } else {
                $box = \App\Models\Box::where('slug', $box_slug)->first();
                $boxId = $box ? $box->id : null;
            }

            if (!empty($data['notes']['box_user_id'])) {
                $boxUserId = $data['notes']['box_user_id'];
            } elseif ($userId && $boxId) {
                $boxUser = \App\Models\BoxUser::where('user_id', $userId)->where('box_id', $boxId)->first();
                $boxUserId = $boxUser ? $boxUser->id : null;
            }

            if (!$userId || !$planId) {
                throw new \Exception("Missing required data: User ID ($userId) or Plan ID ($planId)");
            }

            $userSub = UserSubscription::updateOrCreate(
                ['razorpay_subscription_id' => $request->razorpay_subscription_id],
                [
                    'user_id' => $userId,
                    'box_id' => $boxId,
                    'box_user_id' => $boxUserId,
                    'plan_id' => $planId,
                    'razorpay_plan_id' => $data['plan_id'] ?? null,
                    'status' => $data['status'] ?? 'created',
                    'starts_at' => !empty($data['start_at']) ? date('Y-m-d H:i:s', $data['start_at']) : null,
                    'ends_at' => !empty($data['end_at']) ? date('Y-m-d H:i:s', $data['end_at']) : null,
                    'trial_ends_at' => !empty($data['trial_end']) ? date('Y-m-d H:i:s', $data['trial_end']) : null,
                    'next_billing_at' => !empty($data['charge_at']) ? date('Y-m-d H:i:s', $data['charge_at']) : null,
                    'raw_data' => $data
                ]
            );

            // Log Vendor Commission
            if ($boxId) {
                $box = \App\Models\Box::find($boxId);
                if ($box && $box->referral_code) {
                    $vendor = \App\Models\Vendor::where('referral_code', $box->referral_code)->first();
                    if ($vendor) {
                        $vendorPercentage = \App\Models\VendorPercentage::where('vendor_id', $vendor->id)->first();
                        $percentage = $vendorPercentage ? $vendorPercentage->percentage : config('bizzcrm.default_commission_percentage', 10);
                        
                        $plan = \App\Models\Plan::find($planId);
                        $transactionAmount = $plan ? $plan->price : 0;
                        $commissionAmount = $transactionAmount * ($percentage / 100);

                        \App\Models\VendorCommission::create([
                            'vendor_id' => $vendor->id,
                            'box_id' => $box->id,
                            'transaction_amount' => $transactionAmount,
                            'commission_percentage' => $percentage,
                            'commission_amount' => $commissionAmount,
                            'status' => 'pending',
                        ]);
                    }
                }
            }

           SendSubscriptionConfirmationJob::dispatch($userSub->id);
            return view('frontend.checkout.success');
        } catch (\Exception $e) {
            return redirect_route('subscription.index')->with('error', 'Payment verification failed: ' . $e->getMessage());
        }
    }
}
