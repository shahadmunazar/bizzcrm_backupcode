<?php

namespace App\Models;

use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppGeneratedWidget extends Model implements AuditableContract
{
    use Auditable;

    use HasFactory;


    protected $table = 'whatsapp_generated_widgets';

    protected $fillable = [
        'box_id',
        'type',
        'name',
        'phone_number',
        'custom_message',
        'config',
        'generated_url_or_code',
        'added_by',
    ];

    protected $casts = [
        'config' => 'array',
    ];

    public function box()
    {
        return $this->belongsTo(Box::class);
    }
}
