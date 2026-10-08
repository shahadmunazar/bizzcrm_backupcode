<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'box_id',
        'user_id',
        'referencable_type',
        'referencable_id',
        'action',
        'old_status',
        'new_status',
        'comment',
    ];

    public function referencable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
