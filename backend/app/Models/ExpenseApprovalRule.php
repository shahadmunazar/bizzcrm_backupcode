<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BizzAuditable;

class ExpenseApprovalRule extends Model
{
    use HasFactory, BizzAuditable, SoftDeletes;

    protected $fillable = [
        'box_id',
        'min_amount',
        'max_amount',
        'required_roles',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'required_roles' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault(['name' => 'System']);
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by')->withDefault(['name' => 'System']);
    }
}
