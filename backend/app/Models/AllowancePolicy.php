<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BizzAuditable;

class AllowancePolicy extends Model
{
    use HasFactory, BizzAuditable;

    protected $fillable = [
        'box_id',
        'boxuser_id',
        'role_name',
        'allowance_type_id',
        'rules',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'rules' => 'array',
    ];

    public function boxUser()
    {
        return $this->belongsTo(BoxUser::class, 'boxuser_id');
    }

    public function allowanceType()
    {
        return $this->belongsTo(AllowanceType::class);
    }
}
