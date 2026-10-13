<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadStar extends Model
{
    protected $fillable = ['user_id', 'lead_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
