<?php

namespace App\Models;

use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WhatsAppChatSetting extends Model implements AuditableContract
{
    use Auditable;

    use HasFactory, SoftDeletes;


    protected $table = 'whatsapp_chat_settings';

    protected $fillable = [
        'box_id',
        'lead_id',
        'disappearing_duration',
        'muted_until',
        'added_by',
        'last_updated_by'
    ];

    protected $casts = [
        'muted_until' => 'datetime'
    ];
}
