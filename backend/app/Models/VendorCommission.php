<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'box_id',
        'transaction_amount',
        'commission_percentage',
        'commission_amount',
        'status',
        'verified_at',
        'verified_by',
        'paid_at',
        'paid_by',
        'payment_reference',
        'payment_notes',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'paid_at' => 'datetime',
        'transaction_amount' => 'decimal:2',
        'commission_percentage' => 'decimal:2',
        'commission_amount' => 'decimal:2',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function box()
    {
        return $this->belongsTo(Box::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function payer()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    /**
     * Verify commission by superadmin.
     */
    public function verify($adminId = null, $notes = null)
    {
        $this->update([
            'status' => 'verified',
            'verified_at' => now(),
            'verified_by' => $adminId,
            'payment_notes' => $notes ?: $this->payment_notes,
        ]);
        return $this;
    }

    /**
     * Mark commission as paid by superadmin.
     */
    public function markPaid($adminId = null, $reference = null, $notes = null)
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'paid_by' => $adminId,
            'payment_reference' => $reference ?: $this->payment_reference,
            'payment_notes' => $notes ?: $this->payment_notes,
            'verified_at' => $this->verified_at ?: now(),
            'verified_by' => $this->verified_by ?: $adminId,
        ]);
        return $this;
    }

    /**
     * Revert commission to unpaid status.
     */
    public function markUnpaid($notes = null)
    {
        $this->update([
            'status' => 'unpaid',
            'paid_at' => null,
            'paid_by' => null,
            'verified_at' => null,
            'verified_by' => null,
            'payment_notes' => $notes ?: $this->payment_notes,
        ]);
        return $this;
    }

    /**
     * Record or synchronize vendor commission for any subscription (online or offline plan).
     * By default, status is 'unpaid' and requires superadmin verification and payout.
     */
    public static function recordCommissionForSubscription($subscription)
    {
        try {
            if (!$subscription || !\Illuminate\Support\Facades\Schema::hasTable('vendor_commissions') || !\Illuminate\Support\Facades\Schema::hasTable('vendors')) {
                return null;
            }

            $box = $subscription->box ?: \App\Models\Box::find($subscription->box_id);
            if (!$box) {
                return null;
            }

            // 1. Find referring vendor via referral_code
            $vendor = null;
            if (!empty($box->referral_code)) {
                $vendor = \App\Models\Vendor::where('referral_code', trim($box->referral_code))->first();
            }

            // 2. Check referral invitations if vendor not found directly by referral_code
            if (!$vendor && \Illuminate\Support\Facades\Schema::hasTable('vendor_referral_invitations')) {
                $invitation = \App\Models\VendorReferralInvitation::where('registered_box_id', $box->id)
                    ->orWhere(function ($q) use ($box) {
                        if (!empty($box->superadmin?->email)) {
                            $q->where('recipient_email', strtolower(trim($box->superadmin->email)));
                        }
                    })
                    ->latest()
                    ->first();

                if ($invitation && $invitation->vendor_id) {
                    $vendor = \App\Models\Vendor::find($invitation->vendor_id);
                    if ($vendor && empty($box->referral_code)) {
                        $box->update(['referral_code' => $vendor->referral_code]);
                    }
                }
            }

            if (!$vendor) {
                return null;
            }

            // 3. Get vendor's commission percentage
            $percentage = (float) config('bizzcrm.default_commission_percentage', 10);
            if (\Illuminate\Support\Facades\Schema::hasTable('vendor_percentages')) {
                $vendorPercentage = \App\Models\VendorPercentage::where('vendor_id', $vendor->id)->first();
                if ($vendorPercentage) {
                    $percentage = (float) $vendorPercentage->percentage;
                }
            }

            // 4. Get transaction amount from plan
            $plan = $subscription->plan ?: \App\Models\Plan::find($subscription->plan_id);
            $transactionAmount = $plan ? (float) $plan->price : 0;
            $commissionAmount = round($transactionAmount * ($percentage / 100), 2);

            // 5. Commission status: Default is UNPAID; will be verified and paid by superadmin
            $commissionStatus = 'unpaid';

            // 6. Check if commission already recorded for this box and vendor for this subscription date
            $existingCommission = self::where('vendor_id', $vendor->id)
                ->where('box_id', $box->id)
                ->whereDate('created_at', $subscription->created_at ? $subscription->created_at->toDateString() : now()->toDateString())
                ->first();

            if ($existingCommission) {
                return $existingCommission;
            }

            $commissionData = [
                'vendor_id' => $vendor->id,
                'box_id' => $box->id,
                'transaction_amount' => $transactionAmount,
                'commission_percentage' => $percentage,
                'commission_amount' => $commissionAmount,
                'status' => $commissionStatus,
                'paid_at' => null,
            ];

            $commission = self::create($commissionData);

            // 7. Synchronize invitation status if exists
            if (\Illuminate\Support\Facades\Schema::hasTable('vendor_referral_invitations')) {
                \App\Models\VendorReferralInvitation::where('vendor_id', $vendor->id)
                    ->where(function ($q) use ($box) {
                        $q->where('registered_box_id', $box->id);
                        if (!empty($box->superadmin?->email)) {
                            $q->orWhere('recipient_email', strtolower(trim($box->superadmin->email)));
                        }
                    })
                    ->update([
                        'status' => 'joined',
                        'registered_box_id' => $box->id,
                        'registered_at' => $box->created_at ?? now(),
                    ]);
            }

            return $commission;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('recordCommissionForSubscription non-fatal error: ' . $e->getMessage());
            return null;
        }
    }
}
