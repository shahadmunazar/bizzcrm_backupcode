<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

use Illuminate\Database\Eloquent\Model;

class GmailMessage extends Model implements AuditableContract
{
    use Auditable;
    protected $fillable = [
        'gmail_inbox_id',
        'message_id',
        'thread_id',
        'from_email',
        'from_name',
        'subject',
        'snippet',
        'body',
        'received_at',
        'lead_id',
        'is_processed',
        'activity_status',
        'raw_data',
    ];

    protected $casts = [
        'received_at' => 'datetime',
        'raw_data' => 'array',
        'is_processed' => 'boolean',
    ];

    public function inbox()
    {
        return $this->belongsTo(GmailInbox::class , 'gmail_inbox_id');
    }
}
