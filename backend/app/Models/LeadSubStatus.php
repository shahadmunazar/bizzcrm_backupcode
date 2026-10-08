<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class LeadSubStatus extends Model implements AuditableContract
{
    use Auditable, SoftDeletes;
    protected $table    = 'lead_sub_status';
    protected $fillable = [
        'box_id',
        'name',
        'status',
        'leads_status_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'requires_approval',
        'approval_department_id',
        'approval_designation_id',
    ];

    protected $casts = [
        'approval_roles' => 'array',
        'requires_approval' => 'boolean',
    ];

    public function box()
    {
        return $this->belongsTo(Box::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function activitySetting()
    {
        return $this->belongsTo(Call::class, 'activity_setting_id');
    }

    public function leadsSubStatus()
    {
        return $this->hasMany(ContactStage::class, 'leads_status_id');
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
