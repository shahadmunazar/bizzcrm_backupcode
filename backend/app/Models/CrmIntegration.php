<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

use Illuminate\Database\Eloquent\Model;

class CrmIntegration extends Model implements AuditableContract
{
    use Auditable;
    protected $table = 'crm_integrations';

    protected $fillable = [
        'name',
        'system_name',
        'status',
        'params',
    ];

    protected $casts = [
        'params' => 'array',
    ];
}
