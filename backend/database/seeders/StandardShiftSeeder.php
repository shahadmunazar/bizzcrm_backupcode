<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Box;
use App\Jobs\CreateStandardShiftJob;

class StandardShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $boxes = Box::all();

        foreach ($boxes as $box) {
            (new CreateStandardShiftJob($box->id))->handle();
        }
    }
}
