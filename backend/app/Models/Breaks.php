<?php
namespace App\Models;

use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Breaks extends Model implements AuditableContract
{
    use Auditable;
    use SoftDeletes;

    protected $fillable = [
        'attendance_id',
        'break_start',
        'break_end',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function getDurationAttribute()
    {
        if ($this->break_start && $this->break_end) {
            return \Carbon\Carbon::parse($this->break_start)->diffInMinutes($this->break_end);
        }
        return 0;
    }
}
