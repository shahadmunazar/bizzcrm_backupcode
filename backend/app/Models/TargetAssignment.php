<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TargetAssignment extends Model
{
    protected $fillable = [
        'target_template_id', 'box_user_id', 'department_id', 'team_id', 'calculated_target', 
        'salary_snapshot', 'salary_multiplier', 'status'
    ];

    public function template()
    {
        return $this->belongsTo(TargetTemplate::class, 'target_template_id');
    }

    public function boxUser()
    {
        return $this->belongsTo(BoxUser::class, 'box_user_id');
    }

    public function actuals()
    {
        return $this->hasMany(TargetActual::class);
    }

    public function performance()
    {
        return $this->hasOne(PerformanceResult::class);
    }
}
