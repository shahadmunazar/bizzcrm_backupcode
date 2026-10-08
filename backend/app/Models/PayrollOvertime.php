<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class PayrollOvertime extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;
    protected $fillable = [
        'name',
        'rate_type',
        'rate',
        'box_id',
        'box_user_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'status',
    ];

    public function box()
    {
        return $this->belongsTo(Box::class, 'box_id');
    }

    public function boxUser()
    {
        return $this->belongsTo(BoxUser::class, 'box_user_id');
    }
}
