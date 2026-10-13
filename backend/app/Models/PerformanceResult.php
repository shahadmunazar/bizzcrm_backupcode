<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformanceResult extends Model
{
    protected $fillable = [
        'target_assignment_id', 'box_user_id', 'department_id', 'team_id', 'target_value',
        'actual_value', 'achievement_percentage', 'weighted_score',
        'performance_status', 'calculated_at'
    ];

    public function assignment()
    {
        return $this->belongsTo(TargetAssignment::class, 'target_assignment_id');
    }

    public function boxUser()
    {
        return $this->belongsTo(BoxUser::class, 'box_user_id');
    }
}
