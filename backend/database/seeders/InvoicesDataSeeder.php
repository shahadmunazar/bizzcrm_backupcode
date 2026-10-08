<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvoicesDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();

        // Migrate Invoices
        $invoices = DB::connection('bizzcrm_migrate')->table('invoices')->get();
        $invoiceRows = [];
        foreach ($invoices as $invoice) {
            $row = (array) $invoice;
            $row['paid_amount'] = 0;
            $row['balance_amount'] = $row['amount'];
            $row['due_date'] = null;
            $invoiceRows[] = $row;
        }

        foreach (array_chunk($invoiceRows, 500) as $chunk) {
            DB::table('invoices')->insertOrIgnore($chunk);
        }

        // Migrate Invoice Items
        $invoiceItems = DB::connection('bizzcrm_migrate')->table('invoice_items')->get();
        $itemRows = [];
        foreach ($invoiceItems as $item) {
            $row = (array) $item;
            $itemRows[] = $row;
        }

        foreach (array_chunk($itemRows, 500) as $chunk) {
            DB::table('invoice_items')->insertOrIgnore($chunk);
        }

        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $this->command->info('Invoices and Invoice Items migrated successfully from bizzcrm_migrate.');
    }
}
