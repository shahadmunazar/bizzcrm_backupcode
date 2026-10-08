<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeviceController extends Controller
{
    /**
     * Store the FCM token for a device.
     */
    public function registerToken(Request $request)
    {
        $request->validate([
            'device_id' => 'required|string',
            'fcm_token' => 'required|string',
        ]);

        $updated = DB::table('device_registers')
            ->updateOrInsert(
                ['device_id' => $request->device_id],
                [
                    'fcm_token' => $request->fcm_token,
                    'created_by' => $request->user() ? $request->user()->id : null,
                    'updated_at' => now(),
                ]
            );

        return response()->json([
            'success' => true,
            'message' => 'FCM token registered successfully.'
        ]);
    }
}
