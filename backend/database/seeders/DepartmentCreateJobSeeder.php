<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentCreateJobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $boxes = \App\Models\Box::all();
        
        foreach ($boxes as $box) {
            (new \App\Jobs\DepartmentCreateJob($box->id))->handle();
            $this->command->info('Created departments and teams for Box ID: ' . $box->id);
        }
    }
}
