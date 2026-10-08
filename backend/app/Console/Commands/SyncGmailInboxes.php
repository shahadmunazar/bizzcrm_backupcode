<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\{GmailInbox, Box, BoxCrmIntegration, GmailMessage};
use App\Http\Controllers\GoogleController;
use Illuminate\Support\Facades\{Http, Log};

class SyncGmailInboxes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gmail:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync unread emails from all enabled Gmail inboxes with pagination and safety checks';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $this->info('Starting Gmail sync...');
        Log::info('Gmail Sync Command Started');
        try {
            $inboxes = GmailInbox::where('status', 'Enabled')->get();
            $googleController = new GoogleController();

            if ($inboxes->isEmpty()) {
                $this->info('No enabled Gmail inboxes found.');
                return 0;
            }

            foreach ($inboxes as $inbox) {
                $this->processInboxBatch($inbox, $googleController);
            }

        } catch (\Exception $e) {
            $this->error('Grand Sync Error: ' . $e->getMessage());
            Log::error('Critical Gmail Sync Failure: ' . $e->getMessage());
        }

        $this->info('Gmail sync process complete.');
        Log::info('Gmail Sync Command Finished');
        return 0;
    }

    /**
     * Process an inbox with pagination support
     */
    protected function processInboxBatch($inbox, $googleController)
    {
        $this->info("Processing inbox: {$inbox->email}");

        try {
            $googleController->refreshTokenIfExpired($inbox);
            $box = $inbox->box;
            if (!$box) {
                $this->error("No box found for inbox: {$inbox->email}");
                return;
            }
            $integration = BoxCrmIntegration::where('box_id', $box->id)
                ->whereHas('api', fn($q) => $q->where('system_name', 'gmail'))
                ->first();

            if (!$integration) {
                $this->warn("Gmail integration not found for box: {$box->name}");
                return;
            }

            $filterData = [
                'round_robin' => $integration->params['round_robin'] ?? 0,
                'round_robin_users' => $integration->params['round_robin_users'] ?? [],
                'assigned_to' => $integration->params['assigned_to'] ?? $box->superadmin_id,
                'lead_source_id' => $integration->params['lead_source_id'] ?? null,
                'lead_status_id' => $integration->params['lead_status_id'] ?? null,
            ];

            $mapper = ['name' => 'name', 'email' => 'email', 'phone' => 'phone', 'query' => 'query'];

            $pageToken = null;
            $syncedCount = 0;
            $maxToScan = 1000; // Increased limit to fetch more messages per run
            $scannedCount = 0;

            do {
                $response = Http::withToken($inbox->access_token)
                    ->get("https://gmail.googleapis.com/gmail/v1/users/me/messages", [
                        'maxResults' => 100,
                        'q' => 'is:unread',
                        'pageToken' => $pageToken
                    ]);

                if ($response->status() === 401 && $googleController->refreshToken($inbox)) {
                    // Retry with new token
                    $response = Http::withToken($inbox->access_token)
                        ->get("https://gmail.googleapis.com/gmail/v1/users/me/messages", [
                            'maxResults' => 100,
                            'q' => 'is:unread',
                            'pageToken' => $pageToken
                        ]);
                }

                if (!$response->successful()) {
                    $this->error("Failed to fetch messages for {$inbox->email}: " . substr($response->body(), 0, 200));
                    break;
                }

                $data = $response->json();
                
                if ($scannedCount === 0 && isset($data['resultSizeEstimate'])) {
                    $this->info("Found approximately {$data['resultSizeEstimate']} emails to sync. This may take a few minutes...");
                    Log::info("Gmail Sync ({$inbox->email}): Found approximately {$data['resultSizeEstimate']} emails to sync.");
                }

                $messages = $data['messages'] ?? [];
                $pageToken = $data['nextPageToken'] ?? null;

                foreach ($messages as $msg) {
                    $scannedCount++;

                    $existingMsg = GmailMessage::where('message_id', $msg['id'])->first();
                    if ($existingMsg && $existingMsg->is_processed) {
                        // Mark as read in Gmail if it's already in our database and processed
                        try {
                            Http::withToken($inbox->access_token)
                                ->post("https://gmail.googleapis.com/gmail/v1/users/me/messages/{$msg['id']}/modify", [
                                    'removeLabelIds' => ['UNREAD']
                                ]);
                        } catch (\Exception $e) {
                        }
                        continue;
                    }

                    // For new messages OR existing but unprocessed messages, run full processing
                    if ($googleController->processEmail($inbox, $msg, $box, $filterData, $mapper)) {
                        $syncedCount++;
                    }

                    if ($scannedCount % 50 === 0) {
                        $this->info("Still working... Scanned {$scannedCount} out of 200+ emails. Please do not cancel.");
                    }

                    if ($scannedCount >= $maxToScan)
                        break 2;
                }

            } while ($pageToken);

            $inbox->update(['last_sync_at' => now()]);
            $this->info("Successfully synced {$syncedCount} new messages (out of {$scannedCount} scanned) for {$inbox->email}");
            Log::info("Gmail Sync ({$inbox->email}): Successfully synced {$syncedCount} new messages (out of {$scannedCount} scanned)");

        } catch (\Exception $e) {
            $this->error("Error syncing {$inbox->email}: " . $e->getMessage());
            Log::error("Console Gmail Sync Error ({$inbox->email}): " . $e->getMessage());
        }
    }
}
