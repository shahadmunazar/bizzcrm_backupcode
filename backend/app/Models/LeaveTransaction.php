<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class LeaveTransaction extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    protected $table = 'leave_transaction';

    protected $fillable = [
        'box_id',
        'box_user_id',
        'leave_type_id',
        'date',
        'credit_leave',
        'debit_leave',
        'lapsed_leave',
        'carried_forward_leave',
        'total_leave',
        'balance_leave',
        'transaction_remarks',
        'leave_request_id',
        'opening_balance',
        'status',
        'applied_date',
        'approved_date'
    ];

    public function box()
    {
        return $this->belongsTo(Box::class);
    }

    public function boxUser()
    {
        return $this->belongsTo(BoxUser::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function leaveRequest()
    {
        return $this->belongsTo(LeaveRequest::class, 'leave_request_id');
    }
}
