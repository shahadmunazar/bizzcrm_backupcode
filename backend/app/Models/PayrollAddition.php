<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class PayrollAddition extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    protected $fillable = [
        'name',
        'category_name',
        'amount',
        'is_unit_calculation',
        'box_id',
        'assignee_type',
        'box_user_id',
        'employee_ids',
        'created_by',
        'updated_by',
        'deleted_by',
        'status',
    ];

    // Relationships
    public function box()
    {
        return $this->belongsTo(Box::class, 'box_id');
    }

    public function boxUser()
    {
        return $this->belongsTo(BoxUser::class, 'box_user_id');
    }
}
