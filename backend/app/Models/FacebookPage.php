<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

use App\Traits\BizzAuditable;

class FacebookPage extends Model implements AuditableContract
{
    use SoftDeletes, Auditable, BizzAuditable;

    protected $fillable = [
        'box_id',
        'facebook_account_id',
        'page_id',
        'name',
        'access_token',
        'webhook_enabled',
        'verification_code',
        'added_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'webhook_enabled' => 'string',
    ];

    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(FacebookAccount::class, 'facebook_account_id');
    }

    public function forms(): HasMany
    {
        return $this->hasMany(FacebookForm::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(FacebookLead::class);
    }
}
