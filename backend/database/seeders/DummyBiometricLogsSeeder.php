<?php

namespace Database\Seeders;

use App\Models\AttendanceRealtimeLogs;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DummyBiometricLogsSeeder extends Seeder
{
    public function run(): void
    {
        // 🔹 Configuration for the machines
        $devices = [
            'RSS202506119987' => ['00000001','00000002','00000003','00000004','00000005','00000006','00000007','00000008','00000009','00000010','00000011','00000012','00000013','00000014','00000015','00000016','00000017','00000018','00000019','00000020'],
            'RSS202506119988' => ['00000001','00000002','00000003','00000004','00000005','00000006','00000007','00000008','00000009','00000010','00000011','00000012','00000013','00000014','00000015','00000016','00000017','00000018','00000019','00000020'],
            'RSS202506119989' => ['00000001','00000002','00000003','00000004','00000005','00000006','00000007','00000008','00000009','00000010','00000011','00000012','00000013','00000014','00000015','00000016','00000017','00000018','00000019','00000020'],
            'RSS247272826286' => ['00000001','00000002','00000003','00000004','00000005','00000006','00000007','00000008','00000009','00000010','00000011','00000012','00000013','00000014','00000015','00000016','00000017','00000018','00000019','00000020']
        ];
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;
        $startYear = ($currentMonth < 12) ? ($currentYear - 1) : $currentYear;
        $startDate = Carbon::create($startYear, 10, 1)->startOfDay();
        $endDate = Carbon::today()->endOfDay();
        $totalDays = $startDate->diffInDays($endDate) + 1;
        $this->command->info("📅 Date Range: {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')} ({$totalDays} days)");
        $this->command->info("🚀 Generating logs for every day from October 1st to today...");
        $totalLogs = 0;
        $processedDays = 0;
        
        $maxId = \Illuminate\Support\Facades\DB::connection('attendance')->table('realtime_logs')->max('id') ?? 0;

        foreach ($devices as $deviceId => $biometricUserIds) {
            $this->command->info("📱 Generating logs for Device: {$deviceId}");

            foreach ($biometricUserIds as $biometricUserId) {
                $this->command->info("  👤 Processing User: {$biometricUserId}");

                // Loop through every day from October 1st to today
                $currentDate = $startDate->copy();
                $userLogCount = 0;
                $userDaysProcessed = 0;

                while ($currentDate->lte($endDate)) {
                    // Generate 2-12 random punches per day
                    $punchCount = rand(2, 12);
                    $punchTimes = [];

                    // Generate random punch times for the day (between 6 AM and 8 PM)
                    for ($p = 0; $p < $punchCount; $p++) {
                        // Random seconds from 6 AM (21600) to 8 PM (72000)
                        $randomSeconds = rand(21600, 72000);
                        $punchTime = Carbon::createFromTimestamp($currentDate->timestamp + $randomSeconds);
                        $punchTimes[] = $punchTime->format('His');
                    }

                    // Sort punch times chronologically
                    sort($punchTimes);

                    // Create log entry for each punch
                    foreach ($punchTimes as $time) {
                        // io_time format: YYYYMMDDHHmmss (date from October 1st to today)
                        $ioTime = $currentDate->format('Ymd') . $time;
                        
                        $maxId++;

                        \Illuminate\Support\Facades\DB::connection('attendance')->table('realtime_logs')->insert([
                            'id'            => $maxId,
                            'user_id'       => $biometricUserId,
                            'device_id'     => $deviceId,
                            'io_time'       => $ioTime, // Date from October 1st to today
                            'verify_mode'   => 1,
                            'io_mode'       => 1,
                            'log_data_json' => json_encode([
                                'fk_bin_data_lib' => 'M50',
                                'user_id'         => $biometricUserId,
                                'io_time'         => $ioTime, // Date from October 1st to today in JSON
                                'verify_mode'     => 1,
                                'io_mode'         => 1,
                            ]),
                            'received_at'   => now(), // Current timestamp for all logs
                            'client_ip'     => '192.168.1.' . rand(1, 254),
                        ]);

                        $userLogCount++;
                        $totalLogs++;
                    }

                    $userDaysProcessed++;
                    $processedDays++;

                    // Move to next day
                    $currentDate->addDay();
                }

                $this->command->info("    ✓ Generated {$userLogCount} logs for User {$biometricUserId} across {$userDaysProcessed} days");
            }
        }

        $this->command->info("");
        $this->command->info("✅ Successfully created {$totalLogs} logs");
        $this->command->info("   📅 Date range: {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')} ({$totalDays} days)");
        $this->command->info("   📊 Total days processed: {$processedDays}");
        $this->command->info("   📝 Punches per day: 2-12 (random)");
        $this->command->info("   ⏰ received_at: Current timestamp (" . now()->format('Y-m-d H:i:s') . ")");
        $this->command->info("   📦 log_data_json: Contains dates from October 1st to today");
    }
}
