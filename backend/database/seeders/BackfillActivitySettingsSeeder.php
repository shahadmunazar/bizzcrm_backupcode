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
            $defaultActivityTypes = ['New', 'Follow Up'];
            $special8ActivityTypes = ['New order', 'No order', 'New Payments', 'General Discussion', 'Order Follow up', 'Others', 'Payment Follow Up', 'Check Out'];
            
            $activityTypeIds = [];
            foreach ($activitySettingIds as $settingName => $settingId) {
                // Leads
                foreach ($defaultActivityTypes as $type) {
                    $existingType = DB::table('activity_types')
                        ->where('box_id', $box->id)
                        ->where('activity_setting_id', $settingId)
                        ->where('activity_type', $type)
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

            // 3. Ensure Activity Statuses exist
            $statuses = [
                'Call' => ['Busy', 'No Answer', 'Unavailable', 'Wrong Number'],
                'Meeting' => ['Done', 'Not Done'],
            ];
            
            foreach ($statuses as $settingName => $statusList) {
                foreach ($statusList as $statusName) {
                    $createdTypesForSetting = array_keys($activityTypeIds[$settingName] ?? []);
                    
                    foreach ($createdTypesForSetting as $type) {
                        $typeId = $activityTypeIds[$settingName][$type];
                        
                        $existingStatus = DB::table('activity_statuses')
                            ->where('box_id', $box->id)
                            ->where('activity_setting_id', $activitySettingIds[$settingName])
                            ->where('activity_type_id', $typeId)
                            ->where('activity_status', $statusName)
                            ->first();
                        
                        if (!$existingStatus) {
                            DB::table('activity_statuses')->insert([
                                'box_id' => $box->id,
                                'activity_setting_id' => $activitySettingIds[$settingName],
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
