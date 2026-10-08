<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Builder;

class JobApplicant extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $guarded = ['id'];

    protected $casts = [
        'dob' => 'date',
        'expected_joining_date' => 'date',
        'last_working_date' => 'date',
        'interview_date' => 'date',
        'application_date' => 'datetime',
        'education_details' => 'array',
        'experience_details' => 'array',
    ];

    /**
     * Scope a query to only include records of a given box ID.
     */
    public function scopeOfBox(Builder $query, $boxId)
    {
        return $query->where('box_id', $boxId);
    }

    /**
     * Get the user who created the record.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the record.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
