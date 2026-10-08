<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Industry;
use App\Models\Box;
use Illuminate\Support\Facades\DB;

class IndustrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $industries = [
            'Agriculture',
            'Automotive',
            'Banking & Finance',
            'Construction',
            'Education',
            'Energy & Utilities',
            'Healthcare & Pharmaceuticals',
            'Information Technology (IT)',
            'Manufacturing',
            'Media & Entertainment',
            'Real Estate',
            'Retail & E-commerce',
            'Telecommunications',
            'Transportation & Logistics',
            'Travel & Hospitality',
            'Other'
        ];

        $boxes = Box::pluck('id')->toArray();
        $now = now();
        $insertData = [];

        // Fetch existing to avoid duplicates
        $existing = Industry::select('box_id', 'name')->get()->map(function($item) {
            return $item->box_id . '-' . $item->name;
        })->toArray();

        if (empty($boxes)) {
            foreach ($industries as $industry) {
                if (!in_array("-" . $industry, $existing)) {
                    $insertData[] = [
                        'box_id' => null,
                        'name' => $industry,
                        'status' => 1,
                        'created_at' => $now,
                        'updated_at' => $now
                    ];
                }
            }
        } else {
            foreach ($boxes as $box_id) {
                foreach ($industries as $industry) {
                    if (!in_array($box_id . "-" . $industry, $existing)) {
                        $insertData[] = [
                            'box_id' => $box_id,
                            'name' => $industry,
                            'status' => 1,
                            'created_at' => $now,
                            'updated_at' => $now
                        ];
                    }
                }
            }
        }

        if (!empty($insertData)) {
            $chunks = array_chunk($insertData, 1000);
            foreach ($chunks as $chunk) {
                Industry::insert($chunk);
            }
        }
    }
}
