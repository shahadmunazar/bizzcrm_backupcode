<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TargetTemplate extends Model
{
    protected $fillable = [
        'box_id', 'name', 'department_id', 'target_type', 'calculation_method', 
        'frequency', 'start_date', 'end_date', 'salary_multiplier', 'status', 'created_by'
    ];

    public function metrics()
    {
        return $this->hasMany(TargetMetric::class);
    }

    public function assignments()
    {
        return $this->hasMany(TargetAssignment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
