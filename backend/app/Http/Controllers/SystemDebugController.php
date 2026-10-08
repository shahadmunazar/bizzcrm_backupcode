<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class SystemDebugController extends Controller
{
    public function index()
    {
        // 1. Get Schedule List
        Artisan::call('schedule:list');
        $scheduleList = Artisan::output();

        // 2. Get Last 100 lines of Laravel Log
        $logPath = storage_path('logs/laravel.log');
        $logs = "Log file not found.";
        if (File::exists($logPath)) {
            $file = new \SplFileObject($logPath, 'r');
            $file->seek(PHP_INT_MAX);
            $totalLines = $file->key();
            $start = max(0, $totalLines - 100);
            
            $logsProcessed = [];
            $it = new \LimitIterator($file, $start, 100);
            $file->seek($start);
            while (!$file->eof()) {
                $logsProcessed[] = $file->fgets();
            }
            $logs = implode("", array_filter($logsProcessed));
        }

        // 3. Get Cache Locks info
        $locks = DB::table('cache_locks')->orderBy('expiration', 'desc')->limit(10)->get();

        return view('frontend.system.debug', compact('scheduleList', 'logs', 'locks'));
    }

    public function runCommand($command)
    {
        $allowedCommands = ['api:fetch', 'attendance:check-logs', 'biometric:sync', 'queue:restart'];
        
        if (!in_array($command, $allowedCommands)) {
            return response()->json(['status' => 'error', 'message' => 'Command not allowed.']);
        }

        try {
            Artisan::call($command);
            $output = Artisan::output();
            return response()->json([
                'status' => 'success',
                'message' => "Command '$command' executed.",
                'output' => $output
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
