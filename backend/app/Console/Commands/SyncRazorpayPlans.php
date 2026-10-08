<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncRazorpayPlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-razorpay-plans {--list : List all plans from Razorpay} {--import : Import plans from Razorpay to local database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync local plans with Razorpay, list or import existing Razorpay plans';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\RazorpayService $razorpayService)
    {
        if ($this->option('list')) {
            // ... (keep existing list logic)
            $this->info('Fetching plans from Razorpay...');
            $plans = $razorpayService->fetchAllRazorpayPlans();
            
            if (empty($plans['items'])) {
                $this->warn('No plans found on Razorpay.');
                return;
            }

            $headers = ['ID', 'Name', 'Description', 'Amount', 'Currency', 'Period', 'Status'];
            $data = [];
            
            foreach ($plans['items'] as $item) {
                $data[] = [
                    $item['id'],
                    $item['item']['name'],
                    $item['item']['description'] ?? '',
                    $item['item']['amount'] / 100,
                    $item['item']['currency'],
                    $item['period'],
                    $item['item']['active'] ? 'Active' : 'Inactive'
                ];
            }

            $this->table($headers, $data);
            return;
        }

        if ($this->option('import')) {
            $this->info('Importing plans from Razorpay...');
            $razorPlans = $razorpayService->fetchAllRazorpayPlans();
            $count = 0;

            foreach ($razorPlans['items'] as $item) {
                $plan = \App\Models\Plan::updateOrCreate(
                    ['razorpay_plan_id' => $item['id']],
                    [
                        'name' => $item['item']['name'],
                        'slug' => \Illuminate\Support\Str::slug($item['item']['name']),
                        'type' => $item['period'] == 'yearly' ? 'yearly' : 'monthly',
                        'price' => $item['item']['amount'] / 100,
                        'currency' => $item['item']['currency'],
                        'description' => $item['item']['description'] ?? '',
                        'status' => $item['item']['active'] ? 'active' : 'inactive',
                        'interval_count' => $item['interval'] ?? 1,
                    ]
                );
                $count++;
            }

            $this->info("Successfully imported/updated {$count} plans.");
            return;
        }

        $this->info('Syncing local plans to Razorpay...');
        $results = $razorpayService->syncLocalPlansToRazorpay();

        $this->info("Plans Created: {$results['created']}");
        
        if (!empty($results['errors'])) {
            $this->error('Errors encountered:');
            foreach ($results['errors'] as $error) {
                $this->line("- $error");
            }
        }
    }
}
