<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;

use App\Models\AccountDeletionRequest;
use App\Models\AccountDeletionAudit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProcessAccountDeletion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $requests = AccountDeletionRequest::where('status', 'approved')
            ->whereNotNull('scheduled_deletion_at')
            ->where('scheduled_deletion_at', '<=', Carbon::now())
            ->get();

        foreach ($requests as $request) {
            DB::beginTransaction();
            try {
                // Update Status to Processing
                $request->update(['status' => 'processing']);
                
                AccountDeletionAudit::create([
                    'account_deletion_request_id' => $request->id,
                    'event' => 'Processing Started',
                    'description' => 'Automated account deletion processing started.',
                ]);

                $user = $request->user;

                if ($user) {
                    // Force delete or soft delete the user
                    $user->delete(); // Assuming SoftDeletes on User
                    
                    // Note: In a real-world scenario, here you'd also delete/anonymize 
                    // related records (e.g., messages, files, specific PII data)
                    // according to the business data-retention policy.
                }

                $request->update(['status' => 'completed']);

                AccountDeletionAudit::create([
                    'account_deletion_request_id' => $request->id,
                    'event' => 'Completed',
                    'description' => 'Account deletion processing completed successfully.',
                ]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                
                AccountDeletionAudit::create([
                    'account_deletion_request_id' => $request->id,
                    'event' => 'Error Processing',
                    'description' => 'Error during deletion processing: ' . $e->getMessage(),
                ]);
            }
        }
    }
}
