<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class CrmSource extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    protected $table = 'crm_sources';

    protected $fillable = [
        'box_id',
        'name',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
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
        return $this->hasMany(Lead::class, 'lead_source_id');
    }
}
