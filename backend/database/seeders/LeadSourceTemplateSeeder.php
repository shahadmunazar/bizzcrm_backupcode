<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeadSourceTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/json/lead_source_templates.json');
        
        if (\Illuminate\Support\Facades\File::exists($path)) {
            $json = \Illuminate\Support\Facades\File::get($path);
            $data = json_decode($json, true);

            foreach ($data as $record) {
                // Remove timestamps if they shouldn't be overridden or just insert them
                \App\Models\LeadSourceTemplate::updateOrCreate(
                    ['id' => $record['id']],
                    $record
                );
            }
            $this->command->info('lead_source_templates table seeded successfully from JSON file.');
        } else {
            $this->command->error("JSON file not found at: {$path}");
        }
    }
}
