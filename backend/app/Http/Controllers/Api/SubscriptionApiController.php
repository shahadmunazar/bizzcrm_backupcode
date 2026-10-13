<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Plan;
use App\Models\BoxUser;
use App\Models\Box;
use App\Helpers\SubscriptionHelper;
use Razorpay\Api\Api;

class SubscriptionApiController extends Controller
{
    /**
     * Get the subscription dashboard data for the API/Mobile app.
     * This includes active subscriptions, available plans, features, and usage limits.
     */
    public function dashboard(Request $request, $box_slug = null)
    {
        $user = $request->user();
        $subscription = SubscriptionHelper::getActiveSubscription($user);
        $whatsappSubscription = SubscriptionHelper::getActiveWhatsappSubscription($user);
        
        $boxSlug = $box_slug ?? $request->route('box_slug') ?? session('box_slug');
        $box = null;
        if ($boxSlug) {
            $box = Box::where('slug', $boxSlug)->first();
        }
        
        if (!$box) {
            $boxUserEntry = BoxUser::where('user_id', $user->id)->first();
            if ($boxUserEntry) {
                $box = Box::find($boxUserEntry->box_id);
            }
        }

        // Available Plans
        $monthlyPlans = Plan::where('type', 'monthly')->where('plan_type', 'subscription')->where('status', 'active')->get();
        $yearlyPlans = Plan::where('type', 'yearly')->where('plan_type', 'subscription')->where('status', 'active')->get();
        $topupPlans = Plan::where('plan_type', 'topup')->where('status', 'active')->get();
        $whatsappPlans = Plan::where('plan_type', 'whatsapp')->where('status', 'active')->get();
        
        // Active Subscriptions & Topups
        $activeSubscriptions = SubscriptionHelper::getAllActiveSubscriptions($user);
        $activeTopups = $activeSubscriptions->filter(function($sub) {
            return $sub->plan && $sub->plan->plan_type === 'topup';
        })->values();

        // Features & Limits
        $combinedFeatures = SubscriptionHelper::getCombinedFeatures($user);
        
        $totalUsersCapacity = isset($combinedFeatures['No of Users']) ? (int) $combinedFeatures['No of Users'] : 0;
        $usedUsersCount = $box ? BoxUser::where('box_id', $box->id)->count() : 0;
        
        $usedWhatsappMessagesCount = $box ? \App\Models\WhatsAppMessage::where('box_id', $box->id)->count() : 0;
        $usedWhatsAppAgentsCount = $box ? BoxUser::where('box_id', $box->id)->where('has_whatsapp_access', 1)->count() : 0;
        $whatsappAgentLimit = SubscriptionHelper::getWhatsAppAgentLimit($user);

        return response()->json([
            'success' => true,
            'data' => [
                'active_subscription' => $subscription ? $subscription->load('plan') : null,
                'active_whatsapp_subscription' => $whatsappSubscription ? $whatsappSubscription->load('plan') : null,
                'active_topups' => $activeTopups->map(fn($t) => $t->load('plan')),
                'limits' => [
                    'users_total' => $totalUsersCapacity,
                    'users_used' => $usedUsersCount,
                    'whatsapp_agents_total' => $whatsappAgentLimit,
                    'whatsapp_agents_used' => $usedWhatsAppAgentsCount,
                    'whatsapp_messages_used' => $usedWhatsappMessagesCount,
                ],
                'features' => $combinedFeatures,
                'available_plans' => [
                    'monthly' => $monthlyPlans,
                    'yearly' => $yearlyPlans,
                    'topup' => $topupPlans,
                    'whatsapp' => $whatsappPlans,
                ]
            ]
        ]);
    }

    /**
     * Get billing history (invoices and refunds/credit notes).
     */
    public function invoices(Request $request)
    {
        $user = $request->user();
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
                                    $creditNotes[] = [
                                        'note_id'    => $rf['id'],
                                        'type'       => 'Refund',
                                        'created_at' => $rf['created_at'],
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
            return response()->json(['success' => false, 'message' => 'Failed to fetch billing history from payment gateway.'], 500);
        }

        // Sort descending
        usort($invoicesArray, function($a, $b) {
            return ($b['created_at'] ?? 0) <=> ($a['created_at'] ?? 0);
        });
        usort($creditNotes, function($a, $b) {
            return ($b['created_at'] ?? 0) <=> ($a['created_at'] ?? 0);
        });

        return response()->json([
            'success' => true,
            'data' => [
                'invoices' => $invoicesArray,
                'refunds' => $creditNotes
            ]
        ]);
    }
}
