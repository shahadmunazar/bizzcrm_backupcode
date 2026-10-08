<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model implements AuditableContract
{
    use Auditable;
    protected $fillable = [
        'user_id',
        'box_id',
        'box_user_id',
        'plan_id',
        'razorpay_subscription_id',
        'razorpay_plan_id',
        'status',
        'starts_at',
        'ends_at',
        'cancelled_at',
        'cancellation_reason',
        'trial_ends_at',
        'next_billing_at',
        'raw_data',
        'payment_method'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'next_billing_at' => 'datetime',
        'raw_data' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function box()
    {
        return $this->belongsTo(Box::class);
    }
}
