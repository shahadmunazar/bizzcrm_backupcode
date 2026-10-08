<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Database\Seeders\CrmIntegrationSeeder;
use Illuminate\Support\Facades\Log;

class SeedCrmIntegrationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('SeedCrmIntegrationsJob started.');
        try {
            $seeder = new CrmIntegrationSeeder();
            $seeder->run();
            Log::info('SeedCrmIntegrationsJob completed successfully.');
        } catch (\Exception $e) {
            Log::error('SeedCrmIntegrationsJob failed: ' . $e->getMessage());
        }
    }
}
