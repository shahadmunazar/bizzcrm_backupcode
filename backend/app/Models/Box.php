<?php
namespace App\Models;

use App\Models\BoxUser;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use App\Models\DealStatus;
use App\Models\DealPeriod;
use App\Models\DealSource;
use App\Models\DealPriority;
use App\Models\DealTag;
use App\Models\LetterType;
use App\Models\AllowanceType;
use App\Models\ExpenseCategory;
use App\Models\ExpenseApprovalRule;

class Box extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    protected static function booted()
    {
        static::created(function ($box) {
            $defaultLetterTypes = [
                'Address Proof Letter',
                'Confirmation Letter',
                'Employment Letter',
                'Increment Letter',
                'Internship Completion Letter',
                'Internship Offer Letter',
                'Leave Approval Letter',
                'Offer Letter',
                'Probation Extension Letter',
                'Promotion Letter'
            ];

            foreach ($defaultLetterTypes as $type) {
                LetterType::create([
                    'box_id' => $box->id,
                    'name' => $type
                ]);
            }

            // Default Expense Categories
            $defaultExpenseCategories = [
                'Travel',
                'Food & Dining',
                'Accommodation',
                'Office Supplies',
                'Miscellaneous'
            ];
            foreach ($defaultExpenseCategories as $category) {
                ExpenseCategory::create([
                    'box_id' => $box->id,
                    'name' => $category,
                    'is_active' => true
                ]);
            }

            // Default Allowance Types
            $defaultAllowanceTypes = [
                ['name' => 'Daily Allowance (DA)', 'calculation_type' => 'Per Day', 'amount' => 500, 'description' => 'Standard daily allowance for outstation duties.'],
                ['name' => 'Travel Allowance (TA)', 'calculation_type' => 'Per KM', 'amount' => 10, 'description' => 'Per kilometer compensation for official travel.'],
                ['name' => 'Mobile/Internet Allowance', 'calculation_type' => 'Fixed', 'amount' => 1000, 'description' => 'Monthly fixed allowance for mobile and internet usage.']
            ];
            foreach ($defaultAllowanceTypes as $allowance) {
                AllowanceType::create(array_merge($allowance, [
                    'box_id' => $box->id,
                    'is_active' => true
                ]));
            }

            // Default Approval Rules
            $defaultApprovalRules = [
                ['min_amount' => 0, 'max_amount' => 5000, 'required_roles' => ['manager']],
                ['min_amount' => 5000.01, 'max_amount' => null, 'required_roles' => ['manager', 'admin']]
            ];
            foreach ($defaultApprovalRules as $rule) {
                ExpenseApprovalRule::create(array_merge($rule, [
                    'box_id' => $box->id,
                    'is_active' => true
                ]));
            }        });
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'superadmin_id',
        'name',
        'slug',
        'referral_code',
        'domain',
        'industry',
        'master_industry_id',
        'address',
        'pincode',
        'phone',
        'mobile',
        'contact_name',
        'country_id',
        'state_id',
        'lead_required_field',
        'city_id',
        'reminder_before',
        'company_email',
        'relaxation_time',
        'company_phone',
        'fax_number',
        'company_logo',
        'auto_task_close',
        'bulk_lead_assignment',
        'lead_assignment',
        'change_lead_assigned_if_admin',
        'change_lead_status_if_admin',
        'change_lead_source_if_admin',
        'activity_reminder',
        'allow_duplicate_leads',
        'allow_duplicate_leads_auto',
        'dynamic_employee_id',
        'change_dynamic_prefix',
        'custom_employee_id',
        'facebook_api_params',
        'facebook_access_token',
        'whatsapp_token',
        'whatsapp_api_token',
        'whatsapp_webhook_url',
        'attendace_module_date',
        'attendace_module_from_date',
        'attendace_module_to_date',
        'leave_credit_mode',
        'leave_credit_time',
        'start_day_of_every_month',
        'last_day_of_every_month',
        'leave_credit_selected_days',
        'working_days',
        'day_start_time',
        'day_end_time',
        'currency_code',
        'currency_symbol',
        'ai_credits',
        'whatsapp_business_id',
        'whatsapp_phone_id',
        'whatsapp_business_portfolio_id',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    /**
     * The attributes that should be appended to arrays.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'default_email',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'superadmin_id',
        'whatsapp_token'       => 'array',
        'facebook_api_params'  => 'array',
        'ip_address'           => 'array',
        'working_days'         => 'array',
        'round_robin_users'    => 'array',
        'admin_mail_users'     => 'array',
        // 'reminder_before'      => 'integer',
        'params'               => 'array',
        'auto_task_close_days' => 'integer',
        'can_use_admin_mail'   => 'boolean',
    ];

    /**
     * Get the superadmin that owns the box.
     */
    public function superadmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'superadmin_id');
    }

    /**
     * Get the users for the box.
     */
    public function users(): HasMany
    {
        return $this->hasMany(BoxUser::class);
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

    /**
     * Get the params attribute.
     *
     * @param string|null $value
     * @return array<string, mixed>
     */
    public function getParamsAttribute(?string $value): array
    {
        $params = $value ? json_decode($value, true) : [];

        return [
            'mail_event' => $params['mail_event'] ?? 'none',
            'duplicate'  => $params['duplicate'] ?? 'no',
            ...$params,
        ];
    }

    /**
     * Get the default email attribute.
     *
     * @return string|null
     */
    public function getDefaultEmailAttribute(): ?string
    {
        $user = Auth::user();

        if (! $user || ! $this->can_use_admin_mail) {
            return null;
        }

        if ($this->admin_mail_users === null || in_array($user->id, $this->admin_mail_users)) {
            $mainUser = $this->users()
                ->where('user_id', $this->superadmin_id)
                ->first();

            if (! $mainUser || ! $mainUser->email_connected) {
                return null;
            }

            if ($mainUser->email_type === 'gmail') {
                $jsonPath = Storage::path("gmail/tokens/user-json-{$this->superadmin_id}.json");

                if (file_exists($jsonPath)) {
                    $jsonData = json_decode(file_get_contents($jsonPath));
                    return $jsonData->email ?? null;
                }
            }

            return $mainUser->email_outgoing_user;
        }

        return null;
    }

    //for Box Crm Settings

    public function sources()
    {
        return $this->hasMany(CrmSource::class, 'box_id');
    }

    public function calls()
    {
        return $this->hasMany(Call::class, 'box_id');
    }

    public function contactStages()
    {
        return $this->hasMany(ContactStage::class, 'box_id');
    }

    public function industries()
    {
        return $this->hasMany(Industry::class, 'box_id');
    }

    public function lostReasons()
    {
        return $this->hasMany(LostReason::class, 'box_id');
    }

    //for department relation

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

//for system fields relation

    public function systemFields()
    {
        return $this->hasMany(SystemField::class, 'box_id');
    }

    public function activityTypes()
    {
        return $this->hasMany(ActivityType::class);
    }

    public function leaveTypes()
    {
        return $this->hasMany(LeaveType::class);
    }

}
