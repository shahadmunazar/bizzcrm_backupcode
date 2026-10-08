<?php

namespace App\Jobs;

use App\Models\Box;
use App\Helpers\WhatsApp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncFacebookFormsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $box;
    protected $accessToken;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 300;

    /**
     * Create a new job instance.
     *
     * @param Box $box
     * @param string $accessToken
     */
    public function __construct(Box $box, $accessToken)
    {
        $this->box = $box;
        $this->accessToken = $accessToken;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('SyncFacebookFormsJob started', [
            'box_id' => $this->box->id,
            'box_slug' => $this->box->slug
        ]);

        try {
            $whatsapp = new WhatsApp($this->box);
            
            // This method handles fetching all pages AND their forms
            Log::info('SyncFacebookFormsJob: Fetching pages and forms', ['box_id' => $this->box->id]);
            $pagesResult = $whatsapp->getPages($this->box);
            
            if (!$pagesResult['success']) {
                Log::error('SyncFacebookFormsJob: Failed to fetch data', [
                    'box_id' => $this->box->id,
                    'error' => $pagesResult['error'] ?? 'Unknown error'
                ]);
            } else {
                Log::info('SyncFacebookFormsJob completed successfully', ['box_id' => $this->box->id]);
            }
            
        } catch (\Exception $e) {
            Log::error('SyncFacebookFormsJob failed with exception', [
                'box_id' => $this->box->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
