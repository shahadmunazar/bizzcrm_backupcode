<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiInsight extends Model
{
    protected $fillable = [
        'box_id',
        'analyzable_id',
        'analyzable_type',
        'category',
        'intent',
        'sentiment',
        'priority',
        'action_required',
        'suggested_action',
        'confidence',
        'vector_embedding',
    ];

    protected $casts = [
        'action_required' => 'boolean',
        'vector_embedding' => 'array',
    ];

    public function analyzable()
    {
        return $this->morphTo();
    }
}
