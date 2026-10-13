<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

use App\Traits\BizzAuditable;

class Activity extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable, BizzAuditable;
    protected $table = 'activities';

    protected static function booted()
    {
        parent::booted();

        static::created(function ($activity) {
            if ($activity->description) {
                \App\Jobs\GenerateAiInsightJob::dispatch($activity, 'activity');
            }
        });
    }

    protected $fillable = [
        'box_id',
        'activity_id',
        'customer_id',
        'activity_type',
        'label',
        'subject',
        'description',
        'type',
        'task_type',
        'activity_date',
        'reminder_date',
        'params',
        'assigned_to',
        'added_by',
        'last_updated_by',
        'status',
        'completed_date',
        'email_sent',
        'activity_status_id',
        'lead_status_id',
        'lead_sub_status_id',
        'closed_by',
        'activity_type_status',
        'error_log',
        'created_from',
        'done_with',
        'email_sent_through',
    ];

    protected $casts = [
        'activity_date' => 'datetime',
        'reminder_date' => 'datetime',
        'completed_date' => 'datetime',
        'params' => 'array',
    ];

    /**
     * Dynamically generate subject if it is null in the database.
     */
    public function getSubjectAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }

        if ($this->type === 'history') {
            $addedByName = $this->addedByUser ? $this->addedByUser->name : 'System';
            return $addedByName . ' made some changes';
        }


        
        $addedByName = $this->addedByUser ? $this->addedByUser->name : 'Unknown';
        
        $typeName = $this->type ? $this->type : '';
        $taskTypeStr = $this->task_type ? $this->task_type : '';
        $activityTypeCombined = $taskTypeStr ? ($typeName . ' ' . $taskTypeStr) : $typeName;
        
        $actStatus = $this->activity_type_status ? $this->activity_type_status : ($this->activityStatus ? $this->activityStatus->activity_status : null);
        
        $assignedToName = $this->assignedUser ? $this->assignedUser->name : 'Unknown';
        if ($this->assignedUser && $this->addedByUser && $this->assignedUser->id === $this->addedByUser->id) {
            $assignedToName = 'Self';
        } else if ($this->assigned_to === $this->added_by) {
            $assignedToName = 'Self';
        }
        
        $doneWithName = '';
        if ($this->doneWithUser) {
             $doneWithName = $this->doneWithUser->name;
        } else if ($this->done_with === $this->added_by) {
             $doneWithName = $addedByName;
        } else if ($this->done_with === $this->assigned_to) {
             $doneWithName = $this->assignedUser ? $this->assignedUser->name : '';
        }

        $constructedSubject = $addedByName . ' created a ' . $activityTypeCombined;
        
        if ($actStatus && $actStatus !== 'Pending' && $actStatus !== 'None') {
            $constructedSubject .= ' with status ' . $actStatus;
        }
        if ($doneWithName) {
            $constructedSubject .= ' along with ' . $doneWithName;
        }
        if ($assignedToName) {
            $constructedSubject .= ' and assigned to ' . $assignedToName;
        }

        return $constructedSubject;
    }

    /**
     * Strip HTML tags from description.
     */
    public function getDescriptionAttribute($value)
    {
        if (empty($value)) {
            return $value;
        }
        
        // Strip HTML tags and decode HTML entities (like &nbsp;)
        return html_entity_decode(strip_tags($value));
    }

    // Relationships
    public function box()
    {
        return $this->belongsTo(Box::class , 'box_id');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class , 'activity_id');
    }

    public function customer()
    {
        return $this->belongsTo(CustomerDetail::class, 'customer_id'); // Assuming CustomerDetail represents the customer, or Customer::class
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class , 'assigned_to');
    }


    public function closedByUser()
    {
        return $this->belongsTo(User::class , 'closed_by');
    }

    public function activityStatus()
    {
        return $this->belongsTo(ActivityStatus::class , 'activity_status_id');
    }

    public function activityTypeData()
    {
        return $this->belongsTo(ActivityType::class , 'activity_type');
    }

    public function activityUsers()
    {
        return $this->hasMany(ActivityUser::class , 'activity_id');
    }

    public function leadStatus()
    {
        return $this->belongsTo(ContactStage::class , 'lead_status_id');
    }

    public function leadSubStatus()
    {
        return $this->belongsTo(LeadSubStatus::class , 'lead_sub_status_id');
    }

    public function addedByUser()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function lastUpdatedByUser()
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }
    
    public function doneWithUser()
    {
        return $this->belongsTo(User::class, 'done_with');
    }
}
