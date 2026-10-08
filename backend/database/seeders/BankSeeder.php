<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use JsonMachine\Items;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        
        $this->command->info("Seeding banks from DB...");
        $stream = DB::connection('bizzcrm_migrate')->table('banks')->cursor();
        $chunk = [];
        $processed = 0;
        
        foreach ($stream as $item) {
            $chunk[] = (array) $item;
            $processed++;
            
            if (count($chunk) >= 500) {
                DB::table('banks')->insertOrIgnore($chunk);
                $chunk = [];
            }
        }
        
        if (count($chunk) > 0) {
            DB::table('banks')->insertOrIgnore($chunk);
        }
        
        $this->command->info("Seeded {$processed} records into banks.");
        Schema::enableForeignKeyConstraints();
    }
}
