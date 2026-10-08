<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

use Illuminate\Database\Eloquent\Model;

class LeadWorkingLocation extends Model implements AuditableContract
{
    use Auditable;
    protected $fillable = [
        'box_id',
        'box_user_id',
        'lead_id',
        'state_id',
        'city_id',
        'division_id',
        'location_data',
        'status'
    ];

    protected $casts = [
        'location_data' => 'array',
    ];
}
