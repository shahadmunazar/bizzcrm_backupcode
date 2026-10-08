<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'box_id',
        'transaction_amount',
        'commission_percentage',
        'commission_amount',
        'status',
        'paid_at',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function box()
    {
        return $this->belongsTo(Box::class);
    }
}
