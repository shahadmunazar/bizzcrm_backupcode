<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BackfillActivitySettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $boxes = DB::table('boxes')->get();

        foreach ($boxes as $box) {
            $this->command->info("Processing Box: {$box->name} (ID: {$box->id})");
            
            // Fetch legacy statuses for this box
            $legacyStatuses = DB::connection('bizzcrm_migrate')
                ->table('activity_statuses')
                ->where('box_id', $box->id)
                ->pluck('name')
                ->toArray();

            // 1. Ensure Activity Settings exist (Call, Email, Meeting, WhatsApp, Notes, General)
            $settings = [
                ['name' => 'Call', 'status' => 1],
                ['name' => 'Email', 'status' => 1],
                ['name' => 'Meeting', 'status' => 1],
                ['name' => 'WhatsApp', 'status' => 1],
                ['name' => 'Notes', 'status' => 1],
                ['name' => 'General', 'status' => 1],
            ];
            
            $activitySettingIds = [];
            
            foreach ($settings as $setting) {
                $existingCall = DB::table('calls')
                    ->where('box_id', $box->id)
                    ->where('name', $setting['name'])
                    ->first();
                    
                if ($existingCall) {
                    $activitySettingIds[$setting['name']] = $existingCall->id;
                } else {
                    $id = DB::table('calls')->insertGetId([
                        'box_id' => $box->id,
                        'name' => $setting['name'],
                        'status' => $setting['status'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                    $activitySettingIds[$setting['name']] = $id;
                }
            }

            // 2. Ensure Activity Types exist
            $baseDefaultActivityTypes = ['New', 'Follow Up'];
            $baseSpecial8ActivityTypes = ['New order', 'No order', 'New Payments', 'General Discussion', 'Order Follow up', 'Others', 'Payment Follow Up', 'Check Out'];
            
            // Merge and get unique types (Legacy first, case-insensitive deduplication)
            $defaultActivityTypes = [];
            $special8ActivityTypes = [];
            
            $seenDefault = [];
            foreach (array_merge($legacyStatuses, $baseDefaultActivityTypes) as $type) {
                $type = trim($type);
                if (empty($type)) continue;
                $lower = strtolower($type);
                if (!in_array($lower, $seenDefault)) {
                    $seenDefault[] = $lower;
                    $defaultActivityTypes[] = $type;
                }
            }
            
            $seenSpecial8 = [];
            foreach (array_merge($legacyStatuses, $baseSpecial8ActivityTypes) as $type) {
                $type = trim($type);
                if (empty($type)) continue;
                $lower = strtolower($type);
                if (!in_array($lower, $seenSpecial8)) {
                    $seenSpecial8[] = $lower;
                    $special8ActivityTypes[] = $type;
                }
            }
            
            $activityTypeIds = [];
            foreach ($activitySettingIds as $settingName => $settingId) {
                // Leads
                foreach ($defaultActivityTypes as $type) {
                    $existingType = DB::table('activity_types')
                        ->where('box_id', $box->id)
                        ->where('activity_setting_id', $settingId)
                        ->where('activity_type', $type)
                        ->where('target_type', 'leads')
                        ->first();
                        
                    if ($existingType) {
                        // Optionally update target_type if it was previously something else or NULL
                        DB::table('activity_types')->where('id', $existingType->id)->update(['target_type' => 'leads']);
                        $activityTypeIds[$settingName][$type] = $existingType->id;
                    } else {
                        $id = DB::table('activity_types')->insertGetId([
                            'box_id' => $box->id,
                            'activity_setting_id' => $settingId,
                            'activity_type' => $type,
                            'status' => 1,
                            'target_type' => 'leads',
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);
                        $activityTypeIds[$settingName][$type] = $id;
                    }
                }

                // Customers
                if (in_array($settingName, ['Call', 'Meeting'])) {
                    foreach ($special8ActivityTypes as $type) {
                        $existingType = DB::table('activity_types')
                            ->where('box_id', $box->id)
                            ->where('activity_setting_id', $settingId)
                            ->where('activity_type', $type)
                            ->where('target_type', 'customers')
                            ->first();
                            
                        if ($existingType) {
                            DB::table('activity_types')->where('id', $existingType->id)->update(['target_type' => 'customers']);
                            $activityTypeIds[$settingName][$type] = $existingType->id;
                        } else {
                            $id = DB::table('activity_types')->insertGetId([
                                'box_id' => $box->id,
                                'activity_setting_id' => $settingId,
                                'activity_type' => $type,
                                'status' => 1,
                                'target_type' => 'customers',
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                            ]);
                            $activityTypeIds[$settingName][$type] = $id;
                        }
                    }
                }
            }

            // 3. Ensure Activity Statuses exist (Mapped by Activity Type)
            $statusesByType = [
                'New' => ['Busy', 'No Answer', 'Unavailable', 'Wrong Number', 'Connected'],
                'Follow Up' => ['Done', 'Not Done', 'Busy', 'No Answer', 'Rescheduled'],
                'New order' => ['Processed', 'Pending', 'Cancelled'],
                'No order' => ['Not Interested', 'Call Back Later'],
                'New Payments' => ['Received', 'Pending', 'Failed'],
                'General Discussion' => ['Completed', 'Pending'],
                'Order Follow up' => ['Done', 'Not Done'],
                'Others' => ['Completed', 'Pending'],
                'Payment Follow Up' => ['Done', 'Not Done'],
                'Check Out' => ['Completed', 'Pending'],
            ];
            
            foreach ($activitySettingIds as $settingName => $settingId) {
                $types = DB::table('activity_types')
                    ->where('box_id', $box->id)
                    ->where('activity_setting_id', $settingId)
                    ->get();
                    
                foreach ($types as $typeObj) {
                    $typeName = $typeObj->activity_type;
                    $typeId = $typeObj->id;
                    
                    // Get base statuses for this specific type, fallback to ['Done', 'Not Done'] if type not found
                    $base = $statusesByType[$typeName] ?? ['Done', 'Not Done'];
                    
                    // Seed legacy statuses first, then base statuses. Prevent duplicates (case-insensitive check)
                    $combined = array_merge($legacyStatuses, $base);
                    $statusList = [];
                    $seenStatuses = [];
                    
                    foreach ($combined as $statusName) {
                        $statusName = trim($statusName);
                        if (empty($statusName)) continue;
                        
                        $lower = strtolower($statusName);
                        if (!in_array($lower, $seenStatuses)) {
                            $seenStatuses[] = $lower;
                            $statusList[] = $statusName;
                        }
                    }
                    
                    foreach ($statusList as $statusName) {
                        $existingStatus = DB::table('activity_statuses')
                            ->where('box_id', $box->id)
                            ->where('activity_setting_id', $settingId)
                            ->where('activity_type_id', $typeId)
                            ->where('activity_status', $statusName)
                            ->first();
                        
                        if (!$existingStatus) {
                            DB::table('activity_statuses')->insert([
                                'box_id' => $box->id,
                                'activity_setting_id' => $settingId,
                                'activity_type_id' => $typeId,
                                'activity_status' => $statusName,
                                'status' => 1,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                            ]);
                        }
                    }
                }
            }
        }
        
        $this->command->info("Successfully backfilled settings, types, and statuses for all boxes!");
    }
}
