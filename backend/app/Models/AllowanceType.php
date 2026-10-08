<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BizzAuditable;

class AllowanceType extends Model
{
    use HasFactory, BizzAuditable, SoftDeletes;

    protected $fillable = [
        'box_id',
        'name',
        'description',
        'calculation_type',
        'amount',
        'is_active',
        'created_by',
        'updated_by',
    ];

    public function box()
    {
        return $this->belongsTo(Box::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault(['name' => 'System']);
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by')->withDefault(['name' => 'System']);
    }

    public function policies()
    {
        return $this->hasMany(AllowancePolicy::class, 'allowance_type_id');
    }
}
