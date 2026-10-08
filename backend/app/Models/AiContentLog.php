<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class AiContentLog extends Model implements AuditableContract
{
    use Auditable;
    protected $fillable = [
        'box_id',
        'user_id',
        'prompt',
        'result',
        'style',
        'optimize',
        'type'
    ];
}
