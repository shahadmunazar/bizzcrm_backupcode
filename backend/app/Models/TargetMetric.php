<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TargetMetric extends Model
{
    protected $fillable = [
        'target_template_id', 'metric_type', 'metric_name', 'target_value',
        'unit', 'weight', 'calculation_direction'
    ];

    public function template()
    {
        return $this->belongsTo(TargetTemplate::class, 'target_template_id');
    }
}
