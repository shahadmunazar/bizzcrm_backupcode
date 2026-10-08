<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Log};
use App\Models\{Plan, BoxUser,Box, PackageFeature, Company};
use Carbon\Carbon;
use Razorpay\Api\Api;
use App\Helpers\SubscriptionHelper;
use App\Jobs\{SendSubscriptionCancellationJob};

class SubscriptionController extends Controller
{
    public function index()
    {   
        return view('frontend.subscription.index');
    }

        public function getInvoicesAndRefunds()
    {
        $user = Auth::user();
        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
        $allSubscriptions = SubscriptionHelper::getAllSubscriptions($user);
        
        $invoicesArray = [];
        $creditNotes = [];

        try {
            foreach ($allSubscriptions as $sub) {
                if (!$sub->razorpay_subscription_id) continue;
                
                try {
                    $invoicesResult = $api->invoice->all(['subscription_id' => $sub->razorpay_subscription_id]);
                    $subInvoices = $invoicesResult['items'] ?? [];
                    
                    foreach ($subInvoices as $inv) {
                        $invArray = is_array($inv) ? $inv : $inv->toArray();
                        $invArray['plan_name'] = $sub->plan->name ?? '-';
                        $invoicesArray[] = $invArray;

                        if (!empty($invArray['payment_id'])) {
                            try {
                                $refundsResult = $api->refund->all(['payment_id' => $invArray['payment_id']]);
                                $refunds = $refundsResult['items'] ?? [];
                                foreach ($refunds as $rf) {
                                    $creditNotes[] = (object) [
                                        'note_id'    => $rf['id'],
                                        'type'       => 'Refund',
                                        'created_at' => \Carbon\Carbon::createFromTimestamp($rf['created_at']),
                                        'amount'     => ($rf['amount'] ?? 0) / 100,
                                        'currency'   => $rf['currency'] ?? 'INR',
                                        'status'     => 'Refunded',
                                        'plan_name'  => $sub->plan->name ?? '-'
                                    ];
                                }
                            } catch (\Exception $re) {
                                Log::error('Razorpay refunds fetch failed: ' . $re->getMessage());
                            }
                        }
                    }
                } catch (\Exception $ie) {
                    Log::error('Razorpay historical invoices fetch failed: ' . $ie->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error('Razorpay fetch failed: ' . $e->getMessage());
        }

        usort($invoicesArray, function($a, $b) {
            return ($b['created_at'] ?? 0) <=> ($a['created_at'] ?? 0);
        });
        usort($creditNotes, function($a, $b) {
            return $b->created_at->timestamp <=> $a->created_at->timestamp;
        });

        $invoicesHtml = view('frontend.subscription.partials.invoices_table', ['invoices' => $invoicesArray])->render();
        $refundsHtml = view('frontend.subscription.partials.refunds_table', ['creditNotes' => $creditNotes])->render();

        return response()->json([
            'success' => true,
            'invoices_html' => $invoicesHtml,
            'refunds_html' => $refundsHtml
        ]);
    }

    public function razorpayDetails()
    {
        $user = Auth::user();
        $subscription = SubscriptionHelper::getActiveSubscription($user);

        $razorpaySubscription = null;
        $invoicesArray = [];
        $paymentDetails = null;
        $creditNotes = [];

        try {
            $allSubscriptions = $user->subscriptions()->whereNotNull('razorpay_subscription_id')->get();
            $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

            // Synchronize status for ALL active subscriptions and top-ups
            $activeSubsForSync = SubscriptionHelper::getAllActiveSubscriptions($user);
            
            foreach ($activeSubsForSync as $subToSync) {
                if ($subToSync->razorpay_subscription_id) {
                    try {
                        $subscriptionData = $api->subscription->fetch($subToSync->razorpay_subscription_id);
                        $razorpaySub = $subscriptionData->toArray();
                        $updateData = [
                            'starts_at' => isset($razorpaySub['start_at']) ? date('Y-m-d H:i:s', $razorpaySub['start_at']) : (isset($razorpaySub['current_start']) ? date('Y-m-d H:i:s', $razorpaySub['current_start']) : $subToSync->starts_at),
                            'next_billing_at' => isset($razorpaySub['charge_at']) ? date('Y-m-d H:i:s', $razorpaySub['charge_at']) : (isset($razorpaySub['current_end']) ? date('Y-m-d H:i:s', $razorpaySub['current_end']) : $subToSync->next_billing_at),
                            'ends_at' => isset($razorpaySub['end_at']) ? date('Y-m-d H:i:s', $razorpaySub['end_at']) : (isset($razorpaySub['current_end']) ? date('Y-m-d H:i:s', $razorpaySub['current_end']) : $subToSync->ends_at),
                        ];
                        $razorpayStatus = $razorpaySub['status'] ?? $subToSync->status;
                        if ($subToSync->status !== 'cancelled' || $razorpayStatus === 'expired' || $razorpayStatus === 'terminated') {
                            $updateData['status'] = $razorpayStatus;
                        }
                        if (isset($updateData['status']) && $updateData['status'] === 'cancelled' && !$subToSync->cancelled_at) {
                            $updateData['cancelled_at'] = Carbon::now();
                        }
                        $subToSync->update($updateData);

                        if ($subscription && $subscription->id === $subToSync->id) {
                            $subscription = $subToSync->fresh(['plan']);
                            $razorpaySubscription = $razorpaySub; // Persist response for the base layout
                        }
                    } catch (\Exception $se) {
                        Log::error("Razorpay subscription fetch failed for {$subToSync->razorpay_subscription_id}: " . $se->getMessage());
                    }
                }
            }

            if ($subscription) {
                // Fetch invoices specifically for the ACTIVE subscription to find the payment method quickly
                try {
                    $invoicesResult = $api->invoice->all(['subscription_id' => $subscription->razorpay_subscription_id]);
                    $subInvoices = $invoicesResult['items'] ?? [];
                    
                    foreach ($subInvoices as $inv) {
                        $invArray = is_array($inv) ? $inv : $inv->toArray();
                        $invoicesArray[] = $invArray;
                        if (empty($paymentDetails) && !empty($invArray['payment_id'])) {
                            try {
                                $payment = $api->payment->fetch($invArray['payment_id'])->toArray();
                                if (isset($payment['method'])) {
                                    if ($payment['method'] === 'upi') {
                                        $paymentDetails = $payment['vpa'] ?? 'UPI';
                                    } elseif ($payment['method'] === 'card') {
                                        $card = $payment['card'] ?? [];
                                        $paymentDetails = ($card['network'] ?? 'Card') . ' ending in ' . ($card['last4'] ?? '****');
                                    } else {
                                        $paymentDetails = ucfirst($payment['method']);
                                    }
                                }
                            } catch (\Exception $pe) {
                                Log::error('Razorpay payment fetch failed: ' . $pe->getMessage());
                            }
                        }
                    }
                } catch (\Exception $ie) {
                    Log::error('Razorpay invoices fetch failed: ' . $ie->getMessage());
                }
            }


        } catch (\Exception $e) {
            Log::error('Razorpay fetch failed: ' . $e->getMessage());
        }

        $monthlyPlans = Plan::where('type', 'monthly')->where('plan_type', 'subscription')->where('status', 'active')->get();
        $yearlyPlans = Plan::where('type', 'yearly')->where('plan_type', 'subscription')->where('status', 'active')->get();
        $topupPlans = Plan::where('plan_type', 'topup')->where('status', 'active')->get();
        $whatsappPlans = Plan::where('plan_type', 'whatsapp')->where('status', 'active')->get();
        $whatsappSubscription = SubscriptionHelper::getActiveWhatsappSubscription($user);
        $masterFeatures = PackageFeature::where('is_active', true)->get();
        $boxSlug = request()->route('box_slug') ?? request('box_slug') ?? session('box_slug');
        $box = null;
        if ($boxSlug) {
            $box = Box::where('slug', $boxSlug)->first();
        }
        
        if (!$box) {
            $boxUserEntry = BoxUser::where('user_id', $user->id)->first();
            if ($boxUserEntry) {
                $box = \App\Models\Box::find($boxUserEntry->box_id);
            }
        }

        $company = null;
        if ($box) {
            $company = Company::where('box_id', $box->id)->first();
        }

        $paymentHistory = $user->subscriptions()->with('plan')->latest()->get();
        $paymentMethods = []; 
        usort($invoicesArray, function($a, $b) {
            return ($b['created_at'] ?? 0) <=> ($a['created_at'] ?? 0);
        });
        usort($creditNotes, function($a, $b) {
            return $b->created_at->timestamp <=> $a->created_at->timestamp;
        });
        $invoices = $invoicesArray;
        $totalUsersCapacity = 0;
        $usedUsersCount = $box ? BoxUser::where('box_id', $box->id)->count() : 0;
        
        $combinedFeatures = SubscriptionHelper::getCombinedFeatures($user);
        if (isset($combinedFeatures['No of Users'])) {
            $totalUsersCapacity = (int) $combinedFeatures['No of Users'];
        }

        $activeTopups = SubscriptionHelper::getAllActiveSubscriptions($user)
            ->filter(function($sub) {
                return $sub->plan && $sub->plan->plan_type === 'topup';
            });

        $usedWhatsappMessagesCount = $box ? \App\Models\WhatsAppMessage::where('box_id', $box->id)->count() : 0;
        
        $usedWhatsAppAgentsCount = $box ? BoxUser::where('box_id', $box->id)->where('has_whatsapp_access', 1)->count() : 0;
        $whatsappAgentLimit = SubscriptionHelper::getWhatsAppAgentLimit($user);

        $html = view('frontend.subscription.tabs_content', compact(
            'user', 'subscription', 'monthlyPlans', 'yearlyPlans', 
            'masterFeatures', 'paymentHistory', 'box', 'company', 
            'paymentMethods', 'creditNotes', 'razorpaySubscription', 'invoices', 
            'paymentDetails', 'totalUsersCapacity', 'usedUsersCount', 'topupPlans', 'activeTopups', 'whatsappPlans', 'whatsappSubscription', 'usedWhatsappMessagesCount', 'usedWhatsAppAgentsCount', 'whatsappAgentLimit'
        ))->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    public function cancel(Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $user = Auth::user();
        $subscriptions = SubscriptionHelper::getAllActiveSubscriptions($user);

        if ($subscriptions->isEmpty()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'You do not have an active subscription to cancel.']);
            }
            return back()->with('error', 'You do not have an active subscription to cancel.');
        }

        try {
            $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
            
            $cancelReason = $request->reason;
            if ($request->other_reason) {
                $cancelReason .= ' - ' . $request->other_reason;
            }

            $targetSubId = $request->subscription_id;
            foreach ($subscriptions as $subscription) {
                if ($targetSubId && $subscription->id != $targetSubId) {
                    continue; // Skip if it doesn't match the target subscription
                }
                
                if ($subscription->razorpay_subscription_id) {
                    try {
                        $rzpSubscription = $api->subscription->fetch($subscription->razorpay_subscription_id);
                        $rzpSubscription->cancel(['cancel_at_cycle_end' => 1]);
                    } catch (\Exception $e) {
                         Log::error('Razorpay Cancellation failed for sub ' . $subscription->id . ': ' . $e->getMessage());
                    }
                }

                $subscription->update([
                    'status' => 'cancelled',
                    'cancelled_at' => Carbon::now(),
                    'cancellation_reason' => $cancelReason,
                ]);
            }

            // Only dispatch one email using the primary or first subscription record
            SendSubscriptionCancellationJob::dispatch($subscriptions->first()->id, $cancelReason);

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Your subscription and any active topups have been scheduled for cancellation at the end of the current billing cycle.']);
            }
            return back()->with('success', 'Your subscription and any active topups have been scheduled for cancellation at the end of the current billing cycle.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Cancellation failed: ' . $e->getMessage()]);
            }
            return back()->with('error', 'Cancellation failed: ' . $e->getMessage());
        }
    }
}
