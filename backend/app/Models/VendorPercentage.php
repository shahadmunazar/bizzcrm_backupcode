<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorPercentage extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'percentage',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
