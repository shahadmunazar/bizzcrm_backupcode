<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorReferralInvitation extends Model
{
    use HasFactory;

    protected $table = 'vendor_referral_invitations';

    protected $fillable = [
        'vendor_id',
        'recipient_email',
        'referral_code',
        'referral_url',
        'message',
        'status',
        'registered_vendor_id',
        'registered_box_id',
        'registered_at',
        'last_sent_at',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'last_sent_at' => 'datetime',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function registeredVendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'registered_vendor_id');
    }

    public function registeredBox(): BelongsTo
    {
        return $this->belongsTo(Box::class, 'registered_box_id');
    }
}
