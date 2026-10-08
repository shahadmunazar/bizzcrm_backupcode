<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class AttendanceCorrection extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'attendance_corrections';

    protected $fillable = [
        'box_id',
        'attendance_id',
        'correction_type',
        'break_id',
        'break_start',
        'break_end',
        'box_user_id',
        'punch_in',
        'punch_in_time',
        'punch_out',
        'punch_out_time',
        'reason',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];


    public function box()
    {
        return $this->belongsTo(Box::class, 'box_id');
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class, 'attendance_id');
    }

    public function break()
    {
        return $this->belongsTo(AttendanceBreak::class, 'break_id');
    }

    public function boxUser()
    {
        return $this->belongsTo(BoxUser::class, 'box_user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
