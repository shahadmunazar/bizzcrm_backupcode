<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $permissions = [
            'view users',
            'add users',
            'edit users',
            'delete users',
            'view roles',
            'assign roles',
            'manage settings',
            'all.permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create role and assign permissions
        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $role->givePermissionTo(['view users', 'add users']);
                               // Assign permission to a user
        $user = User::find(1); // or Auth::user() if needed
        if ($user) {
            $user->givePermissionTo('all.permissions');
        }
    }
}
