<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use App\Traits\BizzAuditable;

class WhatsAppCampaignNumber extends Model implements AuditableContract
{
    use SoftDeletes, Auditable, BizzAuditable;

    protected $table = 'whatsapp_campaign_numbers';

    protected $fillable = [
        'box_id',
        'whatsapp_campaign_id',
        'lead_id',
        'phone',
        'name',
        'status',
        'error_message',
        'metadata',
        'added_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(WhatsAppCampaign::class, 'whatsapp_campaign_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }
}
