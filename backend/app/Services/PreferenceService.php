<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\UserPreference;
use App\Models\BoxUser;

class PreferenceService
{
    private function resolveBoxId(?int $boxId = null): ?int
    {
        if ($boxId) return $boxId;
        
        $user = Auth::user();
        if (!$user) return null;

        $boxUser = BoxUser::where('user_id', $user->id)->first();
        return $boxUser ? $boxUser->box_id : null;
    }

    /**
     * Get the resolved preference for a specific module and key.
     * Hierarchy: Session > User Preference > Role Config > Global Default
     */
    public function getPreference(string $module, string $key, $default = null, ?int $boxId = null)
    {
        $boxId = $this->resolveBoxId($boxId);
        $sessionKey = "preferences.{$boxId}.{$module}.{$key}";

        // 1. Session Override
        if (Session::has($sessionKey)) {
            return Session::get($sessionKey);
        }

        // 2. User Preference (Database)
        $user = Auth::user();
        if ($user && $boxId) {
            $userPref = UserPreference::where('user_id', $user->id)->where('box_id', $boxId)->first();
            if ($userPref && isset($userPref->preferences[$module][$key])) {
                return $userPref->preferences[$module][$key];
            }
        }

        // 3. Global Default
        $globalKey = "crm.preferences.{$module}.{$key}";
        return config($globalKey, $default);
    }

    /**
     * Get all merged preferences for a module.
     */
    public function getModulePreferences(string $module, ?int $boxId = null): array
    {
        $boxId = $this->resolveBoxId($boxId);
        $merged = config("crm.preferences.{$module}", []);

        $user = Auth::user();
        if ($user && $boxId) {
            $userPref = UserPreference::where('user_id', $user->id)->where('box_id', $boxId)->first();
            if ($userPref && isset($userPref->preferences[$module])) {
                $merged = array_merge($merged, $userPref->preferences[$module]);
            }
        }

        $sessionOverrides = Session::get("preferences.{$boxId}.{$module}", []);
        $merged = array_merge($merged, $sessionOverrides);

        return $merged;
    }

    /**
     * Save a specific preference permanently to the database.
     */
    public function setPreference(string $module, string $key, $value, ?int $boxId = null): void
    {
        $boxId = $this->resolveBoxId($boxId);
        $user = Auth::user();
        if (!$user || !$boxId) return;

        $userPref = UserPreference::firstOrCreate(['user_id' => $user->id, 'box_id' => $boxId]);
        $preferences = $userPref->preferences ?? [];

        if (!isset($preferences[$module])) {
            $preferences[$module] = [];
        }

        $preferences[$module][$key] = $value;
        
        $userPref->preferences = $preferences;
        $userPref->save();
    }
}
