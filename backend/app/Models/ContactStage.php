<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class ContactStage extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    protected $table = 'contact_stage';

    protected $fillable = [
        'box_id',
        'name',
        'status',
        'task_priority',
        'is_task_priority',
        'color',
        'created_by',
        'updated_by',
        'deleted_by',
        'reminder_mandatory',
        'notification',
        'priority',
        'is_default',
        'is_final',
        'display_amount_field',
        'no_activity_alert',
        'no_activity_alert_unit',
        'requires_approval',
        'approval_department_id',
        'approval_designation_id',
    ];
    protected $casts = [
        'task_priority' => 'array',
        'approval_roles' => 'array',
        'requires_approval' => 'boolean',
    ];

    public function box()
    {
        return $this->belongsTo(Box::class, 'box_id');
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

    public function leads()
    {
        return $this->hasMany(Lead::class, 'lead_status_id');
    }

    public function approvalDepartment()
    {
        return $this->belongsTo(Department::class, 'approval_department_id');
    }

    public function approvalDesignation()
    {
        return $this->belongsTo(\App\Models\Team::class, 'approval_designation_id');
    }
}
