<?php
namespace Database\Seeders;

use App\Models\Box;
use App\Models\BoxUser;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\User;
use Illuminate\Database\Seeder;

class BoxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $user = User::updateOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name'              => 'Super Admin',
                'email'             => 'superadmin@gmail.com',
                'password'          => bcrypt('password123'),
                'email_verified_at' => now(),
            ]
        );
        $user->assignRole('superadmin');

        $country = Country::where('name', 'India')->first();
        $state   = State::where('country_id', $country->id)->whereHas('cities')->inRandomOrder()->first();
        $city    = City::where('state_id', $state->id)->inRandomOrder()->first();

        $boxes = [
            [
                'superadmin_id'        => $user->id,
                'name'                 => 'TechCorp Solutions',
                'slug'                 => 'techcorp-solutions',
                'domain'               => 'techcorp.example.com',
                'title'                => 'TechCorp - Leading Technology Solutions',
                'short_title'          => 'TechCorp',
                'logo'                 => null,
                'ip_address'           => '192.168.1.100',
                'status'               => 'Enabled',
                'phone'                => '+1-555-0123',
                'mobile'               => '+1-555-0124',
                'address'              => '123 Tech Street, Silicon Valley',
                'contact_name'         => 'John Tech',
                'pincode'              => '94025',
                'industry'             => 'Technology',
                'country_id'           => $country->id,
                'state_id'             => $state->id,
                'city_id'              => $city->id,
                'daily_reminder_time'  => '08:00',
                'reminder_before'      => 10,
                'lead_required_field'  => 'email',
                'working_days'         => 'Monday,Tuesday,Wednesday,Thursday,Friday',
                'day_start_time'       => '09:00',
                'day_end_time'         => '18:00',
                'round_robin'          => '1',
                'sms_enable'           => 'Yes',
                'assigned_sms'         => 'Yes',
                'assigned_sms_text'    => 'New lead assigned to you: {lead_name}',
                'can_use_admin_mail'   => '1',
                'whatsapp_enable'      => 'Yes',
                'auto_task_close'      => 'Yes',
                'auto_task_close_days' => 7,
            ],
        ];

        foreach ($boxes as $boxData) {
            $createdBox = Box::updateOrCreate(['slug' => $boxData['slug']], $boxData);
            dispatch(new \App\Jobs\DepartmentCreateJob($createdBox->id));
        }
        $box = Box::where('slug', 'techcorp-solutions')->first();
        // $selectedUsers = $users->random($boxUserCount);
        $selectedUsers = User::where('name', 'like', '% Sample User')->get();

        $marketingDepartment = \App\Models\Department::where('box_id', $box->id)->where('name', 'Marketing')->first();
        $marketingTeams = collect();
        if ($marketingDepartment) {
            $marketingTeams = \App\Models\Team::where('box_id', $box->id)
                ->where('department_id', $marketingDepartment->id)
                ->where('name', 'like', 'MKT %')
                ->get();
        }

        foreach ($selectedUsers as $user) {
            $data = ['added_by' => $box->superadmin_id];

            if (!$user->hasRole(['admin', 'superadmin'])) {
                if ($marketingDepartment && $marketingTeams->isNotEmpty()) {
                    $data['department'] = $marketingDepartment->id;
                    $data['designation'] = $marketingTeams->random()->id;
                }
            }

            BoxUser::firstOrCreate([
                'box_id'   => $box->id,
                'user_id'  => $user->id,
            ], $data);
        }

    }
}
