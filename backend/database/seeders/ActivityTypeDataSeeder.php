<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use JsonMachine\Items;

class ActivityTypeDataSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $boxes = DB::table('boxes')->get();
        $this->command->info('Starting Activity Types Seeder with default types and legacy statuses...');

        $baseDefaultActivityTypes = ['New', 'Follow Up'];
        $baseSpecial8ActivityTypes = ['New order', 'No order', 'New Payments', 'General Discussion', 'Order Follow up', 'Others', 'Payment Follow Up', 'Check Out'];

        foreach ($boxes as $box) {
            // Fetch legacy statuses for this box to use as activity types
            $legacyStatuses = DB::connection('bizzcrm_migrate')
                ->table('activity_statuses')
                ->where('box_id', $box->id)
                ->pluck('name')
                ->toArray();
                
            // Merge and get unique types (Legacy first, case-insensitive deduplication)
            $boxDefaultActivityTypes = [];
            $boxSpecial8ActivityTypes = [];
            
            $seenDefault = [];
            foreach (array_merge($legacyStatuses, $baseDefaultActivityTypes) as $type) {
                $type = trim($type);
                if (empty($type)) continue;
                $lower = strtolower($type);
                if (!in_array($lower, $seenDefault)) {
                    $seenDefault[] = $lower;
                    $boxDefaultActivityTypes[] = $type;
                }
            }
            
            $seenSpecial8 = [];
            foreach (array_merge($legacyStatuses, $baseSpecial8ActivityTypes) as $type) {
                $type = trim($type);
                if (empty($type)) continue;
                $lower = strtolower($type);
                if (!in_array($lower, $seenSpecial8)) {
                    $seenSpecial8[] = $lower;
                    $boxSpecial8ActivityTypes[] = $type;
                }
            }

            $settings = DB::table('calls')->where('box_id', $box->id)->get();
            
            foreach ($settings as $setting) {
                // Insert Leads Activity Types
                foreach ($boxDefaultActivityTypes as $type) {
                    if (empty($type)) continue;

                    $exists = DB::table('activity_types')
                        ->where('box_id', $box->id)
                        ->where('activity_setting_id', $setting->id)
                        ->where('activity_type', $type)
                        ->exists();

                    if (!$exists) {
                        DB::table('activity_types')->insert([
                            'box_id' => $box->id,
                            'activity_setting_id' => $setting->id,
                            'activity_type' => $type,
                            'status' => 1,
                            'target_type' => 'leads',
                            'created_at' => \Carbon\Carbon::now(),
                            'updated_at' => \Carbon\Carbon::now(),
                        ]);
                    }
                }
                
                // Insert Customers Activity Types
                if (in_array($setting->name, ['Call', 'Meeting'])) {
                    foreach ($boxSpecial8ActivityTypes as $type) {
                        if (empty($type)) continue;

                        $exists = DB::table('activity_types')
                            ->where('box_id', $box->id)
                            ->where('activity_setting_id', $setting->id)
                            ->where('activity_type', $type)
                            ->exists();

                        if (!$exists) {
                            DB::table('activity_types')->insert([
                                'box_id' => $box->id,
                                'activity_setting_id' => $setting->id,
                                'activity_type' => $type,
                                'status' => 1,
                                'target_type' => 'customers',
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
