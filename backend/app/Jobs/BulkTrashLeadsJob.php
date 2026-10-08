<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Models\BoxUser;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class BulkTrashLeadsJob implements ShouldQueue
{
    use Queueable;

    // Timeout increased to 1 hour to handle very large datasets (e.g. 100k records)
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

        $user = User::find($this->userId);

        // Preload BoxUsers ONCE efficiently
        $boxUsers = BoxUser::with('user')
            ->where('user_id', $this->userId)
            ->get()
            ->keyBy('box_id');

        // Chunk size 500 covers 100k records in ~200 iterations
        Lead::whereIn('id', $this->leadIds)
            ->chunkById(500, function ($leads) use ($boxUsers, $user) {

                DB::transaction(function () use ($leads, $boxUsers, $user) {

                    // Using native foreach for maximum performance (Benchmark: Best for >10k records)
                    foreach ($leads as $lead) {
                        $trasherName = 'Unassigned';

                        if ($boxUsers->has($lead->box_id)) {
                            $boxUser = $boxUsers[$lead->box_id];

                            $trasherName = $boxUser->user->name
                                ?? trim($boxUser->first_name . ' ' . $boxUser->last_name);
                        } elseif ($user) {
                            $trasherName = $user->name;
                        }

                        Activity::create([
                            'box_id'          => $lead->box_id,
                            'activity_id'     => $lead->id,
                            'activity_type'   => 'leads',
                            'type'            => 'history',
                            'subject'         => "{$trasherName} Trashed the Lead",
                            'description'     => "Lead moved to trash by {$trasherName}",
                            'added_by'        => $this->userId,
                            'last_updated_by' => $this->userId,
                            'status'          => 'Close',
                            'completed_date'  => now(),
                            'params'          => json_encode([]),
                        ]);

                        $lead->update([
                            'last_updated_by' => $this->userId,
                            'trashed_at'      => now(),
                        ]);

                        $lead->delete(); // soft delete
                    }
                });
            });
    }
}
