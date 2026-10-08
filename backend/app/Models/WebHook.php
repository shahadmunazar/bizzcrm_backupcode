<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class WebHook extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;
    
    protected $fillable = [
        'box_id',
        'lead_api_box_id',
        'name',
        'email',
        'phone',
        'query',
        'company_name',
        'params',
        'status',
        'lead_id',
        'lead_source_id',
        'added_by',
        'updated_by',
        'created_by'
    ];

    protected $casts = [
        'params' => 'array',
    ];

    public function box()
    {
        return $this->belongsTo(Box::class);
    }

    public function integration()
    {
        return $this->belongsTo(BoxCrmIntegration::class, 'lead_api_box_id');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
