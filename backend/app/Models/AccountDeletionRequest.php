<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountDeletionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'reason',
        'experience',
        'problems',
        'feedback',
        'confirmations',
        'otp_hash',
        'otp_expires_at',
        'otp_verified_at',
        'status',
        'rejection_reason',
        'scheduled_deletion_at',
        'reviewed_by',
        'approved_at',
        'rejected_at'
    ];

    protected $casts = [
        'problems' => 'array',
        'confirmations' => 'array',
        'otp_expires_at' => 'datetime',
        'otp_verified_at' => 'datetime',
        'scheduled_deletion_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function audits()
    {
        return $this->hasMany(AccountDeletionAudit::class);
    }
}
