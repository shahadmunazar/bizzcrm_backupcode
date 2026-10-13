<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('bizzcrm:calculate-storage')->dailyAt('00:00');

// Schedule::command('attendance:generate-logs')->everyMinute();
Schedule::command('attendance:check-logs')->everyMinute()->withoutOverlapping()->runInBackground();
Schedule::command('attendance:process-biometric')->everyMinute()->withoutOverlapping()->runInBackground();
Schedule::command('attendance:auto-punchout')->everyMinute()->withoutOverlapping()->runInBackground();
Schedule::command('gmail:sync')->everyMinute()->withoutOverlapping()->runInBackground();
Schedule::command('leave:process-holiday')->monthlyOn(1, '0:01')->withoutOverlapping()->runInBackground();
Schedule::command('leave:process-credit')->everyMinute()->name('process-leave-credits')->withoutOverlapping()->onOneServer()->runInBackground();
Schedule::command('attendance:mark-absent')->everyMinute()->withoutOverlapping()->runInBackground();
Schedule::command('sync:system-fields-all')->everyMinute()->name('sync-system-fields-all-boxes')->withoutOverlapping()->onOneServer()->runInBackground();
Schedule::command('permissions:seed-modules')->everyMinute()->withoutOverlapping()->runInBackground();
Schedule::command('api:fetch')->everyMinute()->withoutOverlapping()->runInBackground();
Schedule::command('subscription:expiry-reminders')->daily()->withoutOverlapping()->runInBackground();
Schedule::command('leads:check-no-activity')->everyMinute()->withoutOverlapping()->runInBackground();
Schedule::command('whatsapp:sync-templates')->everyMinute()->withoutOverlapping()->runInBackground();
Schedule::command('whatsapp:process-campaigns')->everyMinute()->withoutOverlapping()->runInBackground();
Schedule::command('facebook:sync-leads')->everyMinute()->withoutOverlapping()->onOneServer()->runInBackground();
Schedule::job(new \App\Jobs\ProcessAccountDeletion)->dailyAt('01:00');

Schedule::command('shifts:create-standard')->everyMinute()->withoutOverlapping()->runInBackground(); //for standard shift create
// Schedule::command('call-logs:generate-random')->everyMinute()->withoutOverlapping()->runInBackground();

// Schedule::command('db:seed --class=PlanSeeder')
//     ->everyMinute()
//     ->withoutOverlapping()
//     ->runInBackground();
// Schedule::command('db:seed --class=FaqSeeder')
//     ->everyMinute()
//     ->withoutOverlapping()
//     ->runInBackground();

// Schedule::command('features:seed-whatsapp')
//     ->everyTwoMinutes()
//     ->withoutOverlapping()
//     ->runInBackground();


    // Schedule::command('db:seed --class=PermissionsTableSeeder')
    // ->everyMinute()
    // ->withoutOverlapping()
    // ->runInBackground();

// Schedule::command('employees:backfill')
// ->everyMinute()
// ->withoutOverlapping()
// ->runInBackground();

// Schedule::command('db:seed --class=LeaveTypeSeeder')
// ->everyMinute()
// ->withoutOverlapping()
// ->runInBackground();

// Schedule::command('db:seed --class=IndianHolidaysSeeder')
// ->everyMinute()
// ->withoutOverlapping()
// ->runInBackground();
