<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Project extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;

    protected $guarded = [];

    protected static function booted()
    {
        parent::booted();

        static::created(function ($project) {
            \App\Jobs\GenerateAiInsightJob::dispatch($project, 'project');
        });
    }

    public function projectType()
    {
        return $this->belongsTo(ProjectType::class, 'project_type_id');
    }

    public function projectCategory()
    {
        return $this->belongsTo(ProjectCategory::class, 'project_category_id');
    }

    public function teamMembers()
    {
        return $this->belongsToMany(User::class, 'project_user')->withPivot('role')->withTimestamps();
    }

    public function box()
    {
        return $this->belongsTo(Box::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function contacts()
    {
        return $this->hasMany(ProjectContact::class);
    }
}