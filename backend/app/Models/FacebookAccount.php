<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

use App\Traits\BizzAuditable;

class FacebookAccount extends Model implements AuditableContract
{
    use SoftDeletes, Auditable, BizzAuditable;

    protected $fillable = [
        'box_id',
        'name',
        'facebook_id',
        'access_token',
        'expires_at',
        'status',
        'api_params',
        'added_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'api_params' => 'array',
        'expires_at' => 'datetime'
    ];

    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class);
    }

    public function pages(): HasMany
    {
        return $this->hasMany(FacebookPage::class, 'facebook_account_id');
    }

}
