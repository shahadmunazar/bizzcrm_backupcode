<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class UserColumnPreference extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $table = 'user_column_preferences';

    protected $fillable = [
        'user_id',
        'page_identifier',
        'column_preferences'
    ];

    protected $casts = [
        'column_preferences' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
