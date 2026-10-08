<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use App\Traits\BizzAuditable;

class CustomerDetail extends Model implements AuditableContract
{
    use SoftDeletes, Auditable, BizzAuditable;

    protected $fillable = [
        'lead_id',
        'company_image',
        'customer_level',
        'parent_name',
        'whatsapp_number',
        'customer_type',
        'territory',
        'payment_terms',
        'credit_limit',
        'outstanding_amount',
        'box_id',
        'boxuser_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'latitude',
        'longitude',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
