<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use App\Traits\BizzAuditable;
class WhatsAppTemplate extends Model implements AuditableContract
{
    use SoftDeletes, Auditable, BizzAuditable;

    protected $table = 'whats_app_templates';

    protected $fillable = [
        'box_id',
        'waba_id',
        'whatsapp_number_id',
        'whats_app_id',
        'name',
        'components',
        'language',
        'whats_app_status',
        'category',
        'params',
        'status',
        'approved_at',
        'added_by',
        'updated_by',
        'last_updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'components' => 'array',
        'params' => 'array',
        'approved_at' => 'datetime',
    ];

    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class);
    }

    public function phoneNumber(): BelongsTo
    {
        return $this->belongsTo(WhatsAppPhoneNumber::class, 'whatsapp_number_id', 'whatsapp_id');
    }
}
