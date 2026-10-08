<?php

namespace App\Jobs;

use App\Models\Lead;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class BulkDeleteLeadsJob implements ShouldQueue
{
    use Queueable;

    // Timeout increased to 1 hour to handle very large datasets
    public int $timeout = 3600;

    public function __construct(
        protected array $leadIds,
        protected int $userId
    ) {}

    public function handle(): void
    {
        if (empty($this->leadIds)) {
            return;
        }

        Lead::withTrashed()
            ->whereIn('id', $this->leadIds)
            ->chunkById(500, function ($leads) {
                DB::transaction(function () use ($leads) {
                    // Using native foreach for maximum performance
                    foreach ($leads as $lead) {
                        $lead->update(['last_updated_by' => $this->userId]);
                        $lead->forceDelete(); 
                    }
                });
            });
    }
}
