<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAiRule extends Model
{
    protected $fillable = [
        'user_id',
        'box_id',
        'name',
        'description',
        'module',
        'trigger',
        'conditions',
        'actions',
        'is_active',
    ];

    protected $casts = [
        'conditions' => 'array',
        'actions' => 'array',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
