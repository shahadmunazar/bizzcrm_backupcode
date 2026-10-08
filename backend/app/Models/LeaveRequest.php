<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class LeaveRequest extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;
    protected $table = 'leave_requests';
    protected $fillable = [
        'box_id',
        'box_user_id',
        'leave_type',
        'leave_balance',
        'applied_for',
        'attachment',
        'from_date',
        'to_date',
        'approved_by',
        'total_days',
        'paid_days',
        'unpaid_days',
        'contact_during_leave',
        'reason',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
    /**
     * Relationships
     */
    public function boxUser()
    {
        return $this->belongsTo(BoxUser::class, 'box_user_id');
    }

    public function box()
    {
        return $this->belongsTo(Box::class, 'box_id');
    }

    public function approvedByUser()
{
    return $this->belongsTo(User::class, 'approved_by');
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

    public function leaveType()
{
    return $this->belongsTo(LeaveType::class, 'leave_type');
}
}
