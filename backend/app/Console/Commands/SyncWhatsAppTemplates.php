<?php

namespace App\Console\Commands;

use App\Models\Box;
use App\Models\WhatsAppBusinessAccount;
use App\Models\WhatsAppTemplate;
use App\Models\User;
use App\Notifications\WhatsAppTemplateStatusUpdated;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncWhatsAppTemplates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:sync-templates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync WhatsApp templates from Meta API and notify users of status changes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting WhatsApp template sync...');

        // Get all WABA accounts that belong to a box with a token
        $accounts = WhatsAppBusinessAccount::with('box')->get();

        foreach ($accounts as $account) {
            $box = $account->box;
            if (!$box) continue;

            $token = $box->whatsapp_token['access_token'] ?? null;
            if (!$token) {
                $this->warn("No token found for box: {$box->name}");
                continue;
            }

            try {
                $this->info("Syncing templates for WABA: {$account->waba_id} ({$account->name})");
                
                // Fetch from Meta API v20.0
                $response = Http::withToken($token)->get("https://graph.facebook.com/v20.0/{$account->waba_id}/message_templates");
                
                if (!$response->successful()) {
                    $this->error("Failed to fetch templates for WABA {$account->waba_id}: " . $response->body());
                    continue;
                }

                $metaTemplates = $response->json()['data'] ?? [];
                
                foreach ($metaTemplates as $mTemp) {
                    $newStatus = $mTemp['status'] ?? 'PENDING';
                    
                    // Find local template
                    $localTemplate = WhatsAppTemplate::where('box_id', $box->id)
                        ->where('whats_app_id', $mTemp['id'])
                        ->first();

                    if ($localTemplate) {
                        $oldStatus = $localTemplate->whats_app_status;
                        
                        // Update logical status in DB
                        $updateData = [
                            'whats_app_status' => $newStatus,
                            'components' => $mTemp['components'],
                            'category' => $mTemp['category'],
                        ];

                        if ($newStatus === 'APPROVED' && $oldStatus !== 'APPROVED') {
                            $updateData['approved_at'] = now();
                        }
                        $localTemplate->update($updateData);

                        // Check for status change to notify user and log
                        if ($oldStatus !== $newStatus) {
                            $this->info("Status changed for '{$localTemplate->name}': {$oldStatus} -> {$newStatus}");
                            
                            // Log detailed info to Laravel storage logs
                            Log::channel('daily')->info("WhatsApp Template Status Update", [
                                'template_id' => $localTemplate->id,
                                'whats_app_id' => $localTemplate->whats_app_id,
                                'name' => $localTemplate->name,
                                'old_status' => $oldStatus,
                                'new_status' => $newStatus,
                                'category' => $mTemp['category'],
                                'components' => $mTemp['components'],
                                'synced_at' => now()->toDateTimeString()
                            ]);

                            $this->notifyUser($localTemplate, $oldStatus, $newStatus);
                        }
                    } else {
                        // Create new template if it doesn't exist locally
                        $newTemplate = WhatsAppTemplate::create([
                            'box_id' => $box->id,
                            'waba_id' => $account->waba_id,
                            'whats_app_id' => $mTemp['id'],
                            'name' => $mTemp['name'],
                            'category' => $mTemp['category'],
                            'language' => $mTemp['language'],
                            'whats_app_status' => $newStatus,
                            'components' => $mTemp['components'],
                            'status' => 1,
                            'approved_at' => ($newStatus === 'APPROVED') ? now() : null,
                            'added_by' => $box->superadmin_id ?: 1,
                        ]);

                        try {
                            $adminId = $box->superadmin_id ?: 1;
                            $user = User::find($adminId);
                            if ($user) {
                                $user->notify(new \App\Notifications\WhatsAppTemplateCreated($newTemplate));
                            }
                        } catch (\Exception $e) {
                            Log::error("Failed to send discovery notification for template {$newTemplate->id}: " . $e->getMessage());
                        }
                    }
                }
            } catch (\Exception $e) {
                $this->error("Error syncing WABA {$account->waba_id}: " . $e->getMessage());
                Log::error("WhatsApp Sync Cron Error: " . $e->getMessage());
            }
        }

        $this->info('WhatsApp template sync completed.');
    }

    /**
     * Notify the user who added the template about the status change
     */
    protected function notifyUser($template, $oldStatus, $newStatus)
    {
        // Try to find the user who added it, fallback to box superadmin
        $userId = $template->added_by;
        if (!$userId && $template->box) {
            $userId = $template->box->superadmin_id;
        }
        $userId = $userId ?: 1;
        
        $user = User::find($userId);

        if ($user) {
            try {
                $user->notify(new WhatsAppTemplateStatusUpdated($template, $oldStatus, $newStatus));
            } catch (\Exception $e) {
                Log::error("Failed to send status update notification for template {$template->id}: " . $e->getMessage());
            }
        }
    }
}
