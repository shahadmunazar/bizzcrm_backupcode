<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

use Illuminate\Support\Facades\Log;
use App\Models\LeadStatusHistory;

use App\Traits\BizzAuditable;

class Lead extends Model implements AuditableContract
{
    use SoftDeletes, Auditable, BizzAuditable;

    public $bypass_approval = false;

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        parent::booted();

        static::updating(function ($lead) {
            if (isset($lead->bypass_approval) && $lead->bypass_approval) return;

            $user = auth()->user();
            if (!$user) return;

            $isGlobalAdmin = $user->hasRole(['superadmin', 'admin']);
            $boxUser = \App\Models\BoxUser::where('user_id', $user->id)->first();
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

            $statusRequiresApproval = false;
            $subStatusRequiresApproval = false;
            $newStatusId = $lead->lead_status_id;
            $newSubStatusId = $lead->lead_sub_status_id;

            if ($lead->isDirty('lead_status_id') && $newStatusId) {
                $statusModel = \App\Models\ContactStage::find($newStatusId);
                if ($statusModel && $statusModel->requires_approval) {
                    $statusRequiresApproval = true;
                }
            }

            if ($lead->isDirty('lead_sub_status_id') && $newSubStatusId) {
                $subStatusModel = \App\Models\LeadSubStatus::find($newSubStatusId);
                if ($subStatusModel && $subStatusModel->requires_approval) {
                    $subStatusRequiresApproval = true;
                }
            }

            if ($statusRequiresApproval || $subStatusRequiresApproval) {
                \Log::info('Lead status change requires approval', [
                    'lead_id' => $lead->id,
                    'old_status_id' => $lead->getOriginal('lead_status_id'),
                    'new_status_id' => $newStatusId,
                    'status_requires_approval' => $statusRequiresApproval,
                    'sub_status_requires_approval' => $subStatusRequiresApproval,
                    'requested_by' => $user->id
                ]);

                $approvalDesignationId = null;
                if ($statusRequiresApproval && isset($statusModel) && $statusModel->approval_designation_id) {
                    $approvalDesignationId = $statusModel->approval_designation_id;
                } elseif ($subStatusRequiresApproval && isset($subStatusModel) && $subStatusModel->approval_designation_id) {
                    $approvalDesignationId = $subStatusModel->approval_designation_id;
                }

                \App\Models\LeadStatusApprovalRequest::create([
                    'box_id' => $lead->box_id,
                    'lead_id' => $lead->id,
                    'requested_by' => $user->id,
                    'old_status_id' => $lead->getOriginal('lead_status_id'),
                    'new_status_id' => $statusRequiresApproval ? $newStatusId : $lead->getOriginal('lead_status_id'),
                    'old_sub_status_id' => $lead->getOriginal('lead_sub_status_id'),
                    'new_sub_status_id' => $subStatusRequiresApproval ? $newSubStatusId : $lead->getOriginal('lead_sub_status_id'),
                    'approval_designation_id' => $approvalDesignationId,
                    'status' => 'pending',
                ]);
                
                $designationName = 'Management';
                if ($statusRequiresApproval && isset($statusModel) && $statusModel->approval_designation_id) {
                    $dept = \App\Models\Team::find($statusModel->approval_designation_id);
                    if ($dept) $designationName = $dept->name;
                } elseif ($subStatusRequiresApproval && isset($subStatusModel) && $subStatusModel->approval_designation_id) {
                    $dept = \App\Models\Team::find($subStatusModel->approval_designation_id);
                    if ($dept) $designationName = $dept->name;
                }

                \App\Models\Activity::create([
                    'box_id' => $lead->box_id,
                    'activity_id' => $lead->id,
                    'type' => 'history',
                    'activity_type' => 'leads',
                    'subject' => 'Approval Requested',
                    'description' => "A status change approval has been requested and is pending review by {$designationName}.",
                    'activity_date' => now(),
                    'added_by' => $user->id,
                    'last_updated_by' => $user->id,
                    'status' => 'Close',
                    'completed_date' => now(),
                ]);

                if ($statusRequiresApproval) {
                    $lead->lead_status_id = $lead->getOriginal('lead_status_id');
                }
                if ($subStatusRequiresApproval) {
                    $lead->lead_sub_status_id = $lead->getOriginal('lead_sub_status_id');
                }
            }
        });

        static::creating(function ($lead) {
            if (isset($lead->bypass_approval) && $lead->bypass_approval) return;

            $user = auth()->user();
            if (!$user) return;

            $isGlobalAdmin = $user->hasRole(['superadmin', 'admin']);
            $boxUser = \App\Models\BoxUser::where('user_id', $user->id)->first();
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
            if ($isGlobalAdmin || $isBoxAdmin) return;

            $statusRequiresApproval = false;
            $subStatusRequiresApproval = false;
            $newStatusId = $lead->lead_status_id;
            $newSubStatusId = $lead->lead_sub_status_id;

            if ($newStatusId) {
                $statusModel = \App\Models\ContactStage::find($newStatusId);
                if ($statusModel && $statusModel->requires_approval) {
                    $statusRequiresApproval = true;
                }
            }

            if ($newSubStatusId) {
                $subStatusModel = \App\Models\LeadSubStatus::find($newSubStatusId);
                if ($subStatusModel && $subStatusModel->requires_approval) {
                    $subStatusRequiresApproval = true;
                }
            }

            if ($statusRequiresApproval || $subStatusRequiresApproval) {
                \Log::info('Lead (new) status requires approval', [
                    'new_status_id' => $newStatusId,
                    'status_requires_approval' => $statusRequiresApproval,
                    'sub_status_requires_approval' => $subStatusRequiresApproval,
                    'requested_by' => $user->id
                ]);

                if ($statusRequiresApproval) {
                    $lead->lead_status_id = null;
                }
                if ($subStatusRequiresApproval) {
                    $lead->lead_sub_status_id = null;
                }
                
                $approvalDesignationId = null;
                if ($statusRequiresApproval && isset($statusModel) && $statusModel->approval_designation_id) {
                    $approvalDesignationId = $statusModel->approval_designation_id;
                } elseif ($subStatusRequiresApproval && isset($subStatusModel) && $subStatusModel->approval_designation_id) {
                    $approvalDesignationId = $subStatusModel->approval_designation_id;
                }

                $lead->pending_approval_data = [
                    'new_status_id' => $statusRequiresApproval ? $newStatusId : null,
                    'new_sub_status_id' => $subStatusRequiresApproval ? $newSubStatusId : null,
                    'approval_designation_id' => $approvalDesignationId,
                ];
            }
        });

        static::updated(function ($lead) {
            Log::info('🛠️ Lead updated event fired for ID: ' . $lead->id);
            if ($lead->isDirty('lead_status_id')) {
                $oldStatus = $lead->getOriginal('lead_status_id');
                $newStatus = $lead->lead_status_id;

                Log::info('🔄 Lead status change detected in update:', ['old' => $oldStatus, 'new' => $newStatus]);

                if ($oldStatus != $newStatus) {
                    LeadStatusHistory::create([
                        'box_id'        => $lead->box_id,
                        'lead_id'       => $lead->id,
                        'old_status_id' => $oldStatus,
                        'new_status_id' => $newStatus,
                        'changed_by'    => auth()->id() ?? $lead->last_updated_by,
                    ]);
                    Log::info('✅ LeadStatusHistory created via update.');
                }
            }
        });

        static::created(function ($lead) {
            Log::info('✨ Lead created event fired for ID: ' . $lead->id . ' with status: ' . ($lead->lead_status_id ?? 'NULL'));
            
            // Dispatch AI Analysis Job
            \App\Jobs\AnalyzeLeadJob::dispatch($lead);
            \App\Jobs\GenerateAiInsightJob::dispatch($lead, 'lead');
            
            if ($lead->lead_status_id) {
                LeadStatusHistory::create([
                    'box_id'        => $lead->box_id,
                    'lead_id'       => $lead->id,
                    'old_status_id' => null,
                    'new_status_id' => $lead->lead_status_id,
                    'changed_by'    => auth()->id() ?? $lead->added_by,
                ]);
                Log::info('✅ LeadStatusHistory created via create event.');
            }

            if (isset($lead->pending_approval_data)) {
                $user = auth()->user();
                $newStatusId = $lead->pending_approval_data['new_status_id'];
                $newSubStatusId = $lead->pending_approval_data['new_sub_status_id'];

                \App\Models\LeadStatusApprovalRequest::create([
                    'box_id' => $lead->box_id,
                    'lead_id' => $lead->id,
                    'requested_by' => $user ? $user->id : null,
                    'old_status_id' => null,
                    'new_status_id' => $newStatusId,
                    'old_sub_status_id' => null,
                    'new_sub_status_id' => $newSubStatusId,
                    'approval_designation_id' => $lead->pending_approval_data['approval_designation_id'] ?? null,
                    'status' => 'pending',
                ]);

                $designationName = 'Management';
                if ($newStatusId) {
                    $statusModel = \App\Models\ContactStage::find($newStatusId);
                    if ($statusModel && $statusModel->approval_designation_id) {
                        $dept = \App\Models\Team::find($statusModel->approval_designation_id);
                        if ($dept) $designationName = $dept->name;
                    }
                } elseif ($newSubStatusId) {
                    $subStatusModel = \App\Models\LeadSubStatus::find($newSubStatusId);
                    if ($subStatusModel && $subStatusModel->approval_designation_id) {
                        $dept = \App\Models\Team::find($subStatusModel->approval_designation_id);
                        if ($dept) $designationName = $dept->name;
                    }
                }

                \App\Models\Activity::create([
                    'box_id' => $lead->box_id,
                    'activity_id' => $lead->id,
                    'type' => 'history',
                    'activity_type' => 'leads',
                    'subject' => 'Approval Requested',
                    'description' => "A status change approval has been requested for this new lead and is pending review by {$designationName}.",
                    'activity_date' => now(),
                    'added_by' => $user ? $user->id : null,
                    'last_updated_by' => $user ? $user->id : null,
                    'status' => 'Close',
                    'completed_date' => now(),
                ]);
            }
        });
    }

    protected $fillable = [
        'box_id',
        'bulk_upload_id',
        'name',
        'last_name',
        'is_starred',
        'email',
        'sec_email',
        'phone',
        'phone_type',
        'mobile',
        'sec_phone',
        'query',
        'designation',
        'address',
        'postal_code',
        'ip_address',
        'country_id',
        'state_id',
        'city_id',
        'lead_source_id',
        'lead_status_id',
        'added_by',
        'last_updated_by',
        'assigned_to',
        'drug_license',
        'gstin',
        'experience',
        'location',
        'industry',
        'created_from',
        'last_activity_id',
        'last_note_id',
        'is_matured',
        'matured_date',
        'amount',
        'currency',
        'process_uploading',
        'is_paid',
        'lead_type',
        'trashed_at',
        'gstin_number',
        'pan_number',
        'company_name',
        'tags',
        'description',
        'visibility',
        'status',
        'duplicate_leads',
        'last_assigned_to',
        'last_lead_status',
        'lost_reason_id',
        'lead_sub_status_id',
        'change_assigned_to_time',
        'change_lead_status_time',
        'assignment_type',
    ];

    protected $casts = [
        'matured_date' => 'date',
        'trashed_at'   => 'datetime',
        'is_matured'   => 'string',
        'is_paid'      => 'string',
        'amount'       => 'float',
        'change_assigned_to_time' => 'datetime',
        'change_lead_status_time' => 'datetime',
    ];

    // === Relationships ===

    public function customerDetail()
    {
        return $this->hasOne(CustomerDetail::class, 'lead_id');
    }

    public function checkIns()
    {
        return $this->hasMany(CustomerCheckIn::class, 'customer_id');
    }

    public function box()
    {
        return $this->belongsTo(Box::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function leadSource()
    {
        return $this->belongsTo(CrmSource::class, 'lead_source_id');
    }

    public function leadStatus()
    {
        return $this->belongsTo(ContactStage::class, 'lead_status_id');
    }
    
    public function lostReason()
    {
        return $this->belongsTo(LostReason::class, 'lost_reason_id');
    }

    public function industryRelation()
    {
        return $this->belongsTo(Industry::class, 'industry');
    }


    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function lastAssignedTo()
    {
        return $this->belongsTo(User::class, 'last_assigned_to');
    }

    public function lastLeadStatus()
    {
        return $this->belongsTo(ContactStage::class, 'last_lead_status');
    }

    public function bulkUpload()
    {
        return $this->belongsTo(BulkUpload::class);
    }

    public function leadSubStatus()
    {
        return $this->belongsTo(LeadSubStatus::class, 'lead_sub_status_id');
    }

    public function divisionAreas()
    {
        return $this->hasMany(DivisionArea::class, 'lead_id');
    }

    public function assignees()
    {
        return $this->belongsToMany(User::class, 'lead_assignees')->withPivot('assigned_by')->withTimestamps();
    }

    public function assignmentHistories()
    {
        return $this->hasMany(LeadAssignmentHistory::class, 'lead_id');
    }

    public function statusHistories()
    {
        return $this->hasMany(LeadStatusHistory::class, 'lead_id')->orderBy('created_at', 'desc');
    }

    public function lastActivity()
    {
        return $this->hasOne(\App\Models\Activity::class, 'activity_id')
            ->where('activity_date', '<', \Carbon\Carbon::now())
            ->latestOfMany('activity_date');
    }
    // public function lastNote()
    // {
    //     return $this->belongsTo(Note::class, 'last_note_id');
    // }

    public function whatsappMessages()
    {
        return $this->hasMany(WhatsAppMessage::class, 'lead_id');
    }

    public function whatsappChatSetting()
    {
        return $this->hasOne(WhatsAppChatSetting::class, 'lead_id');
    }

    public function latestWhatsAppMessage()
    {
        return $this->hasOne(WhatsAppMessage::class, 'lead_id')->ofMany(['created_at' => 'max', 'id' => 'max'], function ($query) {
            // default
        });
    }

    public function latestIncomingWhatsAppMessage()
    {
        return $this->hasOne(WhatsAppMessage::class, 'lead_id')->where('type', 'INCOMING')->ofMany(['created_at' => 'max', 'id' => 'max'], function ($query) {
            // default
        });
    }

    public function receipts()
    {
        return $this->hasMany(\App\Models\Accounts\Receipt::class, 'lead_id');
    }

    public function latestReceipt()
    {
        return $this->hasOne(\App\Models\Accounts\Receipt::class, 'lead_id')->latestOfMany('receipt_date'); // or 'created_at' if receipt_date doesn't work well
    }

    public function stars()
    {
        return $this->hasMany(LeadStar::class, 'lead_id');
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'lead_stars', 'lead_id', 'user_id')->withTimestamps();
    }

    /**
     * Notify all users who favorited this lead about an activity.
     */
    public function notifyFavoriters($activityType, $message, $performedByUser = null)
    {
        if (!$performedByUser) {
            $performedByUser = \Illuminate\Support\Facades\Auth::user();
        }

        $performedByUserId = $performedByUser ? $performedByUser->id : null;
        $performedByUserName = $performedByUser ? $performedByUser->name : 'System';

        $usersToNotify = $this->favoritedBy()->where('user_id', '!=', $performedByUserId)->get();

        if ($usersToNotify->isNotEmpty()) {
            \Illuminate\Support\Facades\Notification::send($usersToNotify, new \App\Notifications\LeadActivityNotification(
                $this,
                $activityType,
                $message,
                $performedByUserId,
                $performedByUserName
            ));
        }
    }
}
