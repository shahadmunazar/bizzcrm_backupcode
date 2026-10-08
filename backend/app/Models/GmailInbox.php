<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class GmailInbox extends Model implements AuditableContract
{
    use Auditable;

    protected $fillable = [
        'box_id',
        'user_id',
        'email',
        'access_token',
        'refresh_token',
        'expires_in',
        'token_type',
        'last_sync_at',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function box()
    {
        return $this->belongsTo(Box::class);
    }

    public function filters()
    {
        return $this->hasMany(MailFilter::class, 'inbox_id');
    }
}