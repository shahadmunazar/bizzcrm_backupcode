<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\AttendanceServerLog;
use App\Models\BiometricMachine;
use Carbon\Carbon;

class CheckAttendanceServerLogs extends Command
{
    protected $signature = 'attendance:check-logs';
    protected $description = 'Check real-time logs per device and update machine sync status';
    protected int $thresholdMinutes = 3;
    public function handle()
    {
        Log::info('Cron Started: Device sync check...');
        try {
            $machines = BiometricMachine::whereNotNull('serial_no')->cursor();
            foreach ($machines as $machine) {
                try {
                    $this->processDeviceSafely($machine);
                } catch (\Throwable $e) {
                    Log::error("DEVICE ERROR [{$machine->serial_no}]: " . $e->getMessage());
                }
            }
        } catch (\Throwable $e) {
            Log::critical("Cron FAILED: " . $e->getMessage());
        }
        Log::info('Cron Finished.');
    }

    private function processDeviceSafely(BiometricMachine $machine)
    {
        $deviceId = trim($machine->serial_no);
        if (!is_string($deviceId) || strlen($deviceId) < 3) {
            Log::warning("INVALID serial_no '{$deviceId}' on machine {$machine->id}");
            return;
        }
        $latest = AttendanceServerLog::where('device_id', $deviceId)
            ->orderByDesc('id')
            ->first();
        if (!$latest) {
            $this->updateIfChanged($machine, 'red', null);
            Log::warning("$deviceId: No logs found — RED");
            return;
        }
        try {
            $lastTime = Carbon::parse($latest->created_at);
        } catch (\Throwable $e) {
            Log::error("$deviceId: INVALID created_at '{$latest->created_at}'");
            return;
        }
        $gapMinutes = $lastTime->diffInMinutes(now());
        if ($gapMinutes >= $this->thresholdMinutes) {
            $this->updateIfChanged($machine, 'red', $latest->created_at);
            Log::warning("$deviceId: OFFLINE ({$gapMinutes} min) — RED");
            return;
        }
        if ($machine->sync_status !== 'green') {
            $this->updateIfChanged($machine, 'green', $latest->created_at);
            Log::info("$deviceId: BACK ONLINE — GREEN");
            return;
        }
        Log::debug("$deviceId: CONTINUOUS — GREEN (no DB update)");
    }

    private function updateIfChanged(BiometricMachine $machine, string $status, $lastSync)
    {
        if ($machine->sync_status === $status && $machine->last_sync == $lastSync) {
            return;
        }
        try {
            $machine->update([
                'sync_status' => $status,
                'last_sync'   => $lastSync,
            ]);
        } catch (\Throwable $e) {
            Log::error("UPDATE FAILED for {$machine->serial_no}: {$e->getMessage()}");
        }
    }
}
