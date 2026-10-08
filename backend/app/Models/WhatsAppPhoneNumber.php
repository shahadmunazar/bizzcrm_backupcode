<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

use App\Traits\BizzAuditable;

class WhatsAppPhoneNumber extends Model implements AuditableContract
{
    use SoftDeletes, Auditable, BizzAuditable;
    
    protected $table = 'whats_app_numbers';

    protected $fillable = [
        'box_id',
        'target_id',
        'whatsapp_id',
        'display_phone_number',
        'name_status',
        'status',
        'is_active',
        'verification_status',
        'quality_rating',
        'capabilities',
        'metadata',
        'added_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'capabilities' => 'array',
        'metadata' => 'array',
    ];

    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class);
    }

    public function whatsappBusinessAccount(): BelongsTo
    {
        return $this->belongsTo(WhatsAppBusinessAccount::class, 'target_id');
    }

}
