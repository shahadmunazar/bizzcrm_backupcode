<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

use App\Traits\BizzAuditable;

class WhatsAppBusinessPortfolio extends Model implements AuditableContract
{
    use SoftDeletes, Auditable, BizzAuditable;

    protected $table = 'whats_app_portfolios';

    protected $fillable = [
        'box_id',
        'portfolio_id',
        'name',
        'timezone',
        'currency',
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

    public function accounts(): HasMany
    {
        return $this->hasMany(WhatsAppBusinessAccount::class, 'whatsapp_business_portfolio_id', 'portfolio_id');
    }

}
