<?php

namespace App\Console\Commands;

use App\Models\{Attendance, AttendanceBreak, AttendanceRealtimeLogs, BiometricMachine, BoxUser, Holiday, LeaveType, LeaveTransaction, UserMachineMapping};
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessBiometricAttendance extends Command
{
    protected $signature   = 'attendance:process-biometric';
    protected $description = 'Process biometric logs using clean and strict punch logic.';

    // Cache keys for frequently accessed data
    private $machineCache = [];
    private $mappingCache = [];
    private $boxUserCache = [];
    private $holidayCache = [];
    private $machinesToUpdate = [];
    private $processingStats = [];

    // Batch processing configuration
    private const BATCH_SIZE = 100;
    private const CHUNK_SIZE = 1000;

    public function handle()
    {
        $this->info("📌 BIOMETRIC Cron Started");

        $lock = Cache::lock('attendance:process-clean', 600);
        if (! $lock->get()) {
            $this->warn("Another instance running.");
            return 0;
        }

        $stats = [
            'machines_processed' => 0,
            'users_processed' => 0,
            'attendance_created' => 0,
            'attendance_updated' => 0,
            'users_with_punches' => 0,
            'users_without_punches' => 0,
            'total_punches' => 0,
            'errors' => 0
        ];

        // Store stats in class property for access in other methods
        $this->processingStats = &$stats;

        try {
            $this->info("🔄 Auto-assigning device IDs...");
            $this->autoAssignDeviceIds();

            $this->info("📊 Processing machines and collecting affected dates...");
            $result = $this->collectAffectedUserDates();
            $affectedUserDates = $result['user_dates'];
            $affectedDates = $result['dates'];

            if (empty($affectedUserDates) && empty($affectedDates)) {
                $this->info("ℹ️ No new biometric logs found since last sync basis.");
                return 0;
            }

            $logCombinations = 0;
            foreach ($affectedUserDates as $dates) {
                $logCombinations += count($dates);
            }

            $this->info("📋 Found {$logCombinations} user-date combinations from logs");
            $this->info("📅 Found " . count($affectedDates) . " unique dates with actual logs");

            $this->info("⚙️ Processing attendance ONLY for dates with logs in AttendanceRealtimeLogs (STRICT: device_id + user_id matching)...");
            $this->processUserDates($affectedUserDates, $affectedDates);

            // Update stats after processing
            $stats['users_processed'] = $stats['users_with_punches'] + $stats['users_without_punches'];

            $this->info("🔄 Updating machine sync times...");
            $this->updateMachineSyncTimes();

            $this->info("✅ BIOMETRIC Cron Completed Successfully");
            $this->info("");
            $this->info("═══════════════════════════════════════════════════════");
            $this->info("📊 FINAL SUMMARY");
            $this->info("═══════════════════════════════════════════════════════");
            $this->info("   👥 Users Processed: " . $stats['users_processed']);
            $this->info("   ✅ Users with Punches: " . $stats['users_with_punches']);
            $this->info("   ⚠️  Users without Punches: " . $stats['users_without_punches']);
            $this->info("   🔢 Total Punches Found: " . $stats['total_punches']);
            $this->info("   🆕 Attendance Created: " . $stats['attendance_created']);
            $this->info("   🔄 Attendance Updated: " . $stats['attendance_updated']);
            $this->info("   ❌ Errors: " . $stats['errors']);
            $this->info("═══════════════════════════════════════════════════════");
        } catch (\Throwable $e) {
            $this->error("❌ Error: " . $e->getMessage());
            $stats['errors']++;
        } finally {
            try {
                if (isset($lock)) {
                    $lock->release();
                }
            } catch (\Throwable $e) {
            }
        }

        return 0;
    }

    /**
     * Auto-assign unknown device IDs to empty machines
     */
    private function autoAssignDeviceIds(): void
    {
        // Use cursor for memory efficiency
        $logDeviceIds = AttendanceRealtimeLogs::select('device_id')
            ->distinct()
            ->whereNotNull('device_id')
            ->where('device_id', '!=', '')
            ->cursor();

        $deviceIds = [];
        foreach ($logDeviceIds as $log) {
            $deviceIds[] = $log->device_id;
        }

        if (empty($deviceIds)) {
            return;
        }

        // Batch check existing machines
        $existingDevices = BiometricMachine::whereIn('serial_no', $deviceIds)
            ->pluck('serial_no')
            ->toArray();

        $newDevices = array_diff($deviceIds, $existingDevices);

        if (empty($newDevices)) {
            return;
        }

        // Batch assign to empty machines
        foreach ($newDevices as $deviceId) {
            $emptyMachine = BiometricMachine::where(function ($q) {
                $q->whereNull('serial_no')->orWhere('serial_no', '');
            })->first();

            if ($emptyMachine) {
                $emptyMachine->update(['serial_no' => $deviceId]);
                Log::info("✅ Auto-matched device_id {$deviceId} to Machine ID {$emptyMachine->id}");
            } else {
                Log::warning("⚠️ Unregistered device_id {$deviceId} found in logs, but no empty BiometricMachine records available to assign.");
            }
        }
    }

    /**
     * Collect affected user dates from all machines
     * STRICT: Process by machine -> device_id -> user_id -> box_user_id -> attendance
     */
    private function collectAffectedUserDates(): array
    {
        $affectedUserDates = [];
        $affectedDates = [];
        $machinesToUpdate  = [];
        $machineCount = 0;
        $logCount = 0;

        // Step 1: Get all biometric machines with last_sync_attendance
        $machines = BiometricMachine::whereNotNull('serial_no')
            ->where('serial_no', '!=', '')
            ->select('id', 'serial_no', 'last_sync_attendance')
            ->cursor();

        foreach ($machines as $machine) {
            $machineCount++;
            $deviceId = $machine->serial_no; // device_id = serial_no
            $syncBasis = $machine->last_sync_attendance;

            // Skip if no last_sync_attendance
            if (!$syncBasis) {
                $this->info("  ⏭️ Machine {$deviceId}: No last_sync_attendance, skipping");
                continue;
            }

            // Get date range: from last_sync_attendance date to current date
            $syncDate = Carbon::parse($syncBasis)->startOfDay();
            $currentDate = Carbon::today();
            $daysToProcess = $syncDate->diffInDays($currentDate) + 1; // Include both start and end dates

            $this->info("  📱 Machine {$deviceId} (ID: {$machine->id}): Processing from {$syncDate->format('Y-m-d')} to {$currentDate->format('Y-m-d')} ({$daysToProcess} days)");

            // Step 2: Get all logs for this device_id from last_sync_attendance (based on received_at)
            // STRICT: We filter by device_id first, then check user_id in loop (BOTH must match together - AND condition)
            // We'll process by date from log_data_json['io_time'] or io_time column (YYYYMMDDHHmmss format)
            // received_at is just for filtering which logs to check, actual date comes from io_time
            $query = AttendanceRealtimeLogs::where('device_id', $deviceId) // STRICT: Filter by device_id first
                ->select('id', 'user_id', 'log_data_json', 'io_time', 'received_at', 'device_id')
                ->where('received_at', '>', $syncBasis);

            $maxReceivedAtForMachine = null;
            $machineLogCount = 0;
            $datesFound = [];

            // Step 3: Process logs and find user_id -> box_user_id mapping
            // STRICT: BOTH device_id AND user_id must match together (AND condition, not OR)
            // Also collect all unique dates from punch times (not just received_at)
            $query->orderBy('received_at')
                ->cursor()
                ->chunk(self::CHUNK_SIZE)
                ->each(function ($chunk) use (&$affectedUserDates, &$affectedDates, &$maxReceivedAtForMachine, &$machineLogCount, &$datesFound, $machine, $deviceId, $syncDate, $currentDate) {
                    foreach ($chunk as $log) {
                        // STRICT: Verify device_id matches (double-check even though we filtered)
                        if ($log->device_id !== $deviceId) {
                            continue; // Skip if device_id doesn't match exactly
                        }

                        $maxReceivedAtForMachine = $log->received_at;
                        $machineLogCount++;

                        // Step 4: Extract user_id from log (STRICT: use column first, fallback to JSON)
                        $userId = $log->user_id;
                        if (empty($userId) && $log->log_data_json) {
                            $logData = json_decode($log->log_data_json, true);
                            $userId = $logData['user_id'] ?? null;
                        }

                        // STRICT: BOTH device_id AND user_id must be present and match
                        if (!$userId) {
                            continue; // Skip logs without user_id - BOTH device_id AND user_id required
                        }

                        // Step 5: Get punch time and date from io_time or log_data_json
                        $pTime = $this->punchTime($log);
                        $date  = $pTime->toDateString();

                        // STRICT: Only process dates from last_sync_attendance to current date
                        $punchDate = Carbon::parse($date);
                        if ($punchDate->lt($syncDate) || $punchDate->gt($currentDate)) {
                            continue; // Skip dates outside the range
                        }

                        $datesFound[$date] = true;

                        // Step 6: Find box_user_id via UserMachineMapping
                        // STRICT: BOTH device_id (via machine->id) AND user_id must match together (AND condition)
                        // Also verify that biometric_machine_id corresponds to a machine with the correct serial_no (device_id)
                        $mapping = $this->getUserMapping($machine->id, $userId, $deviceId);

                        if ($mapping && $mapping->box_user_id) {
                            // Step 7: Store user-date combination with BOTH device_id AND user_id context
                            // Key includes both device_id and user_id to ensure uniqueness per device-user combination
                            $key = "{$deviceId}_{$userId}_{$date}"; // STRICT: Both device_id AND user_id in key
                            if (!isset($affectedUserDates[$key])) {
                                $affectedUserDates[$key] = [
                                    'device_id' => $deviceId,    // STRICT: device_id required
                                    'user_id' => $userId,        // STRICT: user_id required
                                    'box_user_id' => $mapping->box_user_id,
                                    'date' => $date,
                                    'machine_id' => $machine->id
                                ];
                                $affectedDates[$date] = true;
                            }
                        } else {
                            $this->warn("    ⚠️ No mapping found for Device {$deviceId} AND UserID: {$userId} on {$date} (BOTH must match)");
                        }
                    }
                });

            if ($maxReceivedAtForMachine) {
                $machinesToUpdate[$machine->id] = $maxReceivedAtForMachine;
            }

            if ($machineLogCount > 0) {
                $uniqueDatesCount = count($datesFound);
                $this->info("    ✓ Found {$machineLogCount} logs, {$uniqueDatesCount} unique dates with logs");
                $logCount += $machineLogCount;
            } else {
                $this->info("    ℹ️ No new logs found for this machine - skipping");
            }
        }

        // Store for later batch update
        $this->machinesToUpdate = $machinesToUpdate;

        $this->info("📊 Processed {$machineCount} machines, found {$logCount} total logs");
        $this->info("👥 Found " . count($affectedUserDates) . " unique device-user-date combinations from logs");

        // Only return dates that actually have logs (not all dates in range)
        $sortedDates = array_keys($affectedDates);
        sort($sortedDates);
        $dateRange = !empty($sortedDates) ? " ({$sortedDates[0]} to " . end($sortedDates) . ")" : "";

        $this->info("📅 Found " . count($affectedDates) . " unique dates with actual logs{$dateRange}");

        return [
            'user_dates' => $affectedUserDates,
            'dates' => $sortedDates // Return only dates that have logs
        ];
    }

    /**
     * Process all affected user dates in batches
     * STRICT: Only process users who have actual logs in AttendanceRealtimeLogs
     * Skip dates with no logs - don't create absent records
     */
    private function processUserDates(array $affectedUserDates, array $affectedDates): void
    {
        $batch = [];
        $batchCount = 0;
        $totalProcessed = 0;

        // Process only users who have logs (with device_id + user_id context)
        foreach ($affectedUserDates as $key => $data) {
            $batch[] = [
                'device_id' => $data['device_id'],
                'uId' => $data['user_id'],
                'box_user_id' => $data['box_user_id'],
                'date' => $data['date'],
                'machine_id' => $data['machine_id']
            ];

            if (count($batch) >= self::BATCH_SIZE) {
                $this->processBatch($batch);
                $totalProcessed += count($batch);
                $batch = [];
                $batchCount++;

                // Show progress every 10 batches
                if ($batchCount % 10 === 0) {
                    $this->info("  ⏳ Processed {$totalProcessed} user-date combinations...");
                }
            }
        }

        // Process remaining batch from logs
        if (!empty($batch)) {
            $this->processBatch($batch);
            $totalProcessed += count($batch);
        }

        $this->info("✅ Completed processing {$totalProcessed} total user-date combinations (only dates with actual logs)");
    }

    /**
     * Process a batch of user-date combinations
     * STRICT: Requires device_id, user_id, box_user_id, and date
     */
    private function processBatch(array $batch): void
    {
        DB::beginTransaction();
        try {
            foreach ($batch as $item) {
                $this->processUserDate(
                    $item['device_id'],
                    $item['uId'],
                    $item['box_user_id'],
                    $item['date'],
                    $item['machine_id'] ?? null
                );
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error("❌ Batch processing error: " . $e->getMessage());
            $this->processingStats['errors']++;
        }
    }

    /**
     * Process attendance for a single user-date combination
     * STRICT: Requires device_id AND user_id to match exactly
     * Checks log_data_json for user_id and io_time if not in main columns
     */
    private function processUserDate(string $deviceId, string $uId, int $boxUserId, string $date, ?int $machineId = null): void
    {
        $uIdTrimmed = ltrim($uId, '0');
        $uIdPadded  = str_pad($uId, 8, '0', STR_PAD_LEFT);

        // STRICT: Get logs matching BOTH device_id AND user_id together (AND condition, not OR)
        // We'll filter by date from io_time (log_data_json or column) after checking both device_id and user_id
        // Note: Don't filter by received_at date since io_time in log_data_json may have different date
        $allLogs = AttendanceRealtimeLogs::select('id', 'user_id', 'log_data_json', 'io_time', 'received_at', 'device_id')
            ->where('device_id', $deviceId) // STRICT: Must match device_id
            ->orderBy('received_at')
            ->get();

        $group = [];
        foreach ($allLogs as $log) {
            // STRICT: Verify device_id matches (double-check even though we filtered)
            if ($log->device_id !== $deviceId) {
                continue; // Skip if device_id doesn't match exactly
            }

            // Extract user_id from column or log_data_json
            $logUserId = $log->user_id;
            if (empty($logUserId) && $log->log_data_json) {
                $logData = json_decode($log->log_data_json, true);
                $logUserId = $logData['user_id'] ?? null;
            }

            // STRICT: Check if user_id matches (from column or log_data_json)
            // BOTH device_id AND user_id must match together (AND condition)
            if (!$logUserId || !in_array($logUserId, [$uId, $uIdTrimmed, $uIdPadded])) {
                continue; // Skip if user_id doesn't match - BOTH device_id AND user_id must match
            }

            // Extract time from io_time column or log_data_json
            $pTime = $this->punchTime($log);

            // STRICT: Ensure punch time is for the exact date (single date only)
            if ($pTime->toDateString() === $date) {
                $group[] = [
                    'log'   => $log,
                    'uId'   => $uId,
                    'pTime' => $pTime,
                ];
            }
        }

        $punchCount = count($group);

        // Debug output for every user-date combination
        $this->info("  👤 Device: {$deviceId} | User: {$uId} | BoxUser: {$boxUserId} | Date: {$date} | Punches: {$punchCount}");

        if (!empty($group)) {
            $this->processingStats['users_with_punches']++;
            $this->processingStats['total_punches'] += $punchCount;
            $this->processGroup($group, $boxUserId);
        } else {
            // Skip dates with no logs - don't create absent records
            // Only process when actual logs are found in AttendanceRealtimeLogs
            $this->info("    ⏭️ Skipping date {$date} - no logs found in AttendanceRealtimeLogs");
            $this->processingStats['users_without_punches']++;
        }
    }

    /**
     * Process case when user has no punches for the date
     */
    private function processNoPunchCase(int $boxUserId, string $date, string $deviceId): void
    {
        $boxUser = $this->getBoxUser($boxUserId);
        if (!$boxUser) {
            $this->warn("    ⚠️ BoxUser not found: {$boxUserId}");
            return;
        }

        // Get box_id from boxUser
        $boxId = $boxUser->box_id;

        $attendance = Attendance::where('box_user_id', $boxUserId)
            ->where('attendance_date', $date)
            ->lockForUpdate()
            ->first();

        if ($attendance) {
            // Reset attendance
            AttendanceBreak::where('attendance_id', $attendance->id)->delete();
            $attendance->check_in = null;
            $attendance->check_out = null;
            $attendance->working_hours = null;
            $attendance->production_hours = null;
            $attendance->break_duration = null;
            $attendance->status = $this->calculateDetailedStatus(null, $boxUser, $date, $boxId);
            $attendance->save();
            $this->info("    ↳ UPDATED (No punches) | Status: {$attendance->status}");
            $this->processingStats['attendance_updated']++;
        } else {
            // Create new attendance
            $attendance = new Attendance();
            $attendance->box_user_id = $boxUserId;
            $attendance->attendance_date = $date;
            $attendance->box_id = $boxId;
            $attendance->check_attendace_by = 'machine';
            $attendance->status = $this->calculateDetailedStatus(null, $boxUser, $date, $boxId);
            $attendance->save();
            $this->info("    ↳ CREATED (No punches) | Status: {$attendance->status}");
            $this->processingStats['attendance_created']++;
        }
    }

    /**
     * Process a single (device,user) group of realtime logs for one day.
     * STRICT: Uses box_user_id directly (already mapped)
     */
    private function processGroup(array $group, int $boxUserId): void
    {
        if (empty($group)) {
            return;
        }

        // Sort and deduplicate
        usort($group, fn($a, $b) => $a['pTime']->timestamp <=> $b['pTime']->timestamp);
        $group = $this->deduplicatePunches($group);

        if (empty($group)) {
            return;
        }

        $firstItem = $group[0];
        $log       = $firstItem['log'];
        $userId    = $firstItem['uId'];
        $deviceId  = $log->device_id;

        // Use cached machine lookup
        $machine = $this->getMachine($deviceId);
        if (!$machine) {
            $this->warn("    ⚠️ Machine not found for serial: {$deviceId}");
            return;
        }

        // Use cached box user lookup (boxUserId already provided)
        $boxUser = $this->getBoxUser($boxUserId);
        if (!$boxUser) {
            $this->warn("    ⚠️ BoxUser not found: {$boxUserId}");
            return;
        }

        $boxId = $boxUser->box_id;
        $times     = array_map(fn($item) => $item['pTime'], $group);
        $punchCount = count($times);
        $first     = $times[0];
        $last      = $punchCount > 1 ? $times[$punchCount - 1] : null;
        $date      = $first->format('Y-m-d');

        // Validation: Check punch count boundaries
        // Get max punch limit from shift settings, or use default of 50
        $maxPunchLimit = 50; // Default limit
        if ($boxUser && $boxUser->shift && $boxUser->shift->max_punch_in_allowed !== null && $boxUser->shift->max_punch_in_allowed > 0) {
            $maxPunchLimit = (int) $boxUser->shift->max_punch_in_allowed;
            Log::info("🔍 Using shift max_punch_in_allowed limit: {$maxPunchLimit} for User {$boxUserId} on {$date}");
        } else {
            if ($boxUser && $boxUser->shift) {
                Log::info("🔍 Shift assigned but max_punch_in_allowed not set or is 0, using default limit: {$maxPunchLimit} for User {$boxUserId} on {$date}");
            } else {
                Log::info("🔍 No shift assigned, using default max_punch_in_allowed limit: {$maxPunchLimit} for User {$boxUserId} on {$date}");
            }
        }

        // Limit punches according to max_punch_in_allowed or default
        if ($punchCount > $maxPunchLimit) {
            Log::warning("⚠️ Too many punches ({$punchCount}) for User {$boxUserId} on {$date}. Limiting to first {$maxPunchLimit} (max_punch_in_allowed: {$maxPunchLimit}).");
            $times = array_slice($times, 0, $maxPunchLimit);
            $punchCount = $maxPunchLimit;
            $last = $times[$maxPunchLimit - 1];
        }

        // Validation: Ensure all punches are on the same date
        foreach ($times as $idx => $time) {
            if ($time->format('Y-m-d') !== $date) {
                Log::warning("⚠️ Punch #{$idx} for User {$boxUserId} is on different date: {$time->format('Y-m-d')} vs {$date}. Skipping.");
                // Remove punches from different dates
                $times = array_filter($times, fn($t) => $t->format('Y-m-d') === $date);
                $times = array_values($times); // Re-index
                $punchCount = count($times);
                $last = $punchCount > 1 ? $times[$punchCount - 1] : null;
                break;
            }
        }

        // Validation: Minimum 1 punch required
        if ($punchCount < 1) {
            Log::warning("⚠️ No valid punches found for User {$boxUserId} on {$date}");
            return;
        }

        // Validation: Check if check_out is after check_in (when both exist)
        if ($punchCount >= 2 && $last && $last->lte($first)) {
            Log::warning("⚠️ Check-out ({$last->format('H:i:s')}) is not after check-in ({$first->format('H:i:s')}) for User {$boxUserId} on {$date}");
            // Swap if check_out is before check_in (data error)
            $temp = $first;
            $first = $last;
            $last = $temp;
            $times[0] = $first;
            $times[$punchCount - 1] = $last;
        }

        // Get or create attendance
        $attendance = Attendance::where('box_user_id', $boxUserId)
            ->where('attendance_date', $date)
            ->lockForUpdate()
            ->first();

        $isUpdate = (bool)$attendance;
        $checkInTime = $first->format('H:i:s');
        $checkOutTime = $last ? $last->format('H:i:s') : 'N/A';

        if (!$attendance) {
            $attendance = new Attendance();
            $attendance->box_user_id     = $boxUserId;
            $attendance->attendance_date = $date;
            $this->info("    ↳ CREATED | BoxUser: {$boxUserId} | Device: {$deviceId} | User: {$userId} | Punches: {$punchCount} | Check In: {$checkInTime} | Check Out: {$checkOutTime}");
            $this->processingStats['attendance_created']++;
        } else {
            $this->info("    ↳ UPDATED | BoxUser: {$boxUserId} | Device: {$deviceId} | User: {$userId} | Punches: {$punchCount} | Check In: {$checkInTime} | Check Out: {$checkOutTime}");
            $this->processingStats['attendance_updated']++;
        }

        // Ensure attendance is not null before proceeding
        if (!$attendance) {
            $this->error("    ❌ Failed to create/get attendance for BoxUser: {$boxUserId} on {$date}");
            return;
        }

        // Set basic attendance data
        // ALWAYS: First punch = Check In, Last punch = Check Out
        $attendance->box_id     = $boxId;
        $attendance->machine_id = $machine->id;

        // Extract check_in time from first punch (from io_time in log_data_json or column)
        $checkInTime = $first->format('H:i:s');
        $attendance->check_in   = $checkInTime; // First punch is always check_in

        // Extract check_out time from last punch (if exists)
        $checkOutTime = ($punchCount >= 2 && $last) ? $last->format('H:i:s') : null;
        $attendance->check_out  = $checkOutTime; // Last punch is always check_out

        // Debug: Verify check-in time is set correctly from io_time
        $this->info("      🔍 Check-in time set: {$checkInTime} (from first punch io_time: {$first->format('Y-m-d H:i:s')})");
        if ($checkOutTime) {
            $this->info("      🔍 Check-out time set: {$checkOutTime} (from last punch io_time: {$last->format('Y-m-d H:i:s')})");
        }

        $attendance->late_duration      = $this->computeLateDuration($boxUser, $date, $first);
        $attendance->check_attendace_by = 'machine';
        $attendance->save();

        // Calculate breaks based on shift settings
        $calculateBreaks = $this->shouldCalculateBreaks($boxUser);
        $totalBreakSeconds = 0;

        // Debug logging for break calculation
        Log::info("🔍 Break Calculation Debug - User: {$boxUserId}, Date: {$date}, Punches: {$punchCount}, CalculateBreaks: " . ($calculateBreaks ? 'YES' : 'NO'));
        if ($boxUser->shift) {
            $allInOut = $boxUser->shift->all_in_out_calculation;
            $firstInLastOut = $boxUser->shift->first_in_last_out_calculation;
            Log::info("🔍 Shift Info - ID: {$boxUser->shift->id}, all_in_out: " . var_export($allInOut, true) . " (type: " . gettype($allInOut) . "), first_in_last_out: " . var_export($firstInLastOut, true) . " (type: " . gettype($firstInLastOut) . ")");
        } else {
            Log::info("🔍 No shift assigned to user {$boxUserId}");
        }

        // When all_in_out_calculation == 1:
        // - First punch = check_in (already set above)
        // - Last punch = check_out (already set above)
        // - Middle punches (indices 1 to N-2) = breaks (paired as break_start/break_end)
        //
        // Example with 3 punches [10:00, 11:00, 18:00]:
        //   - Check In: 10:00 (first)
        //   - Check Out: 18:00 (last)
        //   - Middle: [11:00] → Break: 11:00 (break_start only, break_end = NULL - ongoing break)
        //   - Production: 10:00-11:00 = 1 hour (ongoing break not counted in production yet)
        //
        // Example with 4 punches [10:00, 11:00, 12:00, 18:00]:
        //   - Check In: 10:00 (first)
        //   - Check Out: 18:00 (last)
        //   - Middle: [11:00, 12:00] → Break: 11:00-12:00 (paired, completed break)
        //   - Production: 10:00-11:00 (1h) + 12:00-18:00 (6h) = 7 hours
        //
        // Example with 5 punches [10:00, 11:00, 12:00, 13:00, 18:00]:
        //   - Check In: 10:00 (first)
        //   - Check Out: 18:00 (last)
        //   - Middle: [11:00, 12:00, 13:00] → Breaks: 11:00-12:00 (paired, completed), 13:00 (ongoing, break_end = NULL)
        //   - Production: 10:00-11:00 (1h) + 12:00-13:00 (1h) = 2 hours (ongoing break not counted)
        //
        // Key: Paired middle punches = completed break. Odd middle punch = ongoing break (break_end = NULL).
        // Check_out is always the last punch and NEVER used as break_end.
        if ($calculateBreaks && $punchCount > 2 && $last) {
            // Extract middle punches (everything between first and last)
            $middle = array_slice($times, 1, $punchCount - 2);

            // Validation: Log if there are many middle punches (potential issue)
            $middleCount = count($middle);
            Log::info("🔍 Processing breaks - Middle punches: {$middleCount}, First: {$first->format('H:i:s')}, Last: {$last->format('H:i:s')}");
            if ($middleCount > 20) {
                Log::info("ℹ️ User {$boxUserId} on {$date} has {$punchCount} punches ({$middleCount} middle punches). Processing breaks...");
            }

            $totalBreakSeconds = $this->processBreaks($attendance, $boxUserId, $boxId, $middle, $date, $last);
            $this->info("🔍 Break calculation result - Total break seconds: {$totalBreakSeconds}, Break duration: " . gmdate('H:i:s', $totalBreakSeconds));

            // Validation: Ensure break duration doesn't exceed working hours
            if ($attendance->check_in && $attendance->check_out) {
                $checkInDT = Carbon::parse($date . ' ' . $attendance->check_in);
                $checkOutDT = Carbon::parse($date . ' ' . $attendance->check_out);
                if ($checkOutDT->lt($checkInDT)) {
                    $checkOutDT->addDay();
                }
                $workingSeconds = $checkInDT->diffInSeconds($checkOutDT);

                if ($totalBreakSeconds > $workingSeconds) {
                    $this->warn("⚠️ Break duration ({$totalBreakSeconds}s) exceeds working hours ({$workingSeconds}s) for User {$boxUserId} on {$date}. Limiting breaks.");
                    $totalBreakSeconds = max(0, $workingSeconds - 60); // Leave at least 1 minute of work
                }
            }
        }

        // Calculate working hours
        $this->calculateWorkingHours($attendance, $date, $totalBreakSeconds, $boxUser);

        // Set status
        $attendance->break_duration = $totalBreakSeconds > 0 ? gmdate('H:i:s', $totalBreakSeconds) : null;
        $attendance->status         = $this->calculateDetailedStatus($attendance, $boxUser, $date, $boxId);
        $attendance->status_updated_at = now();
        $attendance->save();

        // Handle Comp Off credit if status is Comp Off (Holiday or Weekly Off)
        if ($attendance->status === 'Comp Off') {
            $this->handleCompOffCredit($attendance, $boxUser, $date);
        }

        // Final debug output with all details
        $breakDuration = $attendance->break_duration ? $attendance->break_duration : '00:00:00';
        $workingHours = $attendance->working_hours ? $attendance->working_hours : '00:00:00';
        $productionHours = $attendance->production_hours ? $attendance->production_hours : '00:00:00';
        $action = $isUpdate ? 'UPDATED' : 'CREATED';
        $this->info("      ✓ {$action} | Status: {$attendance->status} | Working: {$workingHours} | Production: {$productionHours} | Breaks: {$breakDuration}");
    }

    /**
     * Deduplicate punches (debounce 60 seconds)
     */
    private function deduplicatePunches(array $group): array
    {
        $uniqueGroup = [];
        $lastTimestamp = 0;

        foreach ($group as $item) {
            $currentTimestamp = $item['pTime']->timestamp;
            if ($currentTimestamp - $lastTimestamp > 60) {
                $uniqueGroup[] = $item;
                $lastTimestamp = $currentTimestamp;
            }
        }

        return $uniqueGroup;
    }

    /**
     * Determine if breaks should be calculated based on shift settings
     *
     * When all_in_out_calculation == 1:
     *   - First punch = check_in
     *   - Last punch = check_out
     *   - Middle punches = breaks (paired as break_start/break_end)
     *
     * When first_in_last_out_calculation == 1 or no shift:
     *   - First punch = check_in
     *   - Last punch = check_out
     *   - Middle punches = IGNORED (no breaks calculated)
     */
    private function shouldCalculateBreaks($boxUser): bool
    {
        // Ensure shift relationship is loaded
        if (!$boxUser->relationLoaded('shift')) {
            $boxUser->load('shift');
        }

        if (!$boxUser->shift) {
            Log::debug("🔍 shouldCalculateBreaks: No shift assigned - returning false");
            return false; // No shift = First In / Last Out (no breaks)
        }

        // Check first_in_last_out_calculation (handles both boolean and integer)
        $firstInLastOut = $boxUser->shift->first_in_last_out_calculation;
        if ($firstInLastOut == 1 || $firstInLastOut === true || $firstInLastOut === '1') {
            Log::debug("🔍 shouldCalculateBreaks: first_in_last_out_calculation is true/1 - returning false");
            return false; // First In / Last Out mode (no breaks)
        }

        // Check all_in_out_calculation (handles both boolean and integer)
        $allInOut = $boxUser->shift->all_in_out_calculation;
        if ($allInOut == 1 || $allInOut === true || $allInOut === '1') {
            Log::debug("🔍 shouldCalculateBreaks: all_in_out_calculation is true/1 - returning true");
            return true; // All In / Out mode: Calculate breaks from middle punches
        }

        Log::debug("🔍 shouldCalculateBreaks: No calculation mode set - returning false (default)");
        return false; // Default to First In / Last Out (no breaks)
    }

    /**
     * Calculate working hours and production hours
     */
    private function calculateWorkingHours(Attendance $attendance, string $date, int $totalBreakSeconds, $boxUser): void
    {
        if (!$attendance->check_out) {
            $attendance->working_hours    = null;
            $attendance->production_hours = null;
            $attendance->early_check_out  = null;
            return;
        }
        $checkInDT  = Carbon::parse($date . ' ' . $attendance->check_in);
        $checkOutDT = Carbon::parse($date . ' ' . $attendance->check_out);
        if ($checkOutDT->lt($checkInDT)) {
            $checkOutDT->addDay();
        }

        $worked = $checkInDT->diffInSeconds($checkOutDT);
        $net    = max(0, $worked - $totalBreakSeconds);

        $attendance->working_hours    = gmdate('H:i:s', $worked);
        $attendance->production_hours = gmdate('H:i:s', $net);
        $attendance->early_check_out  = $this->computeEarlyCheckout($boxUser, $date, $checkOutDT);
    }

    /**
     * Process breaks for middle punches
     *
     * Logic: Pair consecutive middle punches as breaks (break_start/break_end)
     * - Each pair of consecutive middle punches = one break
     * - If odd number of middle punches, last one pairs with check_out as break
     * - Time after last middle punch until check_out = production time (NOT break)
     *
     * Examples:
     * - Punches [10:00, 11:00, 18:00]:
     *   * Check In: 10:00, Check Out: 18:00
     *   * Middle [11:00]: Break 11:00-18:00 (single middle pairs with check_out)
     *   * Production: 10:00-11:00 (1 hour)
     *
     * - Punches [10:00, 11:00, 12:00, 18:00]:
     *   * Check In: 10:00, Check Out: 18:00
     *   * Middle [11:00, 12:00]: Break 11:00-12:00 (paired middle punches)
     *   * Production: 10:00-11:00 (1 hour) + 12:00-18:00 (6 hours) = 7 hours
     *
     * - Punches [10:00, 11:00, 12:00, 13:00, 18:00]:
     *   * Check In: 10:00, Check Out: 18:00
     *   * Middle [11:00, 12:00, 13:00]: Breaks 11:00-12:00, 13:00-18:00
     *   * Production: 10:00-11:00 (1 hour) + 12:00-13:00 (1 hour) = 2 hours
     */
    private function processBreaks(Attendance $attendance, $boxUserId, $boxId, array $middleTimes, $date, Carbon $lastPunch): int
    {
        $totalBreakSeconds = 0;
        $middleCount       = count($middleTimes);
        $breaksToInsert    = [];
        $breaksToUpdate    = [];

        Log::info("🔍 processBreaks called - Attendance ID: {$attendance->id}, Middle count: {$middleCount}, Last punch: {$lastPunch->format('H:i:s')}");

        // Get existing breaks to compare structure
        $existingBreaks = AttendanceBreak::where('attendance_id', $attendance->id)
            ->orderBy('break_start', 'asc')
            ->get();

        // Track which breaks already exist to avoid duplicates
        $existingBreakKeys = [];
        foreach ($existingBreaks as $existing) {
            $key = $existing->break_start . '|' . ($existing->break_end ?? 'NULL');
            $existingBreakKeys[$key] = $existing;
        }

        // Process middle punches
        // Rule: Paired middle punches = completed break (with break_end)
        // Odd number: Last middle punch = ongoing break (break_start only, break_end = NULL)
        // NEVER use check_out as break_end
        for ($i = 0; $i < $middleCount; $i++) {
            // Check if there's a next middle punch to pair with
            if ($i + 1 < $middleCount) {
                // We have a pair: current middle punch = break_start, next middle punch = break_end
                $startTime  = $middleTimes[$i];
                $endTime    = $middleTimes[$i + 1];
                $startStamp = $date . ' ' . $startTime->format('H:i:s');
                $endStamp   = $date . ' ' . $endTime->format('H:i:s');

                $startDT  = Carbon::parse($startStamp);
                $endDT    = Carbon::parse($endStamp);
                $duration = max(0, $startDT->diffInSeconds($endDT));

                // Check if this break already exists
                $key = $startStamp . '|' . $endStamp;
                $existingBreak = $existingBreakKeys[$key] ?? null;

                if ($existingBreak) {
                    // Break exists, check if it needs update
                    if ($existingBreak->duration != gmdate('H:i:s', $duration)) {
                        $existingBreak->duration = gmdate('H:i:s', $duration);
                        $existingBreak->save();
                        Log::info("🔍 Updated existing break: {$startStamp} to {$endStamp}");
                    }
                    // Mark as processed so it won't be deleted
                    unset($existingBreakKeys[$key]);
                } else {
                    // New break to insert
                    $breaksToInsert[] = [
                        'attendance_id' => $attendance->id,
                        'box_user_id'   => $boxUserId,
                        'box_id'        => $boxId,
                        'break_start'   => $startStamp,
                        'break_end'     => $endStamp,
                        'duration'      => gmdate('H:i:s', $duration),
                        'break_status'  => 0, // 0 = completed break
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ];
                    Log::info("🔍 Creating paired break: {$startStamp} to {$endStamp} (duration: " . gmdate('H:i:s', $duration) . ")");
                }

                $totalBreakSeconds += $duration;
                $i++; // Skip next punch since it's paired as break_end
            } else {
                // Odd number of middle punches: last middle punch = ongoing break
                // Create break with break_start only, break_end = NULL (break not ended yet)
                $startTime  = $middleTimes[$i];
                $startStamp = $date . ' ' . $startTime->format('H:i:s');

                // Check if this ongoing break already exists
                $key = $startStamp . '|NULL';
                $existingOngoingBreak = $existingBreakKeys[$key] ?? null;

                if (!$existingOngoingBreak) {
                    // New ongoing break to insert
                    $breaksToInsert[] = [
                        'attendance_id' => $attendance->id,
                        'box_user_id'   => $boxUserId,
                        'box_id'        => $boxId,
                        'break_start'   => $startStamp,
                        'break_end'     => null, // NULL = break is ongoing, not ended yet
                        'duration'      => null, // No duration yet since break hasn't ended
                        'break_status'  => 1, // 1 = ongoing break
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ];
                    Log::info("🔍 Creating ongoing break: {$startStamp} (break_end = NULL - break not ended yet)");
                } else {
                    Log::info("🔍 Ongoing break already exists: {$startStamp}");
                    // Mark as processed so it won't be deleted
                    unset($existingBreakKeys[$key]);
                }
                // Don't add to totalBreakSeconds since break hasn't ended yet
                break; // Exit loop - no more pairs possible
            }
        }

        // Delete breaks that are no longer in the expected structure
        // (breaks that weren't matched/processed above)
        if (!empty($existingBreakKeys)) {
            $breaksToDelete = [];
            foreach ($existingBreakKeys as $key => $break) {
                $breaksToDelete[] = $break->id;
            }
            if (!empty($breaksToDelete)) {
                AttendanceBreak::whereIn('id', $breaksToDelete)->delete();
                Log::info("🔍 Deleted " . count($breaksToDelete) . " obsolete breaks for Attendance ID: {$attendance->id}");
            }
        }

        // Batch insert new breaks
        if (!empty($breaksToInsert)) {
            Log::info("🔍 Inserting " . count($breaksToInsert) . " breaks for Attendance ID: {$attendance->id}");
            AttendanceBreak::insert($breaksToInsert);
        }

        Log::info("🔍 processBreaks completed - Total break seconds (completed only): {$totalBreakSeconds}");
        return $totalBreakSeconds;
    }

    /**
     * Update machine sync times in batch
     */
    private function updateMachineSyncTimes(): void
    {
        if (empty($this->machinesToUpdate)) {
            $this->info("ℹ️ No machine sync times to update");
            return;
        }

        // Batch update using DB::table for better performance
        foreach ($this->machinesToUpdate as $machineId => $maxTime) {
            BiometricMachine::where('id', $machineId)->update([
                'last_sync_attendance' => $maxTime,
            ]);
        }

        $this->info("✅ Updated " . count($this->machinesToUpdate) . " machine sync times");
    }

    /**
     * Extract user ID from log (with caching)
     */
    private function extractUserId($log): ?string
    {
        $uId = $log->user_id;

        if (!$uId && $log->log_data_json) {
            $json = json_decode($log->log_data_json, true);
            $uId  = $json['user_id'] ?? null;
        }

        return $uId;
    }

    /**
     * Get machine with caching
     */
    private function getMachine(string $deviceId)
    {
        if (!isset($this->machineCache[$deviceId])) {
            $this->machineCache[$deviceId] = BiometricMachine::where('serial_no', $deviceId)
                ->select('id', 'serial_no')
                ->first();
        }

        return $this->machineCache[$deviceId];
    }

    /**
     * Get user mapping with caching
     * STRICT: BOTH biometric_machine_id (device) AND machine_user_id (user) must match together (AND condition)
     * The same user_id can exist on different devices, so we must match BOTH together
     * Also verifies that biometric_machine_id corresponds to a machine with the correct serial_no (device_id)
     */
    private function getUserMapping(int $machineId, string $userId, string $deviceId)
    {
        // Cache key includes BOTH machineId and userId to ensure uniqueness per device-user combination
        $cacheKey = "{$machineId}_{$userId}";

        if (!isset($this->mappingCache[$cacheKey])) {
            $uIdTrimmed = ltrim($userId, '0');
            $uIdPadded  = str_pad($userId, 8, '0', STR_PAD_LEFT);

            // STRICT: Filter by biometric_machine_id AND machine serial_no (device_id) AND machine_user_id
            // Use whereHas to filter by machine relationship at database level for efficiency
            // BOTH biometric_machine_id AND serial_no (device_id) AND machine_user_id must match together (AND condition)
            $mapping = UserMachineMapping::with(['machine' => function ($query) {
                    // Eager load machine with only needed fields
                    $query->select('id', 'serial_no');
                }])
                ->where('biometric_machine_id', $machineId) // STRICT: biometric_machine_id must match machineId
                ->whereHas('machine', function ($query) use ($deviceId) {
                    // STRICT: Filter by machine relationship - serial_no must match device_id exactly
                    $query->where('serial_no', $deviceId); // STRICT: serial_no must match device_id
                })
                ->where(function ($q) use ($userId, $uIdTrimmed, $uIdPadded) {
                    // STRICT: machine_user_id must match (check different formats: original, trimmed, padded)
                    $q->where('machine_user_id', $userId)
                        ->orWhere('machine_user_id', $uIdTrimmed)
                        ->orWhere('machine_user_id', $uIdPadded);
                })
                ->select('id', 'box_user_id', 'box_id', 'biometric_machine_id', 'machine_user_id')
                ->first();

            // STRICT: Verify that BOTH biometric_machine_id AND machine_user_id match, AND serial_no matches device_id
            if ($mapping) {
                // Step 1: Verify biometric_machine_id matches
                if ($mapping->biometric_machine_id != $machineId) {
                    Log::warning("⚠️ Mapping found but biometric_machine_id mismatch: Expected {$machineId}, Got {$mapping->biometric_machine_id}");
                    $mapping = null; // Reject if device doesn't match
                } else {
                    // Step 2: STRICT: Verify that machine relationship exists and serial_no matches device_id EXACTLY
                    // Use the machine relationship from UserMachineMapping model
                    $machineFromMapping = $mapping->machine;

                    if (!$machineFromMapping) {
                        Log::warning("⚠️ Mapping found but machine not found or serial_no doesn't match device_id '{$deviceId}' for biometric_machine_id: {$mapping->biometric_machine_id}");
                        $mapping = null; // Reject if machine doesn't exist or serial_no doesn't match
                    } else {
                        // STRICT: Double-check serial_no matches device_id (exact match, case-sensitive)
                        if ($machineFromMapping->serial_no !== $deviceId) {
                            Log::warning("⚠️ Mapping found but machine serial_no mismatch: Expected device_id '{$deviceId}', Got machine serial_no '{$machineFromMapping->serial_no}' for biometric_machine_id {$mapping->biometric_machine_id}");
                            $mapping = null; // Reject if serial_no doesn't match device_id exactly
                        } else {
                            // Step 3: Verify machine_user_id matches (check all formats)
                            $mappingUserId = $mapping->machine_user_id;
                            $userIdMatches = in_array($mappingUserId, [$userId, $uIdTrimmed, $uIdPadded], true);
                            if (!$userIdMatches) {
                                Log::warning("⚠️ Mapping found but machine_user_id mismatch: Expected one of [{$userId}, {$uIdTrimmed}, {$uIdPadded}], Got {$mappingUserId}");
                                $mapping = null; // Reject if user doesn't match
                            } else {
                                // All validations passed: biometric_machine_id, serial_no (device_id), and machine_user_id all match
                                Log::debug("✅ Mapping validated: biometric_machine_id={$mapping->biometric_machine_id}, serial_no={$machineFromMapping->serial_no}, device_id={$deviceId}, machine_user_id={$mappingUserId}");
                            }
                        }
                    }
                }
            }

            $this->mappingCache[$cacheKey] = $mapping;
        }

        return $this->mappingCache[$cacheKey];
    }

    /**
     * Get box user with caching and eager loading
     */
    private function getBoxUser(int $boxUserId)
    {
        if (!isset($this->boxUserCache[$boxUserId])) {
            $this->boxUserCache[$boxUserId] = BoxUser::with(['shift', 'box'])
                ->select('id', 'box_id', 'day_start_time', 'day_end_time', 'working_days', 'shift_id')
                ->find($boxUserId);
        }

        return $this->boxUserCache[$boxUserId];
    }

    /**
     * Convert punch to Carbon using io_time from log_data_json (priority) or column
     * Format: YYYYMMDDHHmmss (e.g., 20251028023633 = 2025-10-28 02:36:33)
     * Priority: log_data_json['io_time'] -> io_time column -> received_at
     *
     * @param object $log AttendanceRealtimeLogs record
     * @return Carbon Parsed date/time from io_time
     */
    private function punchTime($log)
    {
        try {
            $ioTime = null;
            $source = 'unknown';

            // Priority 1: Check log_data_json for io_time (most accurate)
            if ($log->log_data_json) {
                $json = json_decode($log->log_data_json, true);
                if ($json && isset($json['io_time']) && !empty($json['io_time'])) {
                    $ioTime = $json['io_time'];
                    $source = 'log_data_json';
                }
            }

            // Priority 2: Check io_time column if not found in JSON
            if (empty($ioTime) && !empty($log->io_time)) {
                $ioTime = $log->io_time;
                $source = 'io_time_column';
            }

            // Parse io_time if available (format: YYYYMMDDHHmmss)
            // Example: 20251028023633 = 2025-10-28 02:36:33
            if ($ioTime) {
                // Remove any non-numeric characters
                $cleanTime = preg_replace('/[^0-9]/', '', $ioTime);

                // Must be exactly 14 digits: YYYYMMDDHHmmss
                if (strlen($cleanTime) === 14) {
                    try {
                        // Parse using format: Y = Year (4 digits), m = Month (2), d = Day (2), H = Hour (2), i = Minutes (2), s = Seconds (2)
                        $parsedTime = Carbon::createFromFormat('YmdHis', $cleanTime);
                        // Debug: Log parsed time for verification
                        $this->info("      ✓ Parsed io_time from {$source}: '{$ioTime}' → {$parsedTime->format('Y-m-d H:i:s')} (Check-in will be: {$parsedTime->format('H:i:s')})");
                        return $parsedTime;
                    } catch (\Throwable $e) {
                        $this->warn("      ⚠️ Failed to parse io_time '{$ioTime}' from {$source}: " . $e->getMessage());
                    }
                } else {
                    $this->warn("      ⚠️ io_time '{$ioTime}' from {$source} is not 14 digits (got " . strlen($cleanTime) . "), expected YYYYMMDDHHmmss format");
                }
            }

            // Fallback to received_at if io_time not available or invalid
            return Carbon::parse($log->received_at);
        } catch (\Throwable $e) {
            $this->error("      ❌ Error parsing punch time: " . $e->getMessage());
            return Carbon::parse($log->received_at);
        }
    }

    private function computeLateDuration($boxUser, $date, Carbon $firstPunch)
    {
        $expectedStartTime = null;
        if ($boxUser && isset($boxUser->shift) && !empty($boxUser->shift->in_time)) {
            $expectedStartTime = $boxUser->shift->in_time;
        } elseif ($boxUser && !empty($boxUser->day_start_time)) {
            $expectedStartTime = $boxUser->day_start_time;
        } elseif ($boxUser && $boxUser->box && !empty($boxUser->box->day_start_time)) {
            $expectedStartTime = $boxUser->box->day_start_time;
        }

        if (!$expectedStartTime) {
            return null;
        }

        try {
            $expectedDT = Carbon::parse($date . ' ' . $expectedStartTime);
        } catch (\Throwable $e) {
            return null;
        }

        if ($firstPunch->gt($expectedDT)) {
            $lateSeconds = $expectedDT->diffInSeconds($firstPunch);
            return gmdate('H:i:s', max(0, $lateSeconds));
        }

        return null;
    }

    private function computeEarlyCheckout($boxUser, $date, Carbon $checkOutDT)
    {
        $expectedEndTime = null;
        if ($boxUser && isset($boxUser->shift) && !empty($boxUser->shift->out_time)) {
            $expectedEndTime = $boxUser->shift->out_time;
        } elseif ($boxUser && !empty($boxUser->day_end_time)) {
            $expectedEndTime = $boxUser->day_end_time;
        } elseif ($boxUser && $boxUser->box && !empty($boxUser->box->day_end_time)) {
            $expectedEndTime = $boxUser->box->day_end_time;
        }

        if (!$expectedEndTime) {
            return '00:00:00';
        }

        try {
            $expectedDT = Carbon::parse($date . ' ' . $expectedEndTime);
        } catch (\Throwable $e) {
            return '00:00:00';
        }

        if ($checkOutDT->lt($expectedDT)) {
            $earlySeconds = $checkOutDT->diffInSeconds($expectedDT);
            return gmdate('H:i:s', max(0, $earlySeconds));
        }

        return '00:00:00';
    }

    /**
     * Determine whether the date is a weekly off for this boxUser
     */
    private function isWeeklyOff($boxUser, $date)
    {
        if (!$boxUser) {
            return false;
        }

        $dayName = ucfirst(strtolower(Carbon::parse($date)->format('l')));

        if (isset($boxUser->shift) && $boxUser->shift) {
            $shift         = $boxUser->shift;
            $weeklyOffDays = array_filter([
                $shift->weekly_off_1 ?? null,
                $shift->weekly_off_2 ?? null,
                $shift->weekly_off_3 ?? null,
            ]);
            if (in_array($dayName, $weeklyOffDays, true)) {
                return true;
            }
        }

        if (!empty($boxUser->working_days)) {
            $userWorkingDays = is_array($boxUser->working_days)
                ? $boxUser->working_days
                : explode(',', $boxUser->working_days);
            $userWorkingDays = array_map('trim', $userWorkingDays);
            return !in_array($dayName, $userWorkingDays, true);
        }

        if ($boxUser->box && !empty($boxUser->box->working_days)) {
            $boxWorkingDays = is_array($boxUser->box->working_days)
                ? $boxUser->box->working_days
                : explode(',', $boxUser->box->working_days);
            $boxWorkingDays = array_map('trim', $boxWorkingDays);
            return !in_array($dayName, $boxWorkingDays, true);
        }

        return false;
    }

    /**
     * Calculate detailed status based on production hours, shift rules, weekly offs, and holidays.
     * For users without shift: Check punches (2 punches = Present), working_days, weekly offs, and holidays.
     * For users with shift: Check production hours against shift rules when punches are found in realtime_logs.
     */
    private function calculateDetailedStatus($attendance, $boxUser, $date, $boxId)
    {
        // Step 1: Check holidays first (highest priority)
        $cacheKey = "holiday_{$boxId}_{$date}";
        if (!isset($this->holidayCache[$cacheKey])) {
            $this->holidayCache[$cacheKey] = Holiday::with('holidayType')
                ->where('box_id', $boxId)
                ->whereDate('date', $date)
                ->get()
                ->filter(function ($h) {
                    if ($h->holidayType) {
                        return strcasecmp($h->holidayType->calculate_in_calendar, 'Yes') === 0;
                    }
                    return true;
                })->first();
        }
        $holiday = $this->holidayCache[$cacheKey];
        $isHoliday = (bool)$holiday;

        if ($isHoliday) {
            // Check Holiday Type for RH
            $holidayType = $holiday->holiday_type;

            // Also check relation if needed
            if (!$holidayType && $holiday->holidayType) {
                $holidayType = $holiday->holidayType->name;
            }

            // If it is RH, we DO NOT force Comp Off immediately.
            // We treated it as a working day candidate if they are present.
            // If they are absent, logic later will determine that.
            // "make persent" implies checking availability (attendance).
            // So if NOT RH, return Comp Off.
            if (strtoupper($holidayType) !== 'RH' && strtoupper($holidayType) !== 'RESTRICTED HOLIDAY') {
                return 'Comp Off';
            }
            // If RH, fall through to check attendance logic below
        }

        // Step 2: Check weekly off
        $isWeeklyOff = $this->isWeeklyOff($boxUser, $date);

        // Step 3: Check if user has shift assigned
        $shift = $boxUser->shift ?? null;
        $hasShift = $shift && isset($shift->shift_enabled) && $shift->shift_enabled;

        // Step 4: Handle users WITHOUT shift assignment
        // STRICT: Check for ALL days according to attendance_date (not just today)
        if (!$hasShift) {
            // Parse the attendance_date to get day name (works for ANY date, not just today)
            $attendanceDate = Carbon::parse($date);
            $dayName = ucfirst(strtolower($attendanceDate->format('l')));

            // Check weekly off FIRST (before checking punches) - uses attendance_date
            if ($isWeeklyOff) {
                return 'Comp Off'; // Weekly off day - return Comp Off regardless of punches
            }

            // If attendance exists and has BOTH check_in AND check_out (2 punches found) → Present
            // This check works for ALL dates, not just today
            if ($attendance && $attendance->check_in && $attendance->check_out) {
                return 'Present'; // Both punches found = Present (for any date)
            }

            // Check if day is in working_days (uses attendance_date, not today)
            $isWorkingDay = false;

            // Check BoxUser working_days
            if (!empty($boxUser->working_days)) {
                $userWorkingDays = is_array($boxUser->working_days)
                    ? $boxUser->working_days
                    : explode(',', $boxUser->working_days);
                $userWorkingDays = array_map('trim', $userWorkingDays);
                $isWorkingDay = in_array($dayName, $userWorkingDays, true);
            }
            // Check Box working_days if not found in BoxUser
            elseif ($boxUser->box && !empty($boxUser->box->working_days)) {
                $boxWorkingDays = is_array($boxUser->box->working_days)
                    ? $boxUser->box->working_days
                    : explode(',', $boxUser->box->working_days);
                $boxWorkingDays = array_map('trim', $boxWorkingDays);
                $isWorkingDay = in_array($dayName, $boxWorkingDays, true);
            }

            // If not a working day → Weekly Off (Comp Off)
            // This check uses attendance_date, works for ALL dates
            if (!$isWorkingDay) {
                return 'Comp Off'; // Weekly off day
            }

            // If working day but missing punches:
            // - If has check_in but no check_out → Punch Out Miss (for past dates) or Present (for today)
            // - If no punches at all → Absent
            if ($attendance && $attendance->check_in && !$attendance->check_out) {
                // Only treat as "Present" if it's today (might still punch out)
                // For all other dates (past dates), it's Punch Out Miss
                if ($attendanceDate->isToday()) {
                    return 'Present'; // Today, might still punch out
                }
                return 'Punch Out Miss'; // Past date with only check_in = Punch Out Miss
            }

            // No punches found → Absent (for any date)
            return 'Absent';
        }

        // Step 5: Handle users WITH shift assignment
        // When shift is assigned and punches are found in realtime_logs, calculate status based on production hours
        if ($isWeeklyOff) {
            return 'Comp Off';
        }

        // Handle null attendance (no punches case)
        if (!$attendance) {
            return 'Absent';
        }

        // If punches found (check_in exists) but no check_out
        if (!$attendance->check_out && !Carbon::parse($date)->isToday()) {
            return 'Punch Out Miss';
        }

        // STRICT: When shift is assigned and punches are found, calculate status based on production_hours
        // Production hours must be calculated from punches (check_in, check_out, breaks)
        $productionHours = $attendance->production_hours;

        // If no production hours calculated yet, but punches exist, return Present as default
        if (!$productionHours) {
            // If both check_in and check_out exist, should have production hours
            // If only check_in exists, might still be processing
            if ($attendance->check_in && $attendance->check_out) {
                return 'Present'; // Both punches found, assume Present until production hours calculated
            }
            return 'Present'; // Default for shift users with punches
        }

        // Calculate status based on production hours against shift rules
        if ($shift) {
            try {
                $productionSeconds      = CarbonInterval::createFromFormat('H:i:s', $productionHours)->totalSeconds;
                $absentBelowSeconds     = CarbonInterval::createFromFormat('H:i:s', $shift->absent_below ?? '00:00:00')->totalSeconds;
                $halfDayBelowSeconds    = CarbonInterval::createFromFormat('H:i:s', $shift->half_day_below ?? '00:00:00')->totalSeconds;
                $shortLeaveBelowSeconds = CarbonInterval::createFromFormat('H:i:s', $shift->short_leave_below ?? '00:00:00')->totalSeconds;
                $shiftHoursSeconds      = CarbonInterval::createFromFormat('H:i:s', $shift->shift_hours ?? '00:00:00')->totalSeconds;
                $relaxationSeconds      = CarbonInterval::createFromFormat('H:i:s', $shift->relaxation_time ?? '00:00:00')->totalSeconds;

                // Status calculation based on production hours:
                // - If production >= shift_hours → Present
                // - If production <= absent_below → Absent
                // - If production <= half_day_below → Half Day
                // - If production <= short_leave_below (adjusted) → Short Leave
                if ($productionSeconds >= $shiftHoursSeconds) {
                    return 'Present';
                }
                if ($productionSeconds <= $absentBelowSeconds) {
                    return 'Absent';
                }
                if ($productionSeconds <= $halfDayBelowSeconds) {
                    return 'Half Day';
                }
                $adjustedShortLeaveThreshold = max(0, $shortLeaveBelowSeconds - $relaxationSeconds);
                if ($productionSeconds <= $adjustedShortLeaveThreshold) {
                    return 'Short Leave';
                }

                // If production is between short_leave and shift_hours → Present
                return 'Present';
            } catch (\Exception $e) {
                Log::warning("⚠️ Error calculating status from production hours: " . $e->getMessage());
                return 'Present'; // Default to Present on error
            }
        }

        return 'Present';
    }

    /**
     * Handle Comp Off credit when user works on Holiday or Weekly Off
     * Credits are based on shift thresholds (absent_below, half_day_below, etc.)
     */
    private function handleCompOffCredit(Attendance $attendance, $boxUser, $date): void
    {
        $productionHours = $attendance->production_hours;
        if (!$productionHours) return;

        try {
            $productionSeconds = CarbonInterval::createFromFormat('H:i:s', $productionHours)->totalSeconds;
            $shift = $boxUser->shift;
            $creditAmount = 0;

            if ($shift) {
                // Ensure thresholds are available
                $absentBelowSeconds     = $shift->absent_below ? CarbonInterval::createFromFormat('H:i:s', $shift->absent_below)->totalSeconds : 0;
                $halfDayBelowSeconds    = $shift->half_day_below ? CarbonInterval::createFromFormat('H:i:s', $shift->half_day_below)->totalSeconds : 0;
                $shortLeaveBelowSeconds = $shift->short_leave_below ? CarbonInterval::createFromFormat('H:i:s', $shift->short_leave_below)->totalSeconds : 0;
                $shiftHoursSeconds      = $shift->shift_hours ? CarbonInterval::createFromFormat('H:i:s', $shift->shift_hours)->totalSeconds : 0;
                $relaxationSeconds      = ($shift->relaxation_time ?? '00:00:00') ? CarbonInterval::createFromFormat('H:i:s', $shift->relaxation_time ?? '00:00:00')->totalSeconds : 0;
                
                $adjustedShortLeaveThreshold = max(0, $shortLeaveBelowSeconds - $relaxationSeconds);
                
                if ($productionSeconds >= $shiftHoursSeconds) {
                    $creditAmount = 1.0;
                } elseif ($productionSeconds < $absentBelowSeconds) {
                    $creditAmount = 0;
                } elseif ($productionSeconds < $halfDayBelowSeconds) {
                    // Fractional credit based on half day deduction
                    $deduction = (float)($shift->half_day_deduction ?? 50); 
                    $creditAmount = max(0, 1.0 - ($deduction / 100));
                } elseif ($productionSeconds < $shortLeaveBelowSeconds) {
                    // Fractional credit based on short leave deduction
                    $deduction = (float)($shift->short_leave_deduction ?? 25); 
                    $creditAmount = max(0, 1.0 - ($deduction / 100));
                } else {
                    $creditAmount = 1.0;
                }
            } else {
                // Default logic if no shift is assigned (8h full, 4h half)
                if ($productionSeconds >= 8 * 3600) {
                    $creditAmount = 1.0;
                } elseif ($productionSeconds >= 4 * 3600) {
                    $creditAmount = 0.5;
                }
            }

            if ($creditAmount > 0) {
                // Find Comp Off leave type
                $compOffType = LeaveType::where('status', 'Active')
                    ->where('box_id', $boxUser->box_id)
                    ->where(function($q) {
                        $q->where('leave_type', 'Comp Off')
                          ->orWhere('leave_type', 'Compensatory Off Leave')
                          ->orWhere('leave_type', 'like', 'Comp%Off%');
                    })
                    ->first();

                if ($compOffType) {
                    // Check if already credited for this date
                    $alreadyCredited = LeaveTransaction::where('box_user_id', $boxUser->id)
                        ->where('leave_type_id', $compOffType->id)
                        ->whereDate('date', $date)
                        ->exists();

                    if (!$alreadyCredited) {
                        // Get current balance
                        $latestTxn = LeaveTransaction::where('box_user_id', $boxUser->id)
                            ->where('leave_type_id', $compOffType->id)
                            ->latest()
                            ->first();

                        $previousBalance = $latestTxn->balance_leave ?? 0;
                        
                        // Re-check why it's Comp Off for remarks
                        $isWeeklyOff = $this->isWeeklyOff($boxUser, $date);
                        $dayName = Carbon::parse($date)->format('l');
                        LeaveTransaction::create([
                            'box_id'              => $boxUser->box_id,
                            'box_user_id'         => $boxUser->id,
                            'leave_type_id'       => $compOffType->id,
                            'date'                => $date,
                            'credit_leave'        => $creditAmount,
                            'balance_leave'       => $previousBalance + $creditAmount,
                            'total_leave'         => $creditAmount,
                            'transaction_remarks' => 'Comp Off earned (' . $creditAmount . ') for working on ' .
                            ($isWeeklyOff ? 'Weekly Off' : 'Holiday') . ' (' . $dayName . ')',
                            'status'              => 'Approved',
                            'created_by'          => null, // System generated
                            'applied_date'        => $date,
                            'approved_date'       => now(),
                        ]);
                        $this->info(" 🎁 Comp Off Credit Added: {$creditAmount} for {$date}");
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("Error calculating Comp Off credit in biometric cron: " . $e->getMessage());
        }
    }
}
