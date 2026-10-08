<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Box;
use App\Jobs\SyncFacebookLeadsJob;
use Illuminate\Support\Facades\Log;

class SyncFacebookLeadsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facebook:sync-leads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize Facebook leads for all boxes with Facebook integration enabled';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Facebook Leads Synchronization...');
        
        $boxes = Box::whereNotNull('facebook_api_params')
            ->whereNotNull('facebook_api_params->access_token')
            ->get();

        $count = $boxes->count();
        $this->info("Found {$count} boxes with Facebook integration.");

        foreach ($boxes as $box) {
            $this->info("Dispatching sync job for box: {$box->slug}");
            
            try {
                SyncFacebookLeadsJob::dispatch($box)->onQueue('facebook');
                Log::info("FacebookSyncCommand: Dispatched SyncFacebookLeadsJob for box {$box->slug} on facebook queue");
            } catch (\Exception $e) {
                $this->error("Failed to dispatch sync job for box {$box->slug}: " . $e->getMessage());
                Log::error("FacebookSyncCommand Error: " . $e->getMessage(), [
                    'box_slug' => $box->slug,
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        $this->info('Facebook Leads Synchronization jobs dispatched successfully.');
        return Command::SUCCESS;
    }
}
