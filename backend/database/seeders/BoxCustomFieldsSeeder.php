<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class BoxCustomFieldsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::connection()->disableQueryLog();

        $this->command->info('==========================================');
        $this->command->info('Starting Box Custom Fields Seeder from DB...');
        $this->command->info('==========================================');

        // Clean table before seeding
        DB::table('box_custom_fields')->truncate();

        $stream = DB::connection('bizzcrm_migrate')->table('box_custom_fields')->cursor();
        $chunk = [];
        $processed = 0;
        $batchSize = 1000;

        foreach ($stream as $item) {
            $chunk[] = (array) $item;

            if (count($chunk) >= $batchSize) {
                DB::table('box_custom_fields')->insert($chunk);
                $processed += count($chunk);
                $chunk = [];
            }
        }

        if (!empty($chunk)) {
            DB::table('box_custom_fields')->insert($chunk);
            $processed += count($chunk);
        }

        $this->command->info("✅ Successfully imported {$processed} Box Custom Fields!");

        // Automatically generate the data_{box_id} tables and their columns based on the newly seeded custom fields
        $this->command->info("🛠️ Generating dynamic box custom tables...");
        \Illuminate\Support\Facades\Artisan::call('crm:generate-box-tables');
        $this->command->info(\Illuminate\Support\Facades\Artisan::output());
    }
}
