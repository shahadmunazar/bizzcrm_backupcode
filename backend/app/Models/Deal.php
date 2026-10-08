<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Deal extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;

    protected $guarded = [];

    protected static function booted()
    {
        parent::booted();

        static::created(function ($deal) {
            \App\Jobs\GenerateAiInsightJob::dispatch($deal, 'deal');
        });
    }

    protected $casts = [
        'assignees' => 'array',
        'tags' => 'array',
    ];

    public function box()
    {
        return $this->belongsTo(Box::class);
    }

    public function dealStatus()
    {
        return $this->belongsTo(DealStatus::class, 'status_id');
    }

    public function dealPeriod()
    {
        return $this->belongsTo(DealPeriod::class, 'period_id');
    }

    public function dealSource()
    {
        return $this->belongsTo(DealSource::class, 'source_id');
    }

    public function dealPriority()
    {
        return $this->belongsTo(DealPriority::class, 'priority_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function contact()
    {
        return $this->belongsTo(ProjectContact::class, 'contact_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function pipeline()
    {
        return $this->belongsTo(Pipeline::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }
}
