<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class ActivityStatus extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;
    protected $table    = 'activity_statuses';
    protected $fillable = [
        'activity_setting_id',
        'activity_type_id',
        'box_id',
        'updated_by',
        'activity_status',
        'status',
    ];

    public function activitySetting()
    {
        return $this->belongsTo(Call::class, 'activity_setting_id');
    }
    public function activityType()
    {
        return $this->belongsTo(ActivityType::class, 'activity_type_id');
    }
    public function box()
    {
        return $this->belongsTo(Box::class, 'box_id');
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
