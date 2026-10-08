<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PackageFeature extends Model implements AuditableContract
{
    use Auditable;
    protected $fillable = ['name', 'slug', 'is_active', 'feature_type'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($feature) {
            if (!$feature->slug) {
                $feature->slug = Str::slug($feature->name);
            }
        });
    }
}
