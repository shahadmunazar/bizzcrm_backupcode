<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Holiday extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    protected $fillable = [
        'box_id',
        'title',
        'date',
        'holiday_code',
        'holiday_type_id',
        'country_region',
        'holiday_type',
        'description',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
    protected $casts = [
    'country_region' => 'array',
];

    protected $dates = ['date', 'deleted_at'];
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

    public function holidayType()
{
    return $this->belongsTo(HolidayType::class, 'holiday_type_id');
}

}
