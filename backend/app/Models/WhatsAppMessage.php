<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

use App\Traits\BizzAuditable;

class WhatsAppMessage extends Model implements AuditableContract
{
    use SoftDeletes, Auditable, BizzAuditable;

    protected $table = 'whatsapp_messages';

    protected static function booted()
    {
        parent::booted();

        static::created(function ($message) {
            // Only process incoming text messages for AI suggestions
            if ($message->type === 'INCOMING' && !empty($message->text)) {
                \App\Jobs\ProcessIncomingWhatsAppJob::dispatch($message);
            }
            if (!empty($message->text)) {
                \App\Jobs\GenerateAiInsightJob::dispatch($message, 'whatsapp');
            }
        });
    }

    protected $fillable = [
        'box_id',
        'whatsapp_campaign_id',
        'lead_id',
        'type',
        'status',
        'error',
        'sender_id',
        'message_id',
        'reply_to_message_id',
        'is_pinned',
        'pinned_at',
        'pin_expires_at',
        'is_starred',
        'conversation_id',
        'conversation_expiration',
        'text',
        'is_sent',
        'sent_at',
        'is_delivered',
        'delivered_at',
        'is_read',
        'read_at',
        'media',
        'added_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'conversation_expiration' => 'datetime',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
        'pinned_at' => 'datetime',
        'pin_expires_at' => 'datetime',
        'media' => 'array',
        'is_sent' => 'boolean',
        'is_delivered' => 'boolean',
        'is_read' => 'boolean',
        'is_pinned' => 'boolean',
        'is_starred' => 'boolean',
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

    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(WhatsAppMessage::class, 'reply_to_message_id');
    }

}
