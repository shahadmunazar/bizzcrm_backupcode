<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use JsonMachine\Items;

class ActivityStatusDataSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $boxes = DB::table('boxes')->get();
        $this->command->info('Starting Activity Statuses Seeder with default statuses and legacy statuses...');

        $baseStatuses = [
            'Call' => ['Busy', 'No Answer', 'Unavailable', 'Wrong Number', 'Done', 'Not Done'],
            'Meeting' => ['Done', 'Not Done', 'Busy', 'No Answer'],
            'Email' => ['Done', 'Not Done'],
            'WhatsApp' => ['Done', 'Not Done'],
            'Notes' => ['Done', 'Not Done'],
            'General' => ['Done', 'Not Done'],
        ];

        foreach ($boxes as $box) {
            // Fetch legacy statuses for this box
            $legacyStatuses = DB::connection('bizzcrm_migrate')
                ->table('activity_statuses')
                ->where('box_id', $box->id)
                ->pluck('name')
                ->toArray();

            $settings = DB::table('calls')->where('box_id', $box->id)->get();
            foreach ($settings as $setting) {
                // Get base statuses for this setting, default to ['Done', 'Not Done'] if not found
                $base = $baseStatuses[$setting->name] ?? ['Done', 'Not Done'];
                
                // Seed legacy statuses first, then base statuses. Prevent duplicates (case-insensitive check)
                $combined = array_merge($legacyStatuses, $base);
                $mergedStatuses = [];
                $seen = [];
                
                foreach ($combined as $statusName) {
                    $statusName = trim($statusName);
                    if (empty($statusName)) continue;
                    
                    $lower = strtolower($statusName);
                    if (!in_array($lower, $seen)) {
                        $seen[] = $lower;
                        $mergedStatuses[] = $statusName;
                    }
                }

                $types = DB::table('activity_types')
                    ->where('box_id', $box->id)
                    ->where('activity_setting_id', $setting->id)
                    ->get();
                    
                foreach ($mergedStatuses as $statusName) {
                    if (empty($statusName)) continue;
                    foreach ($types as $type) {
                        $exists = DB::table('activity_statuses')
                            ->where('box_id', $box->id)
                            ->where('activity_setting_id', $setting->id)
                            ->where('activity_type_id', $type->id)
                            ->where('activity_status', $statusName)
                            ->exists();

                        if (!$exists) {
                            DB::table('activity_statuses')->insert([
                                'box_id' => $box->id,
                                'activity_setting_id' => $setting->id,
                                'activity_type_id' => $type->id,
                                'activity_status' => $statusName,
                                'status' => 1,
                                'created_at' => \Carbon\Carbon::now(),
                                'updated_at' => \Carbon\Carbon::now(),
                            ]);
                        }
                    }
                }
            }
        }

        Schema::enableForeignKeyConstraints();
    }
}
