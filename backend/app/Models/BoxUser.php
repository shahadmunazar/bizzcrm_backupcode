<?php
namespace App\Models;

use App\Models\Box;
use App\Models\User;
use App\Models\EmployeeType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Traits\HasPermissions;
use App\Traits\BizzAuditable;

class BoxUser extends Model implements AuditableContract
{
    use SoftDeletes, Auditable, HasRoles, HasPermissions, BizzAuditable;

    protected $fillable = [
        'id',
        'box_id',
        'user_id',
        'added_by',
        'email_connected',
        'email_type',
        'email_incoming_user',
        'email_incoming_password',
        'email_outgoing_user',
        'email_outgoing_password',
        'email_same_user',
        'email_incoming',
        'email_incoming_port',
        'email_outgoing',
        'email_outgoing_port',
        'email_allow_non_secure_certificate',
        'encryption',
        'email_token',
        'email_refresh_token',
        'allowed_ips',
        'signature',
        'exlude_ip_restriction',
        'auto_responder',
        'auto_responder_text',
        'phone',
        'working_days',
        'day_start_time',
        'day_end_time',
        'last_lead',
        'exclude_round_robin',
        'user_box_timing',
        'gmail_app',
        'status',
        'marriage_date',
        'auto_responser_subject',
        'error_log',
        'auto_responder_attachments',
        'first_name',
        'last_name',
        'username',
        'designation',
        'about',
        'report_office',
        'company',
        'employee_id',
        'staff_role',
        'emp_type',
        'pan_number',
        'relaxation_time',
        'joining_date',
        'department',
        'profile_image',
        'assign_tour',
        'assign_customer',
        'gender',
        'birthday',
        'address',
        'passport_no',
        'profile_image_path',
        'passport_exp_date',
        'nationality',
        'religion',
        'pf_no',
        'esi_no',
        'resignation_date',
        'marital_status',
        'Employment_of_spouse',
        'no_of_children',
        'shift_id',
        'work_mode_id',
        'working_web_days',
        'reporting_manager_id',
        'can_login_anytime',
        'call_logs',
    ];

    protected $casts = [
        'allowed_ips'                => 'array',
        'working_days'               => 'array',
        'auto_responder_attachments' => 'array',
        'exlude_ip_restriction'      => 'boolean',
        'can_login_anytime'          => 'boolean',
        'exclude_round_robin'        => 'boolean',
        'call_logs'                  => 'boolean',
        // 'user_box_timing'            => 'boolean',
        'joining_date'               => 'date',
        'birthday'                   => 'date',
        'passport_exp_date'          => 'date',
        'last_lead'                  => 'datetime',
    ];

    // Relationships

    public function latestSalary()
    {
        return $this->hasOne(Salary::class)->latestOfMany();
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    public function workMode()
    {
        return $this->belongsTo(WorkModeSetting::class, 'work_mode_id');
    }

    public function machineMappings()
    {
        return $this->hasMany(UserMachineMapping::class, 'box_user_id');
    }

    public function assignedOffices()
    {
        return $this->hasMany(AssignEmployeesOffice::class, 'box_user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class, 'box_id');
    }

    // Optional: Return formatted date
    public function getCreatedAtAttribute($value): string
    {
        return Carbon::parse($value)->format('d-m-Y h:i A');
    }

    public function bankDetails()
    {
        return $this->hasMany(EmployeeBankDetail::class, 'box_user_id');
    }

    public function bankStatutory()
    {
        return $this->hasOne(EmployeeBankStatutory::class, 'box_user_id');
    }

    public function educations()
    {
        return $this->hasMany(EmployeeEducation::class, 'box_user_id');
    }

    public function experiences()
    {
        return $this->hasMany(EmployeeExperience::class, 'box_user_id')->orderBy('start_date', 'desc');
    }

    public function families()
    {
        return $this->hasMany(Employeefamily::class, 'box_user_id');
    }

    public function emergencyContacts()
    {
        return $this->hasMany(EmployeeEmergencyContact::class, 'box_user_id');
    }
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function assignedAssets()
    {
        return $this->hasMany(AssignedAsset::class, 'box_user_id');
    }

    // BoxUser.php

// BoxUser.php

    public function departmentRelation()
    {
        return $this->belongsTo(Department::class, 'department');
    }

    public function designationRelation()
    {
        return $this->belongsTo(Team::class, 'designation');
    }

    public function reportingManager()
    {
        return $this->belongsTo(BoxUser::class, 'reporting_manager_id');
    }

    public function leaveTransactions()
    {
        return $this->hasMany(LeaveTransaction::class, 'box_user_id');
    }

    public function employeeType()
    {
        return $this->belongsTo(EmployeeType::class, 'emp_type');
    }

    


    /**
     * Get all subordinate IDs recursively
     */
    public static function getAllSubordinateIds($boxId, $userId, $boxUser)
    {
        $allIds = [];
        if ($boxUser && isset($boxUser->id)) {
            self::getRecursiveSubordinates($boxId, $boxUser->id, $allIds);
        }

        // Also check if the user is a team leader or department leader and include those users
        $departmentIds = Department::where('user_id', $userId)->pluck('id')->toArray();
        $teamIds = Team::where('user_id', $userId)->pluck('id')->toArray();

        if (!empty($departmentIds) || !empty($teamIds)) {
            $additionalSubordinates = self::where('box_id', $boxId)
                ->where(function ($q) use ($departmentIds, $teamIds) {
                    if (!empty($departmentIds)) {
                        $q->orWhereIn('department', $departmentIds);
                    }
                    if (!empty($teamIds)) {
                        $q->orWhereIn('designation', $teamIds);
                    }
                })
                ->pluck('id')
                ->toArray();

            foreach ($additionalSubordinates as $subId) {
                if (!in_array($subId, $allIds)) {
                    $allIds[] = $subId;
                    self::getRecursiveSubordinates($boxId, $subId, $allIds);
                }
            }
        }

        return array_unique($allIds);
    }

    public static function getRecursiveSubordinates($boxId, $managerId, &$allIds)
    {
        $directReports = self::where('box_id', $boxId)
            ->where('reporting_manager_id', $managerId)
            ->pluck('id')
            ->toArray();

        foreach ($directReports as $reportId) {
            if (!in_array($reportId, $allIds)) {
                $allIds[] = $reportId;
                self::getRecursiveSubordinates($boxId, $reportId, $allIds);
            }
        }
    }
}
