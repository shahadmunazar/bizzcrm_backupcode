<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class LeadStatusHistory extends Model implements AuditableContract
{
    use Auditable;

    protected $fillable = [
        'box_id',
        'lead_id',
        'old_status_id',
        'new_status_id',
        'changed_by',
    ];

    public function box()
    {
        return $this->belongsTo(Box::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function oldStatus()
    {
        return $this->belongsTo(ContactStage::class, 'old_status_id');
    }

    public function newStatus()
    {
        return $this->belongsTo(ContactStage::class, 'new_status_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
