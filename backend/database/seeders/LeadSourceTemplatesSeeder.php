<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LeadSourceTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting Migration from lead_source_templates (bizzcrm_migrate) to main database...');

        // Fetch data from old database
        $oldTemplates = DB::connection('bizzcrm_migrate')->table('lead_source_templates')->get();

        if ($oldTemplates->isEmpty()) {
            $this->command->warn('No records found in lead_source_templates table.');
            return;
        }

        $insertedCount = 0;
        $updatedCount = 0;

        foreach ($oldTemplates as $oldTemplate) {
            // Check if it exists in the new table by id or indetifier
            $existing = DB::table('lead_source_templates')->where('id', $oldTemplate->id)->orWhere('indetifier', $oldTemplate->indetifier)->first();

            $data = [
                'name' => $oldTemplate->name,
                'indetifier' => $oldTemplate->indetifier,
                'system_fields' => $oldTemplate->system_fields,
                'source_fields' => $oldTemplate->source_fields,
                'status' => $oldTemplate->status,
                'has_word' => $oldTemplate->has_word,
                'from' => $oldTemplate->from,
                'created_at' => $oldTemplate->created_at ?? Carbon::now(),
                'updated_at' => Carbon::now()
            ];

            if ($existing) {
                // Update
                DB::table('lead_source_templates')
                    ->where('id', $existing->id)
                    ->update($data);
                $updatedCount++;
            } else {
                // Insert with original ID if possible
                $data['id'] = $oldTemplate->id;
                try {
                    DB::table('lead_source_templates')->insert($data);
                    $insertedCount++;
                } catch (\Exception $e) {
                    // Fallback to auto-increment ID if ID collision happens
                    unset($data['id']);
                    DB::table('lead_source_templates')->insert($data);
                    $insertedCount++;
                }
            }
        }
        
        $this->command->info("Migration completed successfully! Inserted: $insertedCount | Updated: $updatedCount");
    }
}
