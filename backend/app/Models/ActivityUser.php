<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class ActivityUser extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $table = 'activity_users';

    protected $fillable = [
        'activity_id',
        'user_id',
        'status',
        'completed_date',
    ];

    protected $casts = [
        'completed_date' => 'datetime',
    ];

    // Relationships
    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}



