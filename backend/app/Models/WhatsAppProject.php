<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

use App\Traits\BizzAuditable;

class WhatsAppProject extends Model implements AuditableContract
{
    use SoftDeletes, Auditable, BizzAuditable;

    protected $fillable = [
        'box_id',
        'name',
        'whatsapp_business_account_id',
        'whatsapp_phone_number_id',
        'is_active',
        'added_by',
        'updated_by',
        'deleted_by'
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(WhatsAppBusinessAccount::class, 'whatsapp_business_account_id');
    }

    public function phone(): BelongsTo
    {
        return $this->belongsTo(WhatsAppPhoneNumber::class, 'whatsapp_phone_number_id');
    }

}
