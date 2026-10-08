<?php

namespace App\Console\Commands;

use App\Models\AttendanceRealtimeLogs;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateRealtimeAttendanceLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:generate-realtime';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate real-time attendance logs into the attendance server using DummyBiometric config';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 🔹 Configuration for the machines matching the seeder
        $devices = [
            'RSS202506119987' => ['00000001','00000002','00000003','00000004','00000005','00000006','00000007','00000008','00000009','00000010','00000011','00000012','00000013','00000014','00000015','00000016','00000017','00000018','00000019','00000020'],
            'RSS202506119988' => ['00000001','00000002','00000003','00000004','00000005','00000006','00000007','00000008','00000009','00000010','00000011','00000012','00000013','00000014','00000015','00000016','00000017','00000018','00000019','00000020'],
            'RSS202506119989' => ['00000001','00000002','00000003','00000004','00000005','00000006','00000007','00000008','00000009','00000010','00000011','00000012','00000013','00000014','00000015','00000016','00000017','00000018','00000019','00000020'],
            'RSS247272826286' => ['00000001','00000002','00000003','00000004','00000005','00000006','00000007','00000008','00000009','00000010','00000011','00000012','00000013','00000014','00000015','00000016','00000017','00000018','00000019','00000020']
        ];

        $insertedCount = 0;
        $now = Carbon::now();

        $maxId = \Illuminate\Support\Facades\DB::connection('attendance')->table('realtime_logs')->max('id') ?? 0;

        foreach ($devices as $deviceId => $biometricUserIds) {
            foreach ($biometricUserIds as $biometricUserId) {
                // Randomly decide whether to punch right now (e.g. 15% chance if running every minute in cron)
                if (rand(1, 100) <= 15) { 
                    $ioMode = rand(0, 1); // 0 = check-in, 1 = check-out
                    $ioTime = $now->format('YmdHis');
                    
                    $maxId++;

                    \Illuminate\Support\Facades\DB::connection('attendance')->table('realtime_logs')->insert([
                        'id'            => $maxId,
                        'user_id'       => $biometricUserId,
                        'device_id'     => $deviceId,
                        'io_time'       => $ioTime,
                        'verify_mode'   => 1,
                        'io_mode'       => $ioMode,
                        'log_data_json' => json_encode([
                            'fk_bin_data_lib' => 'M50',
                            'user_id'         => $biometricUserId,
                            'io_time'         => $ioTime,
                            'verify_mode'     => 1,
                            'io_mode'         => $ioMode,
                        ]),
                        'received_at'   => $now,
                        'client_ip'     => '192.168.1.' . rand(1, 254),
                    ]);

                    $typeString = $ioMode == 0 ? 'Check-in' : 'Check-out';
                    $this->info("Punched $typeString for User: {$biometricUserId} on Device: {$deviceId} at {$ioTime}");
                    
                    $insertedCount++;
                }
            }
        }

        if ($insertedCount === 0) {
            $this->info("No punches generated this time (random chance skipped). Run again to generate.");
        } else {
            $this->info("Successfully generated $insertedCount real-time log(s).");
        }
    }
}
