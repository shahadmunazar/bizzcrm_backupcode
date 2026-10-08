<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

use App\Traits\BizzAuditable;

class FacebookForm extends Model implements AuditableContract
{
    use SoftDeletes, Auditable, BizzAuditable;

    protected $fillable = [
        'box_id',
        'facebook_page_id',
        'form_id',
        'name',
        'status',
        'total_leads',
        'mapping',
        'is_mapped',
        'added_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'mapping' => 'array',
        'is_mapped' => 'string',
    ];

    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class);
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(FacebookPage::class, 'facebook_page_id');
    }

    public function facebookleads(): HasMany
    {
        return $this->hasMany(FacebookLead::class, 'form_id', 'form_id');
    }
}
