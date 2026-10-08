<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Shift extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;
    protected $table    = 'shifts';
    protected $fillable = [
        'box_id',
        'code',
        'shift_name',
        'in_time',
        'out_time',
        'shift_hours',
        'shift_enabled',
        'rest_enabled',
        'lunch_out',
        'lunch_in',
        'lunch_hours',
        'short_break_enabled',
        'short_break_out',
        'short_break_in',
        'short_break_hours',
        'half_day_enabled',
        'half_day_start_time',
        'half_day_end_time',
        'half_day_hours',
        'absent_below',
        'relaxation_time',
        'half_day_below',
        'short_leave_below',
        'max_relaxations_allowed',
        'overtime_allowed',
        'absent_deduction',
        'half_day_deduction',
        'short_leave_deduction',
        'short_break_enabled_2',
        'short_break_out_2',
        'short_break_in_2',
        'short_break_hours_2',
        'weekly_off_1',
        'weekly_off_2',
        'weekly_off_3',
        'weekly_off_4',
        'check_over_time',
        'early_login_punchIn',
        'early_login_minutes',
        'max_punch_in_allowed',
        'auto_punchout_time',
        'all_in_out_calculation',
        'first_in_last_out_calculation',
        'saturday_offs',
        'saturday_half_days',
        'half_day_time',
        'start_from_date',
        'end_date',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'saturday_offs'       => 'array',
        'saturday_half_days'  => 'array',
        'shift_enabled'       => 'boolean',
        'rest_enabled'        => 'boolean',
        'short_break_enabled' => 'boolean',
        'half_day_enabled'    => 'boolean',
        'start_from_date'     => 'date',
        'end_date'            => 'date',
        'all_in_out_calculation' => 'boolean',
        'first_in_last_out_calculation' => 'boolean',
    ];

    // === Relationships ===

    public function box()
    {
        return $this->belongsTo(Box::class);
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

    // === Auto-fill created_by, updated_by, deleted_by ===
    protected static function booted()
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::id();
                $model->updated_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });

        static::deleting(function ($model) {
            if (Auth::check()) {
                $model->deleted_by = Auth::id();
                $model->save(); // Save before soft delete
            }
        });
    }
}
