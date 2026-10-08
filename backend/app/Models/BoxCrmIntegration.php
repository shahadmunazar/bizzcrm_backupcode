<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BoxCrmIntegration extends Model implements AuditableContract
{
    use Auditable;
    use SoftDeletes;

    protected $fillable = [
        'lead_api_id',
        'box_id',
        'status',
        'connection_status',
        'last_checked_date',
        'error_log',
        'params',
        'last_lead_date',
        'token',
    ];

    protected $casts = [
        'params' => 'array',
    ];

    public function api()
    {
        return $this->belongsTo(CrmIntegration::class, 'lead_api_id');
    }

    public function box()
    {
        return $this->belongsTo(Box::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'box_crm_integration_user', 'box_crm_integration_id', 'user_id');
    }
}
