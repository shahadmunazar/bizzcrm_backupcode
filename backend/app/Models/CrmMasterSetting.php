<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class CrmMasterSetting extends Model implements AuditableContract
{
    use Auditable, SoftDeletes;

    protected $table = 'crm_master_settings';

    protected $fillable = [
        'box_id',
        'user_id',
        'box_user_id',
        'type',
        'name',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];


    public function box()
    {
        return $this->belongsTo(Box::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function boxUser()
    {
        return $this->belongsTo(BoxUser::class);
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


    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }
}
