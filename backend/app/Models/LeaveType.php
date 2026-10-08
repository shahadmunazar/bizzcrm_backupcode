<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class LeaveType extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;
    protected $table    = 'leave_types';
    protected $fillable = [
        'box_id',
        'leave_type',
        'credit_frequency',
        'credit_days',
        'number_of_days',
        'leave_code',
        'holiday_off',
                'holiday_off_details',
                'weekly_off',
                'weekly_off_details',
                'overtime_equal_shift',
                'overtime_equal_shift_details',
        'leave_credit_type',
        'carry_forward',
        'carry_forward_days',
        'leave_color',
        'leave_icon',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',

    ];
    public function box()
    {
        return $this->belongsTo(Box::class, 'box_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
