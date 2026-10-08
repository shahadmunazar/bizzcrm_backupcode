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
            dispatch(new \App\Jobs\DepartmentCreateJob($box->id));
            $this->command->info('Dispatched DepartmentCreateJob for Box ID: ' . $box->id);
        }
    }
}
