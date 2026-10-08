<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tour extends Model
{
    use HasFactory;

    protected $fillable = [
        'box_id',
        'name',
        'city',
        'territory',
        'status',
    ];

    public function staff()
    {
        return $this->belongsToMany(BoxUser::class, 'tour_user', 'tour_id', 'boxuser_id');
    }

    public function customers()
    {
        return $this->belongsToMany(Lead::class, 'lead_tour', 'tour_id', 'lead_id');
    }
}
