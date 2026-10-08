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

        // Assuming 'team' relationship points to a Team model which supports getDescendants (nested sets?)
        if($this->team){
          $child = [];
          
          // Assuming the Team model uses a trait or has a method getDescendants
          // The user code referenced $this->teams which might be wrong, they probably meant team() relation or property
          // The user code: foreach ($this->teams->team->getDescendants() as $childTeam)
          // It seems they assume the user has a "teams" relation that points to something that has a "team" relation.
          // Adjusting to standard 'team' relation.
          
          if (method_exists($this->team, 'getDescendants')) {
              foreach ($this->team->getDescendants() as $childTeam) {
                $child[] = $childTeam->id;
              }
          }
          
          // Assuming User is linked to Team via team_id in users table, OR via TeamUser pivot.
          // User code uses TeamUser::select('user_id')...
          if (!empty($child)) {
             // We can check if TeamUser class exists, or use DB facade if not.
             // Using \App\Models\TeamUser if exists, or just query users table if simpler.
             // However, strictly following user code structure:
             $childUsers = \App\Models\TeamUser::select('user_id')->whereIn('team_id',$child)->get();
             foreach ($childUsers as $childUser) {
                $allowrdUsersId[]=$childUser['user_id'];
             }
          }
        }
        return $allowrdUsersId;
    }

    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function activeSubscription()
    {
        return SubscriptionHelper::getActiveSubscription($this);
    }
}
