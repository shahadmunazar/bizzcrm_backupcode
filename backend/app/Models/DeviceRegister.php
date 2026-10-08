<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeviceRegister extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    protected $fillable = [
        'boxes_id',
        'user_id',
        'device_id',
        'device_name',
        'platform',
        'os_version',
        'app_version',
        'brand',
        'model',
        'public_key',
        'key_algorithm',
        'challenge_id',
        'signed_payload',
        'signature',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
