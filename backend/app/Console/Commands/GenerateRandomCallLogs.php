<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Box;
use App\Models\CallLog;
use Illuminate\Support\Carbon;

class GenerateRandomCallLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'call-logs:generate-random';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate random call logs for active boxes to simulate live calls';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $boxes = Box::all();

        if ($boxes->isEmpty()) {
            $this->info('No boxes found to generate call logs for.');
            return;
        }

        $names = ['Papa', 'Mummy', 'Bhaiya', 'Client A', 'Client B', 'Supplier C', 'Unknown'];
        $types = ['incoming', 'outgoing', 'missed'];
        
        $baseLat = 25.3586874;
        $baseLong = 82.9986244;

        foreach ($boxes as $box) {
            // Generates a log roughly 60% of the time when the scheduler runs (every minute)
            if (rand(1, 100) > 60) {
                continue;
            }

            $type = $types[array_rand($types)];
            $status = ($type === 'missed') ? 'no_answer' : 'connected';
            $duration = ($type === 'missed') ? 0 : rand(10, 300);
            
            $startTime = now()->subSeconds(rand(1, 60));
            $endTime = (clone $startTime)->addSeconds($duration);
            
            // Use test numbers
            $testNumbers = [
                '6474647812', '9536263009', '6474647887',
                '7340755132', '8968825262', '8872046680', '7876557373', 
                '8054869549', '9017110180', '8198053993', '7009178558',
                '9878310319', '8968825261', '7876998585', '8283045376'
            ];
            $phone = $testNumbers[array_rand($testNumbers)];
            
            CallLog::create([
                'box_id' => $box->id,
                'from' => $phone,
                'to' => $phone,
                'type' => $type,
                'status' => $status,
                'duration' => $duration,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'start_lat' => $baseLat + (rand(-1000, 1000) / 100000),
                'start_long' => $baseLong + (rand(-1000, 1000) / 100000),
                'end_lat' => $baseLat + (rand(-1000, 1000) / 100000),
                'end_long' => $baseLong + (rand(-1000, 1000) / 100000),
                'rawTimestamp' => $startTime->timestamp * 1000,
                'name' => $names[array_rand($names)],
            ]);
        }

        $this->info('Random call logs generated successfully.');
    }
}
