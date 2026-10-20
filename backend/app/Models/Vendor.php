<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Vendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'company_name',
        'industry_id',
        'country_id',
        'state_id',
        'city_id',
        'pincode',
        'address',
        'status',
        'referral_code',
        'referred_by_vendor_id',
        'remarks',
        'monthly_target',
        'target_type',
        'target_start_date',
        'target_end_date',
    ];


    public static function generateUniqueReferralCode(): string
    {
        do {
            $code = 'VNDR-' . strtoupper(Str::random(6));
        } while (static::where('referral_code', $code)->exists());

        return $code;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'referred_by_vendor_id');
    }

    public function referredVendors(): HasMany
    {
        return $this->hasMany(Vendor::class, 'referred_by_vendor_id');
    }

    public function referralInvitations(): HasMany
    {
        return $this->hasMany(VendorReferralInvitation::class);
    }

    public function industry(): BelongsTo
    {
        return $this->belongsTo(MasterIndustry::class, 'industry_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function vendorPercentage()
    {
        return $this->hasOne(VendorPercentage::class);
    }

    public function targets(): HasMany
    {
        return $this->hasMany(VendorTarget::class)->orderBy('start_date', 'asc');
    }
}
