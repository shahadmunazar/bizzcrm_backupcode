<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class WhatsappPlan extends Model implements AuditableContract
{
    use Auditable;
    protected $fillable = [
        'plan_name',
        'discount_badge',
        'savings_text',
        'description',
        'original_price',
        'current_price',
        'billing_cycle',
        'marketing_msg_cost',
        'utility_msg_cost',
        'button_text',
        'is_highlighted',
        'features',
    ];

    protected $casts = [
        'features' => 'array',
        'is_highlighted' => 'boolean',
    ];
}
