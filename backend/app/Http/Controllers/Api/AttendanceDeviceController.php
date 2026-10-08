<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{DeviceRegister,Box,AttendanceFingerprint};
use Illuminate\Support\Facades\Auth;

class AttendanceDeviceController extends Controller
{
    /**
     * Check if the device is registered for the logged-in user in this box.
     */
    public function checkStatus(Request $request, $box_slug)
    {
        $box = Box::where('slug', $box_slug)->first();
        if (!$box) {
            return response()->json(['success' => false, 'message' => 'Box not found'], 404);
        }

        // Check if there is a device for this box
        $device = DeviceRegister::where('boxes_id', $box->id)->where('device_id',$request->input('device_id'))->where('status', '!=', 'Revoked')->first();
        if ($device) {
            $fingerprint = AttendanceFingerprint::where('device_id', $device->device_id)
                ->where('user_id', $device->user_id)
                ->where('status', 'active')
                ->first();

            return response()->json([
                'success' => true,
                'is_registered' => true,
                'status' => $device->status,
                'device' => $device,
                'is_fingerprint_enrolled' => $fingerprint ? true : false,
                'fingerprint_enrolled_at' => $fingerprint ? $fingerprint->enrolled_at : null
            ]);
        }

        return response()->json([
            'success' => true,
            'is_registered' => false,
            'message' => 'No active device found.'
        ]);
    }

    /**
     * Enroll a new mobile device.
     */
    public function enroll(Request $request, $box_slug)
    {
        $box = Box::where('slug', $box_slug)->first();
        if (!$box) {
            return response()->json(['success' => false, 'message' => 'Box not found'], 404);
        }

        $request->validate([
            'device_id' => 'required',
            'device_name' => 'required',
            'platform' => 'required',
            'public_key' => 'required'
        ]);

        $userId = $request->input('user_id', Auth::id());
        // Check if this specific device is already registered in this box
        $check_duplicate = DeviceRegister::where('device_id', $request->input('device_id'))
            ->where('boxes_id', $box->id)
            ->where('status', '!=', 'Revoked')
            ->first();

        if ($check_duplicate) {
            // If it belongs to another user, they cannot steal the device
            if ($check_duplicate->user_id !== $userId) {
                return response()->json(['success' => false, 'message' => 'Device already registered by another user']);
            }
            // If it belongs to this user, they are re-enrolling it, so we don't block it 
            // (it will be revoked and re-created in the next steps)
        }

        // Revoke any existing device for THIS user in this box (only 1 active device allowed per user)
        DeviceRegister::where('user_id', $userId)
            ->where('boxes_id', $box->id)
            ->where('status', '!=', 'Revoked')
            ->update(['status' => 'Revoked']);

        // Register the new device as Pending Approval
        $device = new DeviceRegister();
        $device->fill($request->all());
        $device->boxes_id = $box->id;
        $device->user_id = $userId;
        $device->created_by = $userId;
        $device->status = 'Pending Approval';
        $device->save();
        return response()->json([
            'success' => true,
            'message' => 'Device enrolled successfully. Awaiting admin approval.',
            'device' => $device
        ], 201);
    }

    /**
     * Tie the employee's fingerprint to that device + box
     */
    public function bindFingerprint(Request $request, $box_slug)
    {
        $box = Box::where('slug', $box_slug)->first();
        if (!$box) {
            return response()->json(['success' => false, 'message' => 'Box not found'], 404);
        }

        $request->validate([
            'device_id' => 'required',
            'public_key' => 'required'
        ]);

        $userId = $request->input('user_id', Auth::id());

        // 1. Look up device registration from API 1
        $deviceRegistration = DeviceRegister::where('boxes_id', $box->id)
            ->where('user_id', $userId) // ensure it belongs to this employee
            ->where('device_id', $request->input('device_id'))
            ->where('status', '!=', 'Revoked')
            ->first();

        if (!$deviceRegistration) {
            return response()->json(['success' => false, 'message' => 'Device not registered for this user'], 404);
        }

        // Check for duplicate signature across the entire system
        if ($request->filled('signature')) {
            $duplicateSignature = AttendanceFingerprint::where('signature', $request->input('signature'))
                ->where('status', 'active')
                ->exists();

            if ($duplicateSignature) {
                return response()->json(['success' => false, 'message' => 'This fingerprint signature is already enrolled'], 409);
            }
        }

        // 2. One-to-one rule enforcement
        // Revoke if THIS device already has a fingerprint binding
        AttendanceFingerprint::where('device_id', $request->input('device_id'))
            ->update(['status' => 'revoked']);
            
        // Revoke if THIS employee is bound on another device in this box
        AttendanceFingerprint::where('user_id', $userId)
            ->where('boxes_id', $box->id)
            ->update(['status' => 'revoked']);

        // Get the box_user_id
        $boxUser = \App\Models\BoxUser::where('user_id', $userId)
            ->where('box_id', $box->id)
            ->first();

        // 3. Insert into attendance_fingerprints
        $fingerprint = new AttendanceFingerprint();
        $fingerprint->boxes_id = $box->id;
        $fingerprint->user_id = $userId;
        $fingerprint->box_user_id = $boxUser ? $boxUser->id : null;
        $fingerprint->device_id = $request->input('device_id');
        $fingerprint->public_key = $request->input('public_key');
        $fingerprint->signature = $request->input('signature'); // Save the enrollment signature
        $fingerprint->key_algorithm = $request->input('key_algorithm');
        $fingerprint->status = 'active';
        $fingerprint->enrolled_at = now();
        $fingerprint->save();

        $targetUser = \App\Models\User::find($userId);

        // 4. Return the binding
        return response()->json([
            'message' => 'Fingerprint attendance enabled',
            'data' => [
                'binding' => [
                    'id' => $fingerprint->id,
                    'user_id' => $userId,
                    'user_name' => $targetUser ? $targetUser->name : '',
                    'device_id' => $fingerprint->device_id,
                    'status' => $fingerprint->status,
                    'enrolled_at' => $fingerprint->enrolled_at
                ]
            ]
        ], 201);
    }

    public function punch(Request $request, $box_slug)
    {
        $box = Box::where('slug', $box_slug)->first();
        if (!$box) {
            return response()->json(['success' => false, 'message' => 'Box not found'], 404);
        }

        $request->validate([
            'device_id' => 'required',
            'punched_at' => 'required',
            'signed_payload' => 'required',
            'signature' => 'required'
        ]);

        // 1. Fetch ALL active fingerprints enrolled on this specific device
        $fingerprints = AttendanceFingerprint::where('device_id', $request->input('device_id'))
            ->where('boxes_id', $box->id)
            ->where('status', 'active')
            ->get();

        if ($fingerprints->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Device not registered'], 403);
        }

        // 2. Cryptographic verification loop (Find WHICH user just punched)
        // We cannot search by 'signature' in the database because every punch creates a BRAND NEW unique signature!
        // Instead, we test the new punch signature against every enrolled public key on this device.
        $matchedFingerprint = null;
        foreach ($fingerprints as $fingerprint) {
            $publicKey = $fingerprint->public_key;
            
            // Check if the key already has PEM headers
            if (strpos($publicKey, '-----BEGIN PUBLIC KEY-----') === 0) {
                $pem = $publicKey;
            } else {
                $pem = "-----BEGIN PUBLIC KEY-----\n" . wordwrap($publicKey, 64, "\n", true) . "\n-----END PUBLIC KEY-----";
            }
            
            $keyResource = openssl_pkey_get_public($pem);
            if ($keyResource === false) {
                continue; // Skip invalid keys to prevent openssl_verify ErrorException
            }

            $verify = openssl_verify(
                $request->input('signed_payload'),
                base64_decode($request->input('signature')),
                $keyResource,
                OPENSSL_ALGO_SHA256
            );

            if ($verify === 1) {
                $matchedFingerprint = $fingerprint;
                break; // Found the exact user whose finger unlocked this device!
            }
        }

        if (!$matchedFingerprint) {
            return response()->json(['success' => false, 'message' => 'Fingerprint verification failed'], 403);
        }

        // Pull exact user identity from the verified hardware binding match
        $userId = $matchedFingerprint->user_id; 
        $boxUserId = $matchedFingerprint->box_user_id;

        // 3. Payload cross-check
        // Ignoring action index [5] for cross-check since we auto-detect
        $payloadParts = explode('|', $request->input('signed_payload'));
        if (count($payloadParts) < 9 ||
            $payloadParts[2] !== $box_slug ||
            $payloadParts[3] != $userId ||
            $payloadParts[4] !== $request->input('device_id') ||
            $payloadParts[8] !== $request->input('punched_at')) {
            return response()->json(['success' => false, 'message' => 'Payload mismatch'], 422);
        }

        // 4. Freshness
        $clientPunchedAt = \Carbon\Carbon::parse($request->input('punched_at'));
        if (now()->diffInMinutes($clientPunchedAt) > 5) {
            return response()->json(['success' => false, 'message' => "Check your phone's time"], 422);
        }

        // Use exact server time for the punch to prevent client-side time spoofing
        $punchedAt = now();

        // Replay check
        if (\App\Models\AttendancePunch::where('signature', $request->input('signature'))->exists()) {
            return response()->json(['success' => false, 'message' => 'Duplicate punch detected'], 409);
        }

        // 5. Use the exact box_user_id assigned during fingerprint enrollment
        $boxUserId = $fingerprint->box_user_id;

        // 6. Save the raw punch immediately
        $actionFromPayload = count($payloadParts) >= 6 ? $payloadParts[5] : 'punch';
        $punch = new \App\Models\AttendancePunch();
        $punch->boxes_id = $box->id;
        $punch->user_id = $userId;
        $punch->box_user_id = $boxUserId;
        $punch->device_id = $request->input('device_id');
        $punch->action = $actionFromPayload; // we log what they sent, but don't rely on it
        $punch->punched_at = $punchedAt;
        $punch->latitude = $request->input('latitude');
        $punch->longitude = $request->input('longitude');
        $punch->signed_payload = $request->input('signed_payload');
        $punch->signature = $request->input('signature');
        $punch->save();

        // 7. Calculate daily attendance auto-detection
        $today = $punchedAt->format('Y-m-d');
        $allPunches = \App\Models\AttendancePunch::where('box_user_id', $boxUserId)
            ->where('boxes_id', $box->id)
            ->whereDate('punched_at', $today)
            ->orderBy('punched_at', 'asc')
            ->get();

        $punchCount = $allPunches->count();
        if ($punchCount === 0) {
            // Should not happen since we just inserted
            return response()->json(['success' => false, 'message' => 'Error processing punch logic'], 500);
        }

        $firstPunch = $allPunches->first();
        $lastPunch = $punchCount > 1 ? $allPunches->last() : null;

        $attendance = \App\Models\Attendance::where('box_user_id', $boxUserId)
            ->where('box_id', $box->id)
            ->where('attendance_date', $today)
            ->first();

        if (!$attendance) {
            $attendance = new \App\Models\Attendance();
            $attendance->box_user_id = $boxUserId;
            $attendance->box_id = $box->id;
            $attendance->attendance_date = $today;
            $attendance->device_id = $request->input('device_id');
            $attendance->check_attendace_by = 'fingerprint';
            $attendance->created_by = $userId;
        } else {
            $attendance->device_id = $request->input('device_id');
            $attendance->check_attendace_by = 'fingerprint';
            $attendance->updated_by = $userId;
        }

        // First punch = Check in
        $checkInDT = \Carbon\Carbon::parse($firstPunch->punched_at);
        $attendance->check_in = $checkInDT->format('H:i:s');
        $attendance->status = 'in'; 

        // Last punch = Check out
        $checkOutDT = null;
        if ($lastPunch) {
            $checkOutDT = \Carbon\Carbon::parse($lastPunch->punched_at);
            $attendance->check_out = $checkOutDT->format('H:i:s');
            $attendance->status = 'out';
        }

        // Process Breaks (Middle Punches)
        $totalBreakSeconds = 0;
        if ($punchCount > 2 && $lastPunch) {
            $middlePunches = $allPunches->slice(1, $punchCount - 2)->values();
            $middleCount = $middlePunches->count();
            
            // Delete existing breaks to recalculate cleanly
            \App\Models\AttendanceBreak::where('attendance_id', $attendance->id)->delete();

            for ($i = 0; $i < $middleCount; $i++) {
                if ($i + 1 < $middleCount) {
                    // Pair found
                    $startDT = \Carbon\Carbon::parse($middlePunches[$i]->punched_at);
                    $endDT = \Carbon\Carbon::parse($middlePunches[$i + 1]->punched_at);
                    $duration = max(0, $startDT->diffInSeconds($endDT));
                    $totalBreakSeconds += $duration;

                    if ($attendance->id) { // only if attendance is saved
                        \App\Models\AttendanceBreak::create([
                            'attendance_id' => $attendance->id,
                            'box_user_id' => $boxUserId,
                            'box_id' => $box->id,
                            'break_start' => $startDT->format('Y-m-d H:i:s'),
                            'break_end' => $endDT->format('Y-m-d H:i:s'),
                            'duration' => gmdate('H:i:s', $duration),
                            'break_status' => 0
                        ]);
                    }
                    $i++; // skip next since it's used as end
                } else {
                    // Odd middle punch - ongoing break
                    $startDT = \Carbon\Carbon::parse($middlePunches[$i]->punched_at);
                    if ($attendance->id) {
                        \App\Models\AttendanceBreak::create([
                            'attendance_id' => $attendance->id,
                            'box_user_id' => $boxUserId,
                            'box_id' => $box->id,
                            'break_start' => $startDT->format('Y-m-d H:i:s'),
                            'break_end' => null,
                            'duration' => null,
                            'break_status' => 1
                        ]);
                    }
                }
            }
        }

        // Calculate hours
        if ($checkInDT && $checkOutDT) {
            if ($checkOutDT->lt($checkInDT)) {
                $checkOutDT->addDay();
            }
            $worked = $checkInDT->diffInSeconds($checkOutDT);
            $net = max(0, $worked - $totalBreakSeconds);

            $attendance->working_hours = gmdate('H:i:s', $worked);
            $attendance->production_hours = gmdate('H:i:s', $net);
        }

        $attendance->break_duration = $totalBreakSeconds > 0 ? gmdate('H:i:s', $totalBreakSeconds) : null;
        
        // --- Calculate Advanced Status (Shifts, Weekly Off, Holidays) ---
        $boxUser = \App\Models\BoxUser::with(['shift', 'box'])->find($boxUserId);
        $calcService = new \App\Services\AttendanceCalculationService();
        $attendance->status = $calcService->calculateDetailedStatus($attendance, $boxUser, $today, $box->id);
        $attendance->save();

        // --- Grant Comp Off if they worked on a Holiday / Weekly Off ---
        if ($attendance->status === 'Comp Off') {
            $calcService->handleCompOffCredit($attendance, $boxUser, $today);
        }
        
        // Save breaks if attendance was just created
        if ($punchCount > 2 && $lastPunch && $attendance->wasRecentlyCreated) {
            // Need to recreate breaks now that we have attendance->id
             $middlePunches = $allPunches->slice(1, $punchCount - 2)->values();
             $middleCount = $middlePunches->count();
             for ($i = 0; $i < $middleCount; $i++) {
                if ($i + 1 < $middleCount) {
                    $startDT = \Carbon\Carbon::parse($middlePunches[$i]->punched_at);
                    $endDT = \Carbon\Carbon::parse($middlePunches[$i + 1]->punched_at);
                    $duration = max(0, $startDT->diffInSeconds($endDT));
                    \App\Models\AttendanceBreak::create([
                        'attendance_id' => $attendance->id,
                        'box_user_id' => $boxUserId,
                        'box_id' => $box->id,
                        'break_start' => $startDT->format('Y-m-d H:i:s'),
                        'break_end' => $endDT->format('Y-m-d H:i:s'),
                        'duration' => gmdate('H:i:s', $duration),
                        'break_status' => 0
                    ]);
                    $i++; 
                } else {
                    $startDT = \Carbon\Carbon::parse($middlePunches[$i]->punched_at);
                    \App\Models\AttendanceBreak::create([
                        'attendance_id' => $attendance->id,
                        'box_user_id' => $boxUserId,
                        'box_id' => $box->id,
                        'break_start' => $startDT->format('Y-m-d H:i:s'),
                        'break_end' => null,
                        'duration' => null,
                        'break_status' => 1
                    ]);
                }
            }
        }

        $responseStatus = $punchCount === 1 ? 'Check-in processed successfully' : 'Punch processed successfully';
        
        return response()->json([
            'message' => $responseStatus,
            'data' => [
                'punch' => [
                    'id' => $punch->id,
                    'punched_at' => $request->input('punched_at'),
                    'punch_count_today' => $punchCount
                ],
                'today' => [
                    'status' => $attendance->status,
                    'punch_in_at' => $attendance->check_in,
                    'punch_out_at' => $attendance->check_out,
                    'breaks_count' => floor($totalBreakSeconds / 60) . ' mins'
                ]
            ]
        ], 201);
    }
}
