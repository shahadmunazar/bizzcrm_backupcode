<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserCurrentLocation;
use App\Models\UserLocationHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class LocationTrackingApiController extends Controller
{
    /**
     * Calculate distance between two coordinate points in meters using Haversine formula.
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // in meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Store the current location of the user and save it to history if applicable.
     */
    public function store(Request $request, $box_slug)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'required|numeric',
            'timestamp' => 'required|date',
            'action' => 'nullable|in:ping,start,end',
            'battery_level' => 'nullable|integer|between:0,100',
            'is_charging' => 'nullable|boolean',
            'network_status' => 'nullable|string|max:50',
            'network_type' => 'nullable|string|max:50',
            'last_online_at' => 'nullable|date',
            'last_offline_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid location data',
                'errors' => $validator->errors()
            ], 422);
        }

        $action = $request->input('action', 'ping');
        $maxAccuracy = config('tracking.max_accuracy', 100);
        if ($request->accuracy > $maxAccuracy && $action == 'ping') {
            return response()->json([
                'success' => false,
                'message' => 'Location accuracy is too low and was discarded'
            ], 400);
        }

        $recordedAt = Carbon::parse($request->timestamp);
        if ($recordedAt->isFuture()) {
            return response()->json([
                'success' => false,
                'message' => 'Future timestamps are not allowed'
            ], 400);
        }

        $userId = Auth::id();
        $box = \App\Models\Box::where('slug', $box_slug)->first();
        if (!$box) {
            return response()->json(['success' => false, 'message' => 'Box not found'], 404);
        }

        $boxUser = \App\Models\BoxUser::where('user_id', $userId)->where('box_id', $box->id)->first();
        
        if (!$boxUser || !$boxUser->is_location_tracking) {
            return response()->json([
                'success' => false,
                'message' => 'Location tracking is disabled for your account by the administrator.'
            ], 403);
        }
        
        $boxUserId = $boxUser->id;

        $currentLocation = UserCurrentLocation::where('user_id', $userId)
                                ->where('box_id', $box->id)
                                ->first();

        $shouldSaveHistory = true;

        // If this is just a regular ping, we check distance and time
        if ($currentLocation && $action === 'ping') {
            $distance = $this->calculateDistance(
                $currentLocation->latitude, 
                $currentLocation->longitude, 
                $request->latitude, 
                $request->longitude
            );
            $timeDiff = $recordedAt->diffInSeconds($currentLocation->recorded_at);

            $minDistance = config('tracking.distance_meters', 50);
            $minTime = config('tracking.interval_seconds', 60);

            if ($distance < $minDistance && $timeDiff < $minTime) {
                $shouldSaveHistory = false;
            }
        }

        $deviceStatusData = [
            'battery_level' => $request->battery_level,
            'is_charging' => $request->is_charging,
            'network_status' => $request->network_status,
            'network_type' => $request->network_type,
            'last_online_at' => $request->last_online_at ? Carbon::parse($request->last_online_at) : null,
            'last_offline_at' => $request->last_offline_at ? Carbon::parse($request->last_offline_at) : null,
        ];

        // Upsert current location
        UserCurrentLocation::updateOrCreate(
            ['user_id' => $userId, 'box_id' => $box->id],
            array_merge([
                'boxuser_id' => $boxUserId,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'accuracy' => $request->accuracy,
                'status' => 'active',
                'recorded_at' => $recordedAt,
                'updated_by' => $userId, 
                'added_by' => $userId, 
            ], $deviceStatusData)
        );

        if ($shouldSaveHistory || $action === 'start' || $action === 'end') {
            UserLocationHistory::create(array_merge([
                'box_id' => $box->id,
                'user_id' => $userId,
                'boxuser_id' => $boxUserId,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'accuracy' => $request->accuracy,
                'status' => 'active',
                'action' => $action,
                'recorded_at' => $recordedAt,
                'added_by' => $userId,
                'updated_by' => $userId,
            ], $deviceStatusData));
        }

        return response()->json([
            'success' => true,
            'message' => 'Location recorded successfully',
            'data' => [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'action' => $action,
                'recorded_at' => $recordedAt->toIso8601String()
            ]
        ]);
    }

    /**
     * Get the latest location of all authorized users.
     */
    public function current(Request $request, $box_slug)
    {
        $box = \App\Models\Box::where('slug', $box_slug)->first();
        if (!$box) {
            return response()->json(['success' => false, 'message' => 'Box not found'], 404);
        }
        
        $query = UserCurrentLocation::with('user:id,name,email')
                        ->where('box_id', $box->id);

        if ($request->has('boxuser_id')) {
            $query->where('boxuser_id', $request->boxuser_id);
            $locations = $query->first(); // If specific user requested, return just that one record
        } elseif ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
            $locations = $query->first(); 
        } else {
            $locations = $query->get();
        }

        return response()->json([
            'success' => true,
            'data' => $locations
        ]);
    }

    /**
     * Get the location history of a specific user.
     */
    public function history(Request $request, $box_slug)
    {
        $validator = Validator::make($request->all(), [
            'boxuser_id' => 'required|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $box = \App\Models\Box::where('slug', $box_slug)->first();

        $query = UserLocationHistory::where('box_id', $box->id)
                                    ->where('boxuser_id', $request->boxuser_id)
                                    ->orderBy('recorded_at', 'desc');

        if ($request->has('start_date')) {
            $query->whereDate('recorded_at', '>=', Carbon::parse($request->start_date));
        }

        if ($request->has('end_date')) {
            $query->whereDate('recorded_at', '<=', Carbon::parse($request->end_date));
        }

        $history = $query->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }

    /**
     * Get ordered location points for drawing a route map.
     */
    public function route(Request $request, $box_slug)
    {
        $validator = Validator::make($request->all(), [
            'boxuser_id' => 'required|integer',
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $box = \App\Models\Box::where('slug', $box_slug)->first();
        $date = Carbon::parse($request->date);

        $points = UserLocationHistory::where('box_id', $box->id)
                                     ->where('boxuser_id', $request->boxuser_id)
                                     ->whereDate('recorded_at', $date)
                                     ->orderBy('recorded_at', 'asc')
                                     ->get(['latitude', 'longitude', 'recorded_at']);

        // Calculate total distance and timings
        $totalDistanceMeters = 0;
        $mobilityTimeSeconds = 0;
        $restTimeSeconds = 0;
        
        for ($i = 1; $i < count($points); $i++) {
            $dist = $this->calculateDistance(
                $points[$i-1]->latitude, $points[$i-1]->longitude,
                $points[$i]->latitude, $points[$i]->longitude
            );
            $totalDistanceMeters += $dist;
            
            $timeDiff = Carbon::parse($points[$i]->recorded_at)->diffInSeconds(Carbon::parse($points[$i-1]->recorded_at));
            // Consider movement > 15 meters as mobility, else rest
            if ($dist < 15) {
                $restTimeSeconds += $timeDiff;
            } else {
                $mobilityTimeSeconds += $timeDiff;
            }
        }

        $checkIn = null;
        $checkOut = null;
        $totalTimeStr = '0 Hours';
        $restTimeStr = '0 Hours';
        $mobilityTimeStr = '0 Hours';

        if ($points->count() > 0) {
            $firstPoint = Carbon::parse($points->first()->recorded_at);
            $lastPoint = Carbon::parse($points->last()->recorded_at);
            $checkIn = $firstPoint->format('h:i A');
            $checkOut = $lastPoint->format('h:i A');
            
            $totalSeconds = $lastPoint->diffInSeconds($firstPoint);
            $totalTimeStr = floor($totalSeconds / 3600) . ' Hours ' . floor(($totalSeconds % 3600) / 60) . ' Minutes';
            $restTimeStr = floor($restTimeSeconds / 3600) . ' Hours ' . floor(($restTimeSeconds % 3600) / 60) . ' Minutes';
            $mobilityTimeStr = floor($mobilityTimeSeconds / 3600) . ' Hours ' . floor(($mobilityTimeSeconds % 3600) / 60) . ' Minutes';
        }

        return response()->json([
            'success' => true,
            'data' => [
                'points' => $points,
                'total_distance_km' => round($totalDistanceMeters / 1000, 2),
                'metrics' => [
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'total_time' => $totalTimeStr,
                    'rest_time' => $restTimeStr,
                    'mobility_time' => $mobilityTimeStr,
                    'date' => $date->format('d-m-Y')
                ]
            ]
        ]);
    }

    /**
     * Get the tracking dashboard summary for the authenticated user or specified user.
     */
    public function dashboard(Request $request, $box_slug)
    {
        $box = \App\Models\Box::where('slug', $box_slug)->first();
        if (!$box) return response()->json(['success' => false, 'message' => 'Box not found'], 404);

        $userId = Auth::id();
        $boxUser = \App\Models\BoxUser::where('user_id', $userId)->where('box_id', $box->id)->first();
        if (!$boxUser) return response()->json(['success' => false, 'message' => 'Box user not found'], 404);

        $targetBoxUserId = $request->input('boxuser_id', $boxUser->id);
        $date = $request->has('date') ? Carbon::parse($request->date) : Carbon::today();

        // 1. Get current location & tracking status
        $currentLocation = UserCurrentLocation::where('box_id', $box->id)
            ->where('boxuser_id', $targetBoxUserId)
            ->first();

        // 2. Check if tracking is active (last action today was 'start' or 'ping')
        $latestHistory = UserLocationHistory::where('box_id', $box->id)
            ->where('boxuser_id', $targetBoxUserId)
            ->whereDate('recorded_at', $date)
            ->latest('recorded_at')
            ->first();

        $isTracking = false;
        if ($latestHistory && in_array($latestHistory->action, ['start', 'ping'])) {
            $isTracking = true;
        }

        // 3. Get all points for the day to calculate distance and metrics
        $points = UserLocationHistory::where('box_id', $box->id)
            ->where('boxuser_id', $targetBoxUserId)
            ->whereDate('recorded_at', $date)
            ->orderBy('recorded_at', 'asc')
            ->get(['latitude', 'longitude', 'recorded_at']);

        $totalDistanceMeters = 0;
        $mobilityTimeSeconds = 0;
        $restTimeSeconds = 0;
        
        for ($i = 1; $i < count($points); $i++) {
            $dist = $this->calculateDistance(
                $points[$i-1]->latitude, $points[$i-1]->longitude,
                $points[$i]->latitude, $points[$i]->longitude
            );
            $totalDistanceMeters += $dist;
            
            $timeDiff = Carbon::parse($points[$i]->recorded_at)->diffInSeconds(Carbon::parse($points[$i-1]->recorded_at));
            if ($dist < 15) {
                $restTimeSeconds += $timeDiff;
            } else {
                $mobilityTimeSeconds += $timeDiff;
            }
        }

        $checkIn = null;
        $checkOut = null;
        $totalTimeStr = '0 Hours 0 Minutes';
        $restTimeStr = '0 Hours 0 Minutes';
        $mobilityTimeStr = '0 Hours 0 Minutes';
        $lastPing = '-';

        if ($points->count() > 0) {
            $firstPoint = Carbon::parse($points->first()->recorded_at);
            $lastPoint = Carbon::parse($points->last()->recorded_at);
            
            $checkIn = $firstPoint->format('h:i A');
            $checkOut = $lastPoint->format('h:i A');
            $lastPing = $lastPoint->format('h:i A');
            
            $totalSeconds = $lastPoint->diffInSeconds($firstPoint);
            $totalTimeStr = floor($totalSeconds / 3600) . ' Hours ' . floor(($totalSeconds % 3600) / 60) . ' Minutes';
            $restTimeStr = floor($restTimeSeconds / 3600) . ' Hours ' . floor(($restTimeSeconds % 3600) / 60) . ' Minutes';
            $mobilityTimeStr = floor($mobilityTimeSeconds / 3600) . ' Hours ' . floor(($mobilityTimeSeconds % 3600) / 60) . ' Minutes';
        }

        return response()->json([
            'success' => true,
            'data' => [
                'is_tracking' => $isTracking,
                'last_ping' => $lastPing,
                'total_distance_km' => round($totalDistanceMeters / 1000, 2),
                'metrics' => [
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'total_time' => $totalTimeStr,
                    'rest_time' => $restTimeStr,
                    'mobility_time' => $mobilityTimeStr,
                ],
                'current_location' => $currentLocation ? [
                    'latitude' => $currentLocation->latitude,
                    'longitude' => $currentLocation->longitude,
                    'accuracy' => $currentLocation->accuracy,
                    'recorded_at' => $currentLocation->recorded_at,
                    'battery_level' => $currentLocation->battery_level
                ] : null
            ]
        ]);
    }
}
