<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class AssetType extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'box_id',
        'name',
        'status',
        'added_by',
        'last_updated_by'
    ];

    public function assets()
    {
        return $this->hasMany(Asset::class , 'asset_type_id');
    }
}
