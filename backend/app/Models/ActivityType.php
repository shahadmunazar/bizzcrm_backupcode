<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class ActivityType extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    protected $fillable = [
        'activity_setting_id',
        'box_id',
        'activity_type',
        'status',
        'target_type',
        'updated_by',
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

    public function activityStatuses()
    {
        return $this->hasMany(ActivityStatus::class, 'activity_type_id');
    }
}
