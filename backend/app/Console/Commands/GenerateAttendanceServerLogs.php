<?php
namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateAttendanceServerLogs extends Command
{
    protected $signature   = 'attendance:generate-logs {--historical : Generate 6 months of historical data}';
    protected $description = 'Generate new server_logs entries automatically for each device';
    public function handle()
    {
        $deviceIDs = [
            'RSS202506119987',
            'RSS202506119988',
            'RSS202506119989',
            'RSS247272826286',
        ];
        
        $maxId = DB::connection('attendance')->table('server_logs')->max('id') ?? 0;

        if ($this->option('historical')) {
            $startDate = Carbon::now()->subMonths(6);
            $endDate = Carbon::now();
            $inserts = [];
            $totalInserted = 0;

            $this->info("Generating historical server_logs from {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}...");

            while ($startDate->lte($endDate)) {
                foreach ($deviceIDs as $deviceId) {
                    // Generate 20-50 random logs per device per day
                    $logCount = rand(20, 50);
                    for ($i = 0; $i < $logCount; $i++) {
                        $maxId++;
                        $randomSecond = rand(0, 86399); // Random second in the day
                        $logTime = $startDate->copy()->startOfDay()->addSeconds($randomSecond);
                        
                        $inserts[] = [
                            'id'           => $maxId,
                            'log_level'    => $this->getRandomLevel(),
                            'message'      => $this->getRandomMessage($deviceId),
                            'client_ip'    => $this->randomIP(),
                            'device_id'    => $deviceId,
                            'request_code' => $this->getRandomRequestCode(),
                            'created_at'   => $logTime->format('Y-m-d H:i:s'),
                        ];
                    }
                }

                if (count($inserts) >= 2000) {
                    DB::connection('attendance')->table('server_logs')->insert($inserts);
                    $totalInserted += count($inserts);
                    $inserts = [];
                }

                $startDate->addDay();
            }

            if (!empty($inserts)) {
                DB::connection('attendance')->table('server_logs')->insert($inserts);
                $totalInserted += count($inserts);
            }

            $this->info("Successfully inserted $totalInserted historical server_logs!");
        } else {
            // Normal real-time behavior (1 log per device)
            foreach ($deviceIDs as $deviceId) {
                $maxId++;
                $data = [
                    'id'           => $maxId,
                    'log_level'    => $this->getRandomLevel(),
                    'message'      => $this->getRandomMessage($deviceId),
                    'client_ip'    => $this->randomIP(),
                    'device_id'    => $deviceId,
                    'request_code' => $this->getRandomRequestCode(),
                    'created_at'   => Carbon::now(),
                ];
                DB::connection('attendance')->table('server_logs')->insert($data);
                $this->info("Inserted new log for Device: $deviceId");
            }
        }
    }
    private function getRandomLevel()
    {
        return collect(['INFO', 'DEBUG', 'RESPONSE', 'WARNING'])->random();
    }
    private function getRandomRequestCode()
    {
        return collect(['receive_cmd', 'send_cmd_result', null])->random();
    }
    private function randomIP()
    {
        return rand(10, 255) . '.' . rand(10, 255) . '.' . rand(10, 255) . '.' . rand(10, 255);
    }
    private function getRandomMessage($deviceId)
    {
        $messages = [
            "Stored command: GET_USER_ID_LIST for device $deviceId",
            "Request received for $deviceId",
            "Response sent successfully for $deviceId",
            "Sending command GET_USER_ID_LIST to device $deviceId",
            "Received result data from device $deviceId",
            "Parsed result and stored temporary block for $deviceId",
            "Saved block file for device $deviceId",
        ];

        return collect($messages)->random();
    }
}
