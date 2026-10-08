<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
class AttendanceBreak extends Model implements AuditableContract
{
    use SoftDeletes,Auditable;
    protected $table = 'attendance_break';
    protected $fillable = [
        'box_user_id',
        'attendance_id',
        'box_id',
        'break_start',
        'break_end',
        'duration',
        'notes',
        'break_status',
    ];
    protected $dates = [
        'break_start',
        'break_end',
        'created_at',
        'updated_at',
        'deleted_at',
    ];


    public function boxUser()
    {
        return $this->belongsTo(BoxUser::class, 'box_user_id');
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class, 'attendance_id');
    }
}
