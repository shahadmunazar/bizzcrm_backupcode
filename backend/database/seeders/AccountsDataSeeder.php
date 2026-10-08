<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class AccountsDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        $tables = [
            'invoices',
            'invoice_items',
            'receipts',
            'banks',
            'divisions',
            'division_areas',
        ];

        foreach ($tables as $table) {
            $this->command->info("Seeding {$table} from DB...");
            
            $stream = DB::connection('bizzcrm_migrate')->table($table)->cursor();
            $chunk = [];
            $processed = 0;
            
            foreach ($stream as $item) {
                $row = (array) $item;
                $processed++;
                
                foreach ($row as $key => &$value) {
                    if ($value === '0000-00-00') {
                        $value = '1970-01-01';
                    } elseif ($value === '0000-00-00 00:00:00') {
                        $value = '1970-01-01 00:00:00';
                    }
                }
                
                // Map column names for receipts
                if ($table === 'receipts') {
                    if (array_key_exists('added_by', $row)) {
                        $row['created_by'] = $row['added_by'];
                        unset($row['added_by']);
                    }
                    if (array_key_exists('last_updated_by', $row)) {
                        $row['updated_by'] = $row['last_updated_by'];
                        unset($row['last_updated_by']);
                    }
                    if (array_key_exists('invoice_no', $row)) {
                        $row['invoice_number'] = $row['invoice_no'];
                        unset($row['invoice_no']);
                    }
                    if (array_key_exists('reference_number', $row)) {
                        $row['reference'] = $row['reference_number'];
                        unset($row['reference_number']);
                    }
                }
                
                if ($table === 'invoices') {
                    $row['paid_amount'] = 0;
                    $row['balance_amount'] = $row['amount'] ?? 0;
                    $row['due_date'] = null;
                }

                if ($table === 'invoice_items') {
                    $row['sgst'] = 0;
                    $row['cgst'] = 0;
                    $row['igst'] = 0;
                }

                $chunk[] = $row;
                
                if (count($chunk) >= 500) {
                    DB::transaction(function () use ($table, &$chunk) {
                        DB::table($table)->insertOrIgnore($chunk);
                    });
                    $chunk = [];
                }
            }
            
            if (count($chunk) > 0) {
                DB::transaction(function () use ($table, &$chunk) {
                    DB::table($table)->insertOrIgnore($chunk);
                });
            }
            
            $this->command->info("Seeded {$processed} records into {$table}.");
        }

        Schema::enableForeignKeyConstraints();
    }
}
