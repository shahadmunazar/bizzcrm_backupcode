<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Box;
use App\Models\LeaveType;
use Illuminate\Support\Str;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $boxes = Box::all();

        $leaveTypes = [
            [
                'leave_type' => 'Casual Leave',
                'leave_code' => 'CL',
                'leave_icon' => 'ti ti-hexagon-letter-c fs-32',
                'leave_color' => 'bg-purple-le',
                'status' => 'Active',
                'credit_frequency' => 'Monthly',
                'credit_days' => 1.0,
                'number_of_days' => 12,
                'leave_credit_type' => 'Automatic',
                'carry_forward' => 'no',
            ],
            [
                'leave_type' => 'Annual Leave',
                'leave_code' => 'AL',
                'leave_icon' => 'ti ti-calendar-event fs-32',
                'leave_color' => 'bg-black-le',
                'status' => 'Active',
                'credit_frequency' => 'Yearly',
                'credit_days' => 14.0,
                'number_of_days' => 14,
                'leave_credit_type' => 'Automatic',
                'carry_forward' => 'yes',
                'carry_forward_days' => 7,
            ],
            [
                'leave_type' => 'Medical Leave',
                'leave_code' => 'ML',
                'leave_icon' => 'ti ti-vaccine fs-32',
                'leave_color' => 'bg-blue-le',
                'status' => 'Active',
                'credit_frequency' => 'Yearly',
                'credit_days' => 10.0,
                'number_of_days' => 10,
                'leave_credit_type' => 'Automatic',
                'carry_forward' => 'no',
            ],
            [
                'leave_type' => 'Compensatory Off Leave',
                'leave_code' => 'CO',
                'leave_icon' => 'ti ti-hexagonal-prism-plus fs-32',
                'leave_color' => 'bg-pink-le',
                'status' => 'Active',
                'credit_frequency' => 'Manual',
                'credit_days' => 0.0,
                'number_of_days' => 0,
                'leave_credit_type' => 'Manual',
                'carry_forward' => 'no',
            ],
        ];

        foreach ($boxes as $box) {
            foreach ($leaveTypes as $type) {
                LeaveType::updateOrCreate(
                    [
                        'box_id' => $box->id,
                        'leave_type' => $type['leave_type'],
                    ],
                    [
                        'leave_code' => $type['leave_code'],
                        'leave_icon' => $type['leave_icon'],
                        'leave_color' => $type['leave_color'],
                        'status' => $type['status'],
                        'credit_frequency' => $type['credit_frequency'] ?? null,
                        'credit_days' => $type['credit_days'] ?? null,
                        'number_of_days' => $type['number_of_days'] ?? null,
                        'leave_credit_type' => $type['leave_credit_type'] ?? null,
                        'carry_forward' => $type['carry_forward'] ?? 'no',
                        'carry_forward_days' => $type['carry_forward_days'] ?? null,
                        'holiday_off' => 1,
                        'weekly_off' => 1,
                    ]
                );
            }
        }
    }
}
