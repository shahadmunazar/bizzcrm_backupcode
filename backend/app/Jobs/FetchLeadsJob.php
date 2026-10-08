<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\Leads\LeadsController; // We will use the static builder
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\BoxCustomField;
use Illuminate\Support\Facades\Schema;

class FetchLeadsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $jobId;
    public $boxId;
    public $params;
    public $userId;

    /**
     * Create a new job instance.
     *
     * @param string $jobId
     * @param int $boxId
     * @param array $params (Request parameters)
     * @param int $userId
     */
    public function __construct($jobId, $boxId, $params, $userId)
    {
        $this->jobId = $jobId;
        $this->boxId = $boxId;
        $this->params = $params;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $start = microtime(true);
        \Log::info("FetchLeadsJob [{$this->jobId}]: Started");
        
        // Increase limits
        ini_set('memory_limit', '1024M'); // Bump to 1GB to be safe
        set_time_limit(300); // 5 minutes max execution

        try {
            \Log::info("FetchLeadsJob [{$this->jobId}]: Building Query");
            
            // 1. Build Query
            $leadsQuery = LeadsController::buildLeadsQuery($this->boxId, $this->params, true);
            
            $limit = $this->params['length'] ?? 10;
            $offset = $this->params['start'] ?? 0;
            
            // Validate limit
            $limit = intval($limit);
            $offset = intval($offset);

            if ($limit != -1) {
                $leadsQuery->skip($offset)->take($limit);
            }
            
            \Log::info("FetchLeadsJob [{$this->jobId}]: Query Built. Fetching Custom Field Defs.");

            // 2. Get Custom Field Definitions
            $customFields = BoxCustomField::where('box_id', $this->boxId)
                ->where('object', 'leads')
                ->get();

            $transformed = [];
            $count = 0;

            \Log::info("FetchLeadsJob [{$this->jobId}]: Starting Cursor Iteration");

            // 3. Iterate with Cursor (Low Memory)
            foreach ($leadsQuery->cursor() as $lead) {
                $count++;
                
                // transform row
                $row = $lead->toArray();
                
                // Relationships
                $row['source'] = $lead->leadSource ? $lead->leadSource->name : null;
                $row['industry'] = $lead->industryRelation ? $lead->industryRelation->name : null;
                $row['last_updated_by'] = $lead->updatedBy ? $lead->updatedBy->name : ($lead->last_updated_by ?? null);
                
                $row['country_id'] = $lead->country ? $lead->country->name : null;
                $row['state_id'] = $lead->state ? $lead->state->name : null;
                $row['city_id'] = $lead->city ? $lead->city->name : null;
                
                // Custom Data
                $cData = [];
                foreach ($customFields as $field) {
                    $key = 'custom_' . $field->col_name;
                    $cData[$field->col_name] = $lead->$key ?? null;
                }
                $row['custom_data'] = (object)$cData;
                
                $transformed[] = $row;

                // Safety: Avoid too huge payloads for simple AJAX
                if ($count > 20000) {
                     \Log::warning("FetchLeadsJob [{$this->jobId}]: Hit 20k safety limit. Cutting off.");
                     break; 
                }
            }
            
            $duration = round(microtime(true) - $start, 2);
            \Log::info("FetchLeadsJob [{$this->jobId}]: Iteration Complete. Processed $count records in {$duration}s.");

            // 4. Cache Result
            // Use try-catch specifically for Cache writing, as 10k rows might be big
            try {
                Cache::put('leads_fetch_' . $this->jobId, [
                    'data' => $transformed,
                    'draw' => intval($this->params['draw'] ?? 1),
                    'recordsTotal' => $count, 
                    'recordsFiltered' => $count
                ], 300); // 5 minutes
                \Log::info("FetchLeadsJob [{$this->jobId}]: Cache Write Success.");
            } catch (\Throwable $ce) {
                \Log::error("FetchLeadsJob [{$this->jobId}]: Cache Write Failed: " . $ce->getMessage());
                throw $ce;
            }

        } catch (\Throwable $e) {
            \Log::error("FetchLeadsJob [{$this->jobId}] Failed: " . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            // Attempt to write error to cache so frontend sees it
            try {
                Cache::put('leads_fetch_' . $this->jobId, ['error' => 'Job Failed: ' . $e->getMessage()], 300);
            } catch (\Throwable $ce) {
                // If cache fails, we can't do much
            }
            // Do not rethrow if we want to avoid "FAIL" in queue list, but usually better to FAIL.
            // But since we handled the user notification via Cache, we might return normally? 
            // No, let's throw so admin sees fail.
            throw $e;
        }
    }
}
