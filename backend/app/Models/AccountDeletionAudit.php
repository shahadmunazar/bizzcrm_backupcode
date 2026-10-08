<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountDeletionAudit extends Model
{
    protected $fillable = [
        'account_deletion_request_id',
        'event',
        'description',
        'user_id'
    ];

    public function request()
    {
        return $this->belongsTo(AccountDeletionRequest::class, 'account_deletion_request_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
