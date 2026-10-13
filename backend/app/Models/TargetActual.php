<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TargetActual extends Model
{
    protected $fillable = [
        'target_assignment_id', 'metric_id', 'period', 'actual_value'
    ];

    public function assignment()
    {
        return $this->belongsTo(TargetAssignment::class, 'target_assignment_id');
    }

    public function metric()
    {
        return $this->belongsTo(TargetMetric::class, 'metric_id');
    }
}
