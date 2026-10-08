<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Box;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class HolidayType extends Model implements AuditableContract
{
    use SoftDeletes,Auditable;
    protected $table = 'holiday_type';
    protected $fillable = [
        'box_id',
        'holiday_type',
        'holiday_code',
        'holiday_date',
        'is_mandatory',
        'max_limit',
        'status',
        'calculate_in_calendar',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
    protected $dates = ['holiday_date', 'deleted_at'];

    /**
     * Box relation
     */
    public function box()
    {
        return $this->belongsTo(Box::class, 'box_id');
    }

    /**
     * User who created this holiday type
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * User who last updated this holiday type
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * User who deleted this holiday type
     */
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function holidays()
    {
        return $this->hasMany(Holiday::class);
    }
}
