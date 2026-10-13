<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\CreateStandardShiftJob;

class CreateStandardShiftsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shifts:create-standard {box_id? : Optional Box ID to create standard shift for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a standard shift for all boxes (or a specific box) based on standard working hours.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $boxId = $this->argument('box_id');

        $this->info("Creating standard shifts" . ($boxId ? " for Box ID: {$boxId}..." : " for all boxes..."));

        (new CreateStandardShiftJob($boxId))->handle();

        $this->info("Successfully processed standard shift creation.");
    }
}
