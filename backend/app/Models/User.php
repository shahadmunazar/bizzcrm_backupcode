<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use App\Helpers\SubscriptionHelper;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements AuditableContract
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, Auditable, SoftDeletes;

    protected $guard_name = 'web';


    protected $fillable = [
        'name',
        'email',
        'mobile',
        'password',
        'otp',
        'otp_expires_at',
        'token_expires_at',
        'team_id',
        'box_id',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_expires_at'    => 'datetime',
        'token_expires_at'  => 'datetime',
        'password'          => 'hashed',
    ];

    public function employee()
    {
        return $this->hasOne(BoxUser::class);
    }

    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class);
    }

    public function box_user(): BelongsTo
    {
        return $this->belongsTo(BoxUser::class);
    }

    public function box_user_new()
    {
        return $this->hasOne(BoxUser::class);
    }

    public function boxes()
    {
        return $this->belongsToMany(Box::class, 'box_users', 'user_id', 'box_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function vendor()
    {
        return $this->hasOne(Vendor::class);
    }
    public function getSelfAndChainMembers()
    {
        $allowrdUsersId = [$this->id];

        // Fetch subordinates strictly using Reporting Manager hierarchy
        $boxUser = \App\Models\BoxUser::where('user_id', $this->id)->first();
        if ($boxUser) {
            $subordinateIds = [];
            \App\Models\BoxUser::getRecursiveSubordinates($boxUser->box_id, $boxUser->id, $subordinateIds);
            
            if (!empty($subordinateIds)) {
                $subUserIds = \App\Models\BoxUser::whereIn('id', $subordinateIds)->pluck('user_id')->toArray();
                $allowrdUsersId = array_merge($allowrdUsersId, $subUserIds);
            }
        }

        // Fallback to legacy team logic if applicable
        if($this->team){
          $child = [];
          if (method_exists($this->team, 'getDescendants')) {
              foreach ($this->team->getDescendants() as $childTeam) {
                $child[] = $childTeam->id;
              }
          }
          
          if (!empty($child) && class_exists('\App\Models\TeamUser')) {
             $childUsers = \App\Models\TeamUser::select('user_id')->whereIn('team_id',$child)->get();
             foreach ($childUsers as $childUser) {
                $allowrdUsersId[]=$childUser['user_id'];
             }
          }
        }
        return array_values(array_unique($allowrdUsersId));
    }

    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function activeSubscription()
    {
        return SubscriptionHelper::getActiveSubscription($this);
    }

    public function starredLeads()
    {
        return $this->belongsToMany(Lead::class, 'lead_stars', 'user_id', 'lead_id')->withTimestamps();
    }
}
