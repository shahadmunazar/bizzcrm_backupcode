<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Location Tracking Settings
    |--------------------------------------------------------------------------
    |
    | Configuration options for the Field Tracking Location Module.
    | These define thresholds and limits for how often locations are 
    | recorded to optimize database storage and frontend rendering.
    |
    */

    // Minimum time elapsed (in seconds) before saving a new history record
    'interval_seconds' => env('TRACKING_INTERVAL', 60),

    // Minimum distance moved (in meters) before saving a new history record
    // If user has not moved this much, the new ping is considered a duplicate.
    'distance_meters' => env('TRACKING_DISTANCE', 50),

    // Maximum acceptable GPS accuracy (in meters). Pings with higher 
    // accuracy values (meaning less precise) will be discarded.
    'max_accuracy' => env('TRACKING_MAX_ACCURACY', 100),

];
