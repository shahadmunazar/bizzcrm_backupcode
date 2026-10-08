<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BizzAuditable;

class Asset extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable, BizzAuditable;


    protected $fillable = [
        'box_id',
        'asset_id',
        'asset_type_id',
        'asset_name',
        'brand',
        'model',
        'serial_number',
        'vendor',
        'purchase_date',
        'cost',
        'warranty_end_date',
        'status',
        'description',
        'assigned_to',
        'assigned_date',
        'return_date',
        'added_by',
        'last_updated_by'
    ];

    protected $casts = [
        'purchase_date' => 'datetime',
        'warranty_end_date' => 'datetime',
        'assigned_date' => 'datetime',
        'return_date' => 'datetime',
        'cost' => 'decimal:2'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->asset_id)) {
                $latest = static::where('box_id', $model->box_id)->latest('id')->first();
                $nextId = $latest ? ((int) str_replace('AST-', '', $latest->asset_id) + 1) : 1;
                $model->asset_id = 'AST-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function assetType()
    {
        return $this->belongsTo(AssetType::class , 'asset_type_id');
    }

    public function assignedToBoxUser()
    {
        return $this->belongsTo(BoxUser::class , 'assigned_to');
    }
}
