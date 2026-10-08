<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailFilter extends Model implements AuditableContract
{
    use Auditable;
    use HasFactory;

    protected $fillable = [
        'box_id',
        'inbox_id',
        'name',
        'from',
        'subject',
        'has_words',
        'template_id',
        'status',
        'assigned_to',
        'lead_source_id',
        'lead_status_id',
        'round_robin',
        'round_robin_users',
        'enable_shifts',
        'shifts',
        'last_lead_at',
        'added_by',
        'last_updated_by',
    ];

    protected $casts = [
        'round_robin_users' => 'array',
        'shifts' => 'array',
        'last_lead_at' => 'datetime',
    ];

    public function box()
    {
        return $this->belongsTo(Box::class);
    }

    public function inbox()
    {
        // Maps to GmailInbox in this project structure
        return $this->belongsTo(GmailInbox::class, 'inbox_id');
    }

    public function template()
    {
        return $this->belongsTo(LeadSourceTemplate::class, 'template_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function leadSource()
    {
        return $this->belongsTo(CrmSource::class, 'lead_source_id');
    }

    public function leadStatus()
    {
        return $this->belongsTo(ContactStage::class, 'lead_status_id');
    }
}
