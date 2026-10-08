<?php

namespace App\Console\Commands;

use Database\Seeders\WhatsAppFeatureSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class RunWhatsAppFeatureSeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'features:seed-whatsapp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run WhatsAppFeatureSeeder (idempotent, can be run on production safely)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $start = microtime(true);
        $this->info('Running WhatsAppFeatureSeeder...');
        Log::info('features:seed-whatsapp started', [
            'trigger'   => app()->runningInConsole() ? 'console/cron' : 'unknown',
            'timestamp' => now()->toDateTimeString(),
        ]);

        Artisan::call('db:seed', [
            '--class' => WhatsAppFeatureSeeder::class,
            '--force' => true,
        ]);
        
        $output = Artisan::output();
        $this->info($output);

        $durationMs = round((microtime(true) - $start) * 1000, 1);
        Log::info('features:seed-whatsapp completed', [
            'artisan_output'    => trim($output),
            'duration_ms'       => $durationMs,
            'timestamp'         => now()->toDateTimeString(),
        ]);
        
        $this->info('WhatsApp features seeding complete.');
        return self::SUCCESS;
    }
}
