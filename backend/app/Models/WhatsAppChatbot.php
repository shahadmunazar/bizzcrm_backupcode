<?php

namespace App\Models;

use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppChatbot extends Model implements AuditableContract
{
    use Auditable;

    use HasFactory;


    protected $table = 'whatsapp_chatbots';

    protected $fillable = [
        'box_id',
        'whatsapp_project_id',
        'name',
        'trigger_type',
        'trigger_value',
        'flow_data',
        'settings',
        'is_active',
    ];

    protected $casts = [
        'flow_data' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    public function box()
    {
        return $this->belongsTo(Box::class);
    }

    public function project()
    {
        return $this->belongsTo(WhatsAppProject::class, 'whatsapp_project_id');
    }
}
