<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

use App\Traits\BizzAuditable;

class FacebookLead extends Model implements AuditableContract
{
    use SoftDeletes, Auditable, BizzAuditable;

    protected $fillable = [
        'box_id',
        'facebook_page_id',
        'lead_id',
        'adgroup_id',
        'ad_id',
        'created_time',
        'leadgen_id',
        'page_id',
        'form_id',
        'status',
        'data',
        'added_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'created_time' => 'datetime',
        'data' => 'array',
        'status' => 'string',
    ];

    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class);
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(FacebookPage::class, 'facebook_page_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

}
