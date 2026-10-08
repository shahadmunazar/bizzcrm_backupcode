<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BizzAuditable;

class EmployeeAllowance extends Model
{
    use HasFactory, BizzAuditable;

    protected $fillable = [
        'box_id',
        'boxuser_id',
        'allowance_type_id',
        'business_date',
        'amount',
        'calculation_details',
        'status',
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
