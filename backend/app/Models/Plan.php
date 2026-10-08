<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model implements AuditableContract
{
    use Auditable;
    protected $fillable = [
        'name',
        'slug',
        'type',
        'payment_mode',
        'plan_type',
        'price',
        'currency',
        'interval_count',
        'trial_period_days',
        'grace_period_days',
        'description',
        'features',
        'status',
        'razorpay_plan_id',
        'discount_badge',
        'savings_text',
        'original_price',
        'billing_cycle',
        'marketing_msg_cost',
        'utility_msg_cost',
        'button_text',
        'is_highlighted',
    ];

    protected $casts = [
        'features' => 'array',
        'price' => 'decimal:2',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSubscription($query)
    {
        return $query->where('plan_type', 'subscription');
    }

    public function scopeWhatsapp($query)
    {
        return $query->where('plan_type', 'whatsapp');
    }
}
