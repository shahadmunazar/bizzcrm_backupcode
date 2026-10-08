<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coupon extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $fillable = [
        'code',
        'user_type',
        'type',
        'value',
        'valid_until',
        'usage_limit',
        'used_count',
        'status',
    ];
}
