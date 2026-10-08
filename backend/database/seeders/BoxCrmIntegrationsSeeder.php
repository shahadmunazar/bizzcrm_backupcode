<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BoxCrmIntegrationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // You can define explicit mappings here if the columns in `plugin_boxes` (old lead_api_boxes)
        // have different names than `box_crm_integrations`.
        // Format: 'TargetColumn' => 'SourceColumn'
        $columnMapping = [
            'lead_api_id' => 'lead_api_id', // Changed back to lead_api_id since the actual table has it
            'box_id'      => 'box_id',
            'status'      => 'status',
            'connection_status' => 'connection_status', // May not exist in old table
            'last_checked_date' => 'last_checked_date',
            'error_log'   => 'error_log',
            'params'      => 'params',
            'last_lead_date' => 'last_lead_date', // May not exist in old table
            'token'       => 'token',
            'created_at'  => 'created_at',
            'updated_at'  => 'updated_at',
            'deleted_at'  => 'deleted_at',
        ];

        // Mapping from legacy lead_apis IDs to new crm_integrations IDs
        $idMapping = [
            1 => 2, // India Mart
            2 => 7, // Pharma Khabar
            3 => 9, // Trade India
            4 => 8, // Trade XL
            5 => 5, // 99 Acers
            6 => 6, // Web Hook
            7 => 3, // Pharma Hopers
            8 => 4, // Pharma Vends
            9 => 1, // Facebook
        ];

        // Fetch data from lead_api_boxes which was imported into bizzcrm
        $leadApiBoxes = DB::connection('bizzcrm_migrate')->table('lead_api_boxes')->get();

        foreach ($leadApiBoxes as $item) {
            
            // Helper function to safely get mapped column data
            $getValue = function($targetCol, $default = null) use ($item, $columnMapping) {
                $sourceCol = $columnMapping[$targetCol] ?? $targetCol;
                return property_exists($item, $sourceCol) ? $item->{$sourceCol} : $default;
            };

            $oldApiId = $getValue('lead_api_id');
            $newApiId = $idMapping[$oldApiId] ?? $oldApiId; // fallback to original if not found in map

            DB::table('box_crm_integrations')->updateOrInsert(
                [
                    // We use ID if it exists, otherwise we match by lead_api_id & box_id combination
                    'id' => property_exists($item, 'id') ? $item->id : null,
                ],
                [
                    'lead_api_id' => $newApiId,
                    'box_id' => $getValue('box_id'),
                    'status' => $getValue('status', 'active'),
                    'connection_status' => $getValue('connection_status'),
                    'last_checked_date' => $getValue('last_checked_date'),
                    'error_log' => $getValue('error_log'),
                    'params' => empty($getValue('params')) ? null : $getValue('params'),
                    'last_lead_date' => $getValue('last_lead_date'),
                    'token' => $getValue('token'),
                    'created_at' => $getValue('created_at', now()),
                    'updated_at' => $getValue('updated_at', now()),
                    'deleted_at' => $getValue('deleted_at'),
                ]
            );
        }
    }
}
