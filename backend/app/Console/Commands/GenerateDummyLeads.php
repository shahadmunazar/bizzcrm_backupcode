<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\{Box, Lead, User};
use App\Http\Controllers\Leads\LeadsController;
use Carbon\Carbon;

class GenerateDummyLeads extends Command
{
    protected $signature = 'leads:generate-dummy';
    protected $description = 'Generate continuous dummy India Mart leads for testing Round Robin';

    public function handle()
    {
        $controller = new LeadsController();
        
        // Fetch all active India Mart integrations (API ID 2)
        $integrations = \App\Models\BoxCrmIntegration::with(['box', 'users'])
            ->where('lead_api_id', 2)
            ->where('status', 'Enabled')
            ->get();

        if ($integrations->isEmpty()) {
            $this->warn("No active India Mart integrations found.");
            return;
        }

        foreach ($integrations as $integration) {
            $box = $integration->box;
            if (!$box) continue;

            $this->info("Processing Box: {$box->slug} (ID: {$box->id})");
            
            // Re-use logic for filterData from ApiFetch to get RR users
            $params = $integration->params ?? [];
            $linkedUsers = $integration->users->pluck('id')->toArray();
            $rrUsers = !empty($linkedUsers) ? $linkedUsers : ($params['round_robin_users'] ?? []);
            $roundRobin = !empty($linkedUsers) ? 1 : ($params['round_robin'] ?? 0);

            $this->info("-> Using RR Users: [" . implode(', ', $rrUsers) . "]");

            $filterData = [
                'lead_source_id' => $params['lead_source_id'] ?? $params['source'] ?? null,
                'lead_status_id' => $params['lead_status_id'] ?? null,
                'assigned_to' => $params['assigned_to'] ?? null,
                'round_robin' => $roundRobin,
                'round_robin_users' => $rrUsers,
                'enable_shifts' => $params['enable_shifts'] ?? 'No',
                'shifts' => $params['shifts'] ?? [],
            ];

            // Standard mapper for saveLead to process India Mart payload
            $mapper = [
                'SENDER_NAME' => 'name',
                'SENDER_EMAIL' => 'email',
                'SENDER_MOBILE' => 'phone',
                'SENDER_COMPANY' => 'company_name',
                'SENDER_ADDRESS' => 'address',
                'QUERY_MESSAGE' => 'query',
            ];

            $lastPayload = null;
            // Generate a Batch of 3 NEW Leads to see RR in action
            for ($i = 1; $i <= 3; $i++) {
                $timestamp = microtime(true);
                $payload = [
                    'UNIQUE_QUERY_ID' => 'IM_DUMMY_' . $timestamp,
                    'SENDER_NAME' => "Batch Lead #{$i} " . date('H:i:s'),
                    'SENDER_EMAIL' => "batch_{$i}_" . str_replace('.', '', $timestamp) . "@example.com",
                    'SENDER_MOBILE' => '91' . rand(7000000000, 9999999999),
                    'SENDER_COMPANY' => 'Mock Batch Inc',
                    'SENDER_ADDRESS' => 'Batch Street',
                    'QUERY_MESSAGE' => "Round Robin Batch Lead #{$i} generated at " . now(),
                ];
                $lastPayload = $payload;

                $this->info("  - Generating NEW lead #{$i}...");
                $lead = $controller->saveLead($box, $payload, $filterData, $mapper, now(), $box->superadmin_id, 'India Mart Dummy Batch');
                $this->info("    -> ID: {$lead->id}, Assigned To: " . ($lead->assignedTo->name ?? "User {$lead->assigned_to}") . " (Duplicate: No)");
            }

            // Also generate ONE Duplicate of the last batch lead to verify preservation + "Added also"
            if ($lastPayload) {
                $this->info("  - Generating DUPLICATE lead (Second Inquiry for Lead #3)...");
                $duplicateMatch = $controller->saveLead($box, $lastPayload, $filterData, $mapper, now(), $box->superadmin_id, 'India Mart Dummy Batch (Duplicate)');
                $this->info("    -> ID: {$duplicateMatch->id}, Assigned To: " . ($duplicateMatch->assignedTo->name ?? "User {$duplicateMatch->assigned_to}") . " (Duplicate: Yes)");
            }
        }
    }
}
