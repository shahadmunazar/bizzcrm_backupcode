<?php
namespace App\Console\Commands;

use Database\Seeders\ModulePermissionSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RunModulePermissionSeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:seed-modules';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run ModulePermissionSeeder (idempotent, no duplicate permissions)';

    public function handle(): int
    {
        $shiftPermissions = [
            'view_shift',
            'add_shift',
            'edit_shift',
            'delete_shift',
            'generate_shift',
        ];

        // Generate a hash of the current permissions configuration
        $currentPermissionsState = [
            'modules' => \App\Helpers\PermissionHelper::allModules(),
            'shift'   => $shiftPermissions,
        ];
        $currentHash = md5(json_encode($currentPermissionsState));
        
        // Retrieve the last known hash from cache
        $cachedHash = \Illuminate\Support\Facades\Cache::get('permissions_seeder_hash');

        if ($cachedHash === $currentHash) {
            $this->info('No permission changes detected. Skipping seeder optimization.');
            Log::info('permissions:seed-modules skipped - no changes detected.');
            return self::SUCCESS;
        }

        $start = microtime(true);
        $this->info('Running ModulePermissionSeeder...');
        Log::info('permissions:seed-modules started', [
            'trigger'   => app()->runningInConsole() ? 'console/cron' : 'unknown',
            'timestamp' => now()->toDateTimeString(),
        ]);
        Artisan::call('db:seed', [
            '--class' => ModulePermissionSeeder::class,
            '--force' => true,
        ]);
        $output = Artisan::output();
        $this->info($output);

        // Seed Shift Permissions
        $this->info('Seeding Shift Permissions...');
        foreach ($shiftPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $durationMs = round((microtime(true) - $start) * 1000, 1);
        Log::info('permissions:seed-modules completed', [
            'artisan_output'    => trim($output),
            'permissions_total' => Permission::count(),
            'roles_total'       => Role::count(),
            'duration_ms'       => $durationMs,
            'timestamp'         => now()->toDateTimeString(),
        ]);
        Log::info('Module permissions seeding complete.');

        // Update the cached hash now that we've successfully seeded
        \Illuminate\Support\Facades\Cache::forever('permissions_seeder_hash', $currentHash);

        return self::SUCCESS;
    }
}
