<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkModeSetting;
use App\Models\Box;

class WorkModeSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $modes = [
            'Work From Home',
            'Work from Office',
            'Field',
            'Hybrid'
        ];

        $boxes = Box::pluck('id')->toArray();
        $now = now();
        $insertData = [];

        // Fetch existing to avoid duplicates
        $existing = WorkModeSetting::select('box_id', 'name')->get()->map(function($item) {
            return $item->box_id . '-' . $item->name;
        })->toArray();

        if (empty($boxes)) {
            foreach ($modes as $mode) {
                if (!in_array("-" . $mode, $existing)) {
                    $insertData[] = [
                        'box_id' => null,
                        'name' => $mode,
                        'status' => 1,
                        'created_at' => $now,
                        'updated_at' => $now
                    ];
                }
            }
        } else {
            foreach ($boxes as $boxId) {
                foreach ($modes as $mode) {
                    if (!in_array($boxId . "-" . $mode, $existing)) {
                        $insertData[] = [
                            'box_id' => $boxId,
                            'name' => $mode,
                            'status' => 1,
                            'created_at' => $now,
                            'updated_at' => $now
                        ];
                    }
                }
            }
        }

        // Chunk bulk insert
        $chunks = array_chunk($insertData, 1000);
        foreach ($chunks as $chunk) {
            WorkModeSetting::insert($chunk);
        }
    }
}
