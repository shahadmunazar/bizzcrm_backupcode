<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use App\Models\BoxUser;
use App\Models\Box;
use App\Models\UserSubscription;
use Carbon\Carbon;

class SubscriptionHelper
{
    protected static $cachedFeatures = null;
    protected static $cachedActiveWaSubscription = null;

    public static function clearCache()
    {
        self::$cachedFeatures = null;
        self::$cachedActiveWaSubscription = null;
    }

    /**
     * Get the current active box context.
     */
    private static function getCurrentBox($user)
    {
        $boxSlug = request()->route('box_slug') ?? request('box_slug') ?? session('box_slug');
        // dd($boxSlug);
        if ($boxSlug) {
            return Box::where('slug', $boxSlug)->first();
        }
        
        $boxUserEntry = BoxUser::where('user_id', $user->id)->first();
        if ($boxUserEntry) {
            return Box::find($boxUserEntry->box_id);
        }
        
        return null;
    }

    public static function getActiveSubscription($user = null)
    {
        $user = $user ?? Auth::user();
        
        if (!$user) {
            return null;
        }

        $ownerIds = [$user->id];
        $box = self::getCurrentBox($user);

        if ($box) {
            if ($box->superadmin_id) {
                $ownerIds[] = $box->superadmin_id;
            }
            
            $boxUserIds = BoxUser::where('box_id', $box->id)
                ->pluck('user_id')
                ->toArray();
            
            $ownerIds = array_merge($ownerIds, $boxUserIds);
        }

        $ownerIds = array_unique($ownerIds);

        if (empty($ownerIds)) {
            return null;
        }

        return UserSubscription::whereIn('user_id', $ownerIds)
            ->whereHas('plan', function ($query) {
                $query->where('plan_type', 'subscription');
            })
            ->where(function ($query) {
                $query->whereIn('status', ['active', 'past_due', 'created', 'authenticated'])
                      ->orWhere(function ($subQuery) {
                          $subQuery->where('status', 'cancelled')
                                   ->where(function ($q) {
                                       $q->whereNull('next_billing_at')->orWhere('next_billing_at', '>=', Carbon::now());
                                   });
                      });
            })
            ->latest()
            ->first();
    }

    public static function getActiveWhatsappSubscription($user = null)
    {
        $user = $user ?? Auth::user();
        
        if (!$user) {
            return null;
        }

        $ownerIds = [$user->id];
        $box = self::getCurrentBox($user);

        if ($box) {
            if ($box->superadmin_id) {
                $ownerIds[] = $box->superadmin_id;
            }
            
            $boxUserIds = BoxUser::where('box_id', $box->id)
                ->pluck('user_id')
                ->toArray();
            
            $ownerIds = array_merge($ownerIds, $boxUserIds);
        }

        $ownerIds = array_unique($ownerIds);

        if (empty($ownerIds)) {
            return null;
        }

        return UserSubscription::whereIn('user_id', $ownerIds)
            ->whereHas('plan', function ($query) {
                $query->where('plan_type', 'whatsapp');
            })
            ->where(function ($query) {
                $query->whereIn('status', ['active', 'past_due', 'created', 'authenticated'])
                      ->orWhere(function ($subQuery) {
                          $subQuery->where('status', 'cancelled')
                                   ->where(function ($q) {
                                       $q->whereNull('next_billing_at')->orWhere('next_billing_at', '>=', Carbon::now());
                                   });
                      });
            })
            ->latest()
            ->first();
    }

    public static function getAllActiveSubscriptions($user = null)
    {
        $user = $user ?? Auth::user();
        if (!$user) return collect();

        $ownerIds = [$user->id];
        $box = self::getCurrentBox($user);

        if ($box) {
            if ($box->superadmin_id) $ownerIds[] = $box->superadmin_id;
            $boxUserIds = BoxUser::where('box_id', $box->id)->pluck('user_id')->toArray();
            $ownerIds = array_merge($ownerIds, $boxUserIds);
        }
        $ownerIds = array_unique($ownerIds);
        if (empty($ownerIds)) return collect();

        return UserSubscription::with('plan')->whereIn('user_id', $ownerIds)
            ->where(function ($query) {
                $query->whereIn('status', ['active', 'past_due', 'created', 'authenticated'])
                      ->orWhere(function ($subQuery) {
                          $subQuery->where('status', 'cancelled')
                                   ->where(function ($q) {
                                       $q->whereNull('next_billing_at')->orWhere('next_billing_at', '>=', Carbon::now());
                                   });
                      });
            })
            ->get();
    }

    public static function getCombinedFeatures($user = null)
    {
        if (self::$cachedFeatures !== null) {
            return self::$cachedFeatures;
        }

        $subscriptions = self::getAllActiveSubscriptions($user);
        $baseSub = null;
        $additionalPlans = [];

        foreach ($subscriptions as $sub) {
            if ($sub->plan && $sub->plan->plan_type === 'subscription') {
                if (!$baseSub || $sub->created_at > $baseSub->created_at) {
                    $baseSub = $sub; 
                }
            } elseif ($sub->plan && in_array($sub->plan->plan_type, ['topup', 'whatsapp'])) {
                $additionalPlans[] = $sub;
            }
        }

        $combinedFeatures = [];

        if ($baseSub) {
            $features = $baseSub->plan->features ?? [];
            foreach ($features as $key => $value) {
                $valueStr = strtolower((string)$value);
                if (is_numeric($value)) {
                    $combinedFeatures[$key] = (float)$value;
                } elseif (in_array($valueStr, ['✅', 'yes', 'checked', 'true', '1'])) {
                    $combinedFeatures[$key] = true;
                } elseif (in_array($valueStr, ['❌', 'no', 'unchecked', 'false', '0'])) {
                    $combinedFeatures[$key] = false;
                } else {
                    $combinedFeatures[$key] = $value;
                }
            }
        }

        foreach ($additionalPlans as $sub) {
            $features = $sub->plan->features ?? [];
            foreach ($features as $key => $value) {
                $valueStr = strtolower((string)$value);
                if (is_numeric($value)) {
                    $combinedFeatures[$key] = ($combinedFeatures[$key] ?? 0) + (float)$value;
                } elseif (in_array($valueStr, ['✅', 'yes', 'checked', 'true', '1'])) {
                    $combinedFeatures[$key] = true;
                }
            }
        }

        
        self::$cachedFeatures = $combinedFeatures;
        return $combinedFeatures;
    }

    public static function hasFeature($featureName)
    {
        $user = Auth::user();
        if (!$user) return false;

        if ($user->hasRole('superadmin') || $user->id == 1) return true;

        if ($featureName === 'WhatsApp API Integration') {
            return self::getActiveWhatsappSubscription($user) !== null;
        }

        $features = self::getCombinedFeatures($user);

        if (array_key_exists($featureName, $features)) {
            $value = $features[$featureName];
            if (is_numeric($value) && $value > 0) return true;
            if ($value === true) return true;
            if (is_string($value)) {
                $valLower = strtolower(trim($value));
                if (in_array($valLower, ['❌', 'no', 'unchecked', 'false', '0'])) {
                    return false;
                }
                // Features like '2 APIs Included', '5 APIs', 'Unlimited APIs' mean the feature is available
                if (str_contains($valLower, 'included') || str_contains($valLower, 'unlimited') || str_contains($valLower, 'api')) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get numeric limit for a feature (e.g. No of Users: 3, Lead Integration: 2, Unlimited: 999999).
     */
    public static function getFeatureLimit(string $featureName, $default = 0): int
    {
        $user = Auth::user();
        if (!$user) return $default;

        if ($user->hasRole('superadmin') || $user->id == 1) return 999999;

        $features = self::getCombinedFeatures($user);
        if (!isset($features[$featureName])) return $default;

        $val = $features[$featureName];
        if (is_numeric($val)) return (int)$val;

        $valLower = strtolower((string)$val);
        if (str_contains($valLower, 'unlimited')) {
            return 999999;
        }

        if (preg_match('/(\d+)/', (string)$val, $matches)) {
            return (int)$matches[1];
        }

        return $default;
    }

    /**
     * Check if the current user has any valid subscription
     */
    public static function hasValidSubscription()
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        if ($user->hasRole('superadmin') || $user->id == 1) {
            return true;
        }

        return self::getActiveSubscription($user) !== null || self::getActiveWhatsappSubscription($user) !== null;
    }

    /**
     * Get the WhatsApp Agent limit for the active WhatsApp plan.
     */
    public static function getWhatsAppAgentLimit($user = null)
    {
        $user = $user ?? Auth::user();
        if (!$user) return 0;

        $waSubscription = self::getActiveWhatsappSubscription($user);
        if (!$waSubscription || !$waSubscription->plan) {
            return 0;
        }

        // You could parse the features array or rely on a specific feature name
        // For now, based on standard plans returning "1 Owner + 5 FREE Agents included"
        $features = $waSubscription->plan->features ?? [];
        foreach ($features as $key => $value) {
            if (str_contains(strtolower($key), 'agent') && preg_match('/(\d+)\s*free\s*agents/i', $key, $matches)) {
                return (int) $matches[1] + 1; // e.g. 1 owner + 5 agents = 6
            }
        }
        
        // Default limit if plan exists but we couldn't parse it
        return 6;
    }
}
