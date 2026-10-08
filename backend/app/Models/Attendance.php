<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Attendance extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    protected $fillable = [

        'box_user_id',
        'box_id',
        'machine_id',
        'device_id',
        'created_by',
        'updated_by',
        'attendance_date',
        'check_attendace_by',
        'status',
        'check_in',
        'check_out',
        'is_auto_punch',
        'break_duration',
        'status_updated_at',
        'late_duration',
        'early_check_out',
        'production_hours',
        'working_hours',
        'over_time',
        'punch_in_latitude',
        'punch_in_longitude',
        'punch_in_location',
        'punch_out_latitude',
        'punch_out_longitude',
        'punch_out_location'
    ];

    // Relationships

    public function boxUser()
    {
        return $this->belongsTo(BoxUser::class);
    }

    public function box()
    {
        return $this->belongsTo(Box::class);
    }

    public function machine()
    {
        return $this->belongsTo(BiometricMachine::class, 'machine_id');
    }

// Attendance.php
public function breaks() {
    return $this->hasMany(AttendanceBreak::class);
}


    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

public function corrections()
    {
        return $this->hasMany(AttendanceCorrection::class, 'attendance_id');
    }


}
