<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BizzAuditable;

class CustomerCheckIn extends Model
{
    use HasFactory, SoftDeletes, BizzAuditable;

    protected $fillable = [
        'customer_id',
        'box_id',
        'boxuser_id',
        'remarks',
        'latitude',
        'longitude',
        'images',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(Lead::class, 'customer_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
