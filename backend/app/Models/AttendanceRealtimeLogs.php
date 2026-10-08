<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class AttendanceRealtimeLogs extends Model implements AuditableContract
{
    use Auditable;
    protected $connection = 'attendance';
    protected $table = 'realtime_logs';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'device_id',
        'user_id',
        'verify_mode',
        'io_mode',
        'io_time',
        'log_image',
        'log_data_json',
        'received_at',
        'client_ip',
    ];
}
