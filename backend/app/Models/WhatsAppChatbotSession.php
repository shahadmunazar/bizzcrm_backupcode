<?php

namespace App\Models;

use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppChatbotSession extends Model implements AuditableContract
{
    use Auditable;

    use HasFactory;


    protected $table = 'whatsapp_chatbot_sessions';

    protected $fillable = [
        'box_id',
        'lead_id',
        'whatsapp_chatbot_id',
        'current_node_id',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function chatbot()
    {
        return $this->belongsTo(WhatsAppChatbot::class, 'whatsapp_chatbot_id');
    }
}
