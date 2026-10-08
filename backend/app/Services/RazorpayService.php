<?php

namespace App\Services;
use Razorpay\Api\Api;
use App\Models\Plan;
use Illuminate\Support\Facades\Log;

class RazorpayService
{
    protected $api;

    public function __construct()
    {
        $apiKey = config('services.razorpay.key') ?: 'rzp_test_SIMkRJkQ6BbIjC';
        $apiSecret = config('services.razorpay.secret') ?: '8lh8fkSiiqTqIRW4sDXdg9Qr';
        $this->api = new Api($apiKey, $apiSecret);
    }

    /**
     * Sync local plans with Razorpay.
     */
    public function syncLocalPlansToRazorpay()
    {
        $plans = Plan::where('status', 'active')->get();
        $results = [
            'created' => 0,
            'updated' => 0,
            'errors' => []
        ];

        foreach ($plans as $plan) {
            try {
                if (!$plan->razorpay_plan_id) {
                    $razorPlan = $this->createRazorpayPlan($plan);
                    $plan->razorpay_plan_id = $razorPlan['id'];
                    $plan->save();
                    $results['created']++;
                }
            } catch (\Exception $e) {
                Log::error("Failed to sync plan {$plan->name}: " . $e->getMessage());
                $results['errors'][] = "Plan {$plan->name}: " . $e->getMessage();
            }
        }

        return $results;
    }

    /**
     * Create a plan on Razorpay.
     */
    public function createRazorpayPlan(Plan $plan)
    {
        $period = $plan->type === 'monthly' ? 'monthly' : 'yearly';
        $interval = 1; 
        
        $planSuffix = $plan->type === 'yearly' ? ' (Yearly)' : ' (Monthly)';
        if ($plan->plan_type === 'topup') {
            $planSuffix .= ' (Recurring Top-Up)';
        }
        
        $data = [
            'period' => $period,
            'interval' => $interval,
            'item' => [
                'name' => $plan->name . $planSuffix,
                'description' => substr($plan->description, 0, 255),
                'amount' => (int) ($plan->price * 100), // Razorpay handles in paise
                'currency' => $plan->currency ?: 'INR',
            ],
            // Optional: 'notes' => ['local_plan_id' => $plan->id]
        ];

        return $this->api->plan->create($data);
    }

    /**
     * Fetch all plans from Razorpay.
     */
    public function fetchAllRazorpayPlans()
    {
        return $this->api->plan->all();
    }

    /**
     * Update a plan on Razorpay (Only name and description can be updated).
     */
    public function updateRazorpayPlan(Plan $plan)
    {
        if (!$plan->razorpay_plan_id) {
            return $this->createRazorpayPlan($plan);
        }
        return $this->api->plan->fetch($plan->razorpay_plan_id);
    }
}
