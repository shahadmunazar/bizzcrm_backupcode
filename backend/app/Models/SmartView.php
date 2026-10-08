<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmartView extends Model
{
    use HasFactory;

    protected $fillable = [
        'box_id',
        'user_id',
        'name',
        'module',
        'natural_language_instruction',
        'filters',
        'ai_rules',
        'priority',
        'auto_update',
    ];

    protected $casts = [
        'filters' => 'array',
        'ai_rules' => 'array',
        'auto_update' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
