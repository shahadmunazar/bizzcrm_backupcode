<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

use App\Traits\BizzAuditable;

class WhatsAppBusinessAccount extends Model implements AuditableContract
{
    use SoftDeletes, Auditable, BizzAuditable;
    
    protected $table = 'whats_app_accounts';

    protected $fillable = [
        'box_id',
        'whatsapp_business_portfolio_id',
        'waba_id',
        'name',
        'currency',
        'timezone',
        'access_token',
        'account_status',
        'is_active',
        'message_template_namespace',
        'metadata',
        'added_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class);
    }

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(WhatsAppBusinessPortfolio::class, 'whatsapp_business_portfolio_id', 'portfolio_id');
    }

    public function templates(): HasMany
    {
        return $this->hasMany(WhatsAppTemplate::class, 'whats_app_id', 'waba_id');
    }

    public function phoneNumbers(): HasMany
    {
        return $this->hasMany(WhatsAppPhoneNumber::class, 'target_id');
    }

}
