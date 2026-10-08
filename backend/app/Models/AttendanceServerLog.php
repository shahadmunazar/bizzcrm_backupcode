<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class AttendanceServerLog extends Model implements AuditableContract
{
    use Auditable;
    protected $connection = 'attendance';
    protected $table = 'server_logs';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'log_level',
        'message',
        'client_ip',
        'device_id',
        'request_code',
        'created_at'
    ];
}
