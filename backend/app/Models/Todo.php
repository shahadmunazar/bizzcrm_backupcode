<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Box;
use App\Models\BoxUser;

class Todo extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'box_id',
        'box_user_id',
        'title',
        'category',
        'responsible_persons',
        'start_date',
        'due_date',
        'tags',
        'priority',
        'status',
        'description',
    ];

    protected $casts = [
        'responsible_persons' => 'array',
        'start_date' => 'datetime',
        'due_date' => 'datetime',
    ];

    public function box()
    {
        return $this->belongsTo(Box::class);
    }

    public function creator()
    {
        return $this->belongsTo(BoxUser::class, 'box_user_id');
    }
}
