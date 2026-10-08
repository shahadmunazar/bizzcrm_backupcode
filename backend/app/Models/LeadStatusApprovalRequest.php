<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadStatusApprovalRequest extends Model
{
    protected $table = 'lead_status_approval_requests';

    protected $fillable = [
        'box_id',
        'lead_id',
        'requested_by',
        'old_status_id',
        'new_status_id',
        'old_sub_status_id',
        'new_sub_status_id',
        'approval_designation_id',
        'status',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function oldStatus()
    {
        return $this->belongsTo(ContactStage::class, 'old_status_id');
    }

    public function newStatus()
    {
        return $this->belongsTo(ContactStage::class, 'new_status_id');
    }

    public function oldSubStatus()
    {
        return $this->belongsTo(LeadSubStatus::class, 'old_sub_status_id');
    }

    public function newSubStatus()
    {
        return $this->belongsTo(LeadSubStatus::class, 'new_sub_status_id');
    }
}
