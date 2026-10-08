<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

use App\Traits\BizzAuditable;

class WhatsAppCampaign extends Model implements AuditableContract
{
    use SoftDeletes, Auditable, BizzAuditable;

    protected $table = 'whatsapp_campaigns';

    protected $fillable = [
        'box_id',
        'whats_app_template_id',
        'sender_id',
        'name',
        'schedule_at',
        'completed_at',
        'status',
        'include_all',
        'send_at',
        'params',
        'added_by',
        'updated_by',
        'deleted_by',
        'total_rows',
        'processed_rows',
        'file_path',
        'whatsapp_chatbot_id'
    ];

    protected $casts = [
        'schedule_at' => 'datetime',
        'completed_at' => 'datetime',
        'send_at' => 'datetime',
        'params' => 'array',
        'include_all' => 'boolean',
    ];

    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class);
    }

    public function chatbot(): BelongsTo
    {
        return $this->belongsTo(WhatsAppChatbot::class, 'whatsapp_chatbot_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(WhatsAppTemplate::class, 'whats_app_template_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(WhatsAppMessage::class, 'whatsapp_campaign_id');
    }

    public function numbers(): HasMany
    {
        return $this->hasMany(WhatsAppCampaignNumber::class, 'whatsapp_campaign_id');
    }
}
