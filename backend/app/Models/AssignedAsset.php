<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class AssignedAsset extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'asset_id',
        'box_user_id',
        'box_id',
        'assigned_date',
        'returned_date',
        'added_by'
    ];

    protected $casts = [
        'assigned_date' => 'datetime',
        'returned_date' => 'datetime'
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function boxUser()
    {
        return $this->belongsTo(BoxUser::class, 'box_user_id');
    }

    public function box()
    {
        return $this->belongsTo(Box::class);
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
