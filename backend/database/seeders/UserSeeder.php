<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'superadmin',
            'admin',
            'employee',
            'deals',
            'manager',
            'project',
            'leads',
            'project-manager',
        ];

        foreach ($roles as $roleName) {
            $role  = Role::firstOrCreate(['name' => $roleName]);
            $email = $roleName . '@gmail.com';
            $user  = User::firstOrCreate(
                ['email' => $email],
                [
                    'name'     => ucfirst(str_replace('-', ' ', $roleName)) . ' Sample User',
                    'password' => Hash::make('password1231'),
                    'email_verified_at' => now(),
                ]
            );

            if (! $user->hasRole($roleName)) {
                $user->assignRole($roleName);
            }

            // Create a BoxUser record to attach the user to the default box (box_id = 1 or first available)
            $box = \App\Models\Box::first();
            if ($box) {
                $marketingDepartment = \App\Models\Department::where('box_id', $box->id)->where('name', 'Marketing')->first();
                $marketingTeams = collect();
                if ($marketingDepartment) {
                    $marketingTeams = \App\Models\Team::where('box_id', $box->id)
                        ->where('department_id', $marketingDepartment->id)
                        ->where('name', 'like', 'MKT %')
                        ->get();
                }

                $data = ['box_id' => $box->id, 'added_by' => $user->id];
                if (!$user->hasRole(['admin', 'superadmin'])) {
                    if ($marketingDepartment && $marketingTeams->isNotEmpty()) {
                        $data['department'] = $marketingDepartment->id;
                        $data['designation'] = $marketingTeams->random()->id;
                    }
                }

                \App\Models\BoxUser::firstOrCreate(
                    ['user_id' => $user->id],
                    $data
                );
            }
        }
        Permission::firstOrCreate(['name' => 'edit users', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'delete users', 'guard_name' => 'web']);
    }
}
