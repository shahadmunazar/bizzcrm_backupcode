<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmployeeType;
use App\Models\Box;

class EmployeeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            'Full-Time',
            'Part-Time',
            'Contract',
            'Internship',
            'Freelance'
        ];

        $boxes = Box::pluck('id')->toArray();
        $now = now();
        $insertData = [];

        // Fetch existing to avoid duplicates, as there is no unique constraint
        $existing = EmployeeType::select('box_id', 'name')->get()->map(function($item) {
            return ($item->box_id ?? '') . '-' . $item->name;
        })->toArray();

        // 1. Always ensure global system defaults (box_id = null)
        foreach ($types as $type) {
            if (!in_array("-" . $type, $existing)) {
                $insertData[] = [
                    'box_id' => null,
                    'name' => $type,
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now
                ];
                $existing[] = "-" . $type;
            }
        }

        // 2. Ensure per-box employee types for all existing boxes
        foreach ($boxes as $boxId) {
            foreach ($types as $type) {
                if (!in_array($boxId . "-" . $type, $existing)) {
                    $insertData[] = [
                        'box_id' => $boxId,
                        'name' => $type,
                        'status' => 'active',
                        'created_at' => $now,
                        'updated_at' => $now
                    ];
                    $existing[] = $boxId . "-" . $type;
                }
            }
        }

        // Chunk bulk insert
        if (!empty($insertData)) {
            $chunks = array_chunk($insertData, 1000);
            foreach ($chunks as $chunk) {
                EmployeeType::insert($chunk);
            }
        }
    }
}
