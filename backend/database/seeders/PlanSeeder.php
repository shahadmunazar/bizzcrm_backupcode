<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PlanSeeder extends Seeder
{
    public function run()
    {
        $plans = [
            // Monthly Plans
            [
                'name' => 'Basic',
                'slug' => 'basic-monthly',
                'type' => 'monthly',
                'price' => 2499,
                'currency' => 'INR',
                'interval_count' => 1,
                'description' => 'For individuals and small teams',
                'features' => [
                    'No of Users' => '3',
                    'Lead Integration' => '✅',
                    'Reminders' => '✅',
                    'Meetings' => '✅',
                    'One Click WhatsApp' => '✅',
                    'HR Documents' => '✅',
                    'Chat Support' => '✅'
                ],
                'status' => 'active',
                'trial_period_days' => 0,
                'grace_period_days' => 0,
            ],
            [
                'name' => 'Starter',
                'slug' => 'starter-monthly',
                'type' => 'monthly',
                'price' => 4999,
                'currency' => 'INR',
                'interval_count' => 1,
                'description' => 'For growing businesses',
                'features' => [
                    'No of Users' => '5',
                    'Attendance' => '₹2500 / Year',
                    'Custom Fields' => '✅',
                    'Lead Integration' => '₹1000/source/Year',
                    'Reminders' => '✅',
                    'Meetings' => '✅',
                    'Call Records' => '₹599/year/user',
                    'One Click WhatsApp' => '✅',
                    'SMS Integration' => '✅',
                    'WhatsApp API Integration' => '₹999 / Monthly',
                    'Auto Dialer' => '₹1500/user/Monthly',
                    'Field Force Tracking' => '₹1000/user/Year',
                    'WhatsApp API Reporting' => '₹1499 / Month',
                    'Leave Management' => '✅',
                    'Holidays' => '✅',
                    'Salary' => '✅',
                    'HR Documents' => '✅',
                    'Chat Support' => '✅',
                    'Call / Video Call Support' => '₹399 / Hour'
                ],
                'status' => 'active',
                'trial_period_days' => 7,
                'grace_period_days' => 3,
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional-monthly',
                'type' => 'monthly',
                'price' => 8999,
                'currency' => 'INR',
                'interval_count' => 1,
                'description' => 'For large organizations',
                'features' => [
                    'No of Users' => '10',
                    'Attendance' => '✅',
                    'Custom Fields' => '✅',
                    'Lead Integration' => '✅',
                    'Reminders' => '✅',
                    'Meetings' => '✅',
                    'Call Records' => '₹599/year/user',
                    'One Click WhatsApp' => '✅',
                    'SMS Integration' => '✅',
                    'WhatsApp API Integration' => '✅',
                    'Auto Dialer' => '₹1500/user/Monthly',
                    'Field Force Tracking' => '✅',
                    'WhatsApp API Reporting' => '✅',
                    'Leave Management' => '✅',
                    'Holidays' => '✅',
                    'Salary' => '✅',
                    'HR Documents' => '✅',
                    'Chat Support' => '✅',
                    'Call / Video Call Support' => '✅'
                ],
                'status' => 'active',
                'trial_period_days' => 14,
                'grace_period_days' => 7,
            ],

            // Yearly Plans
            [
                'name' => 'Basic',
                'slug' => 'basic-yearly',
                'type' => 'yearly',
                'price' => 24990,
                'currency' => 'INR',
                'interval_count' => 1,
                'description' => 'For individuals and small teams',
                'features' => [
                    'No of Users' => '3',
                    'Lead Integration' => '✅',
                    'Reminders' => '✅',
                    'Meetings' => '✅',
                    'One Click WhatsApp' => '✅',
                    'HR Documents' => '✅',
                    'Chat Support' => '✅'
                ],
                'status' => 'active',
                'trial_period_days' => 0,
                'grace_period_days' => 0,
            ],
            [
                'name' => 'Starter',
                'slug' => 'starter-yearly',
                'type' => 'yearly',
                'price' => 49990,
                'currency' => 'INR',
                'interval_count' => 1,
                'description' => 'For growing businesses',
                'features' => [
                    'No of Users' => '5',
                    'Attendance' => '₹2500 / Year',
                    'Custom Fields' => '✅',
                    'Lead Integration' => '₹1000/source/Year',
                    'Reminders' => '✅',
                    'Meetings' => '✅',
                    'Call Records' => '₹599/year/user',
                    'One Click WhatsApp' => '✅',
                    'SMS Integration' => '✅',
                    'WhatsApp API Integration' => '₹999 / Monthly',
                    'Auto Dialer' => '₹1500/user/Monthly',
                    'Field Force Tracking' => '₹1000/user/Year',
                    'WhatsApp API Reporting' => '₹1499 / Month',
                    'Leave Management' => '✅',
                    'Holidays' => '✅',
                    'Salary' => '✅',
                    'HR Documents' => '✅',
                    'Chat Support' => '✅',
                    'Call / Video Call Support' => '₹399 / Hour'
                ],
                'status' => 'active',
                'trial_period_days' => 7,
                'grace_period_days' => 3,
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional-yearly',
                'type' => 'yearly',
                'price' => 89990,
                'currency' => 'INR',
                'interval_count' => 1,
                'description' => 'For large organizations',
                'features' => [
                    'No of Users' => '10',
                    'Attendance' => '✅',
                    'Custom Fields' => '✅',
                    'Lead Integration' => '✅',
                    'Reminders' => '✅',
                    'Meetings' => '✅',
                    'Call Records' => '₹599/year/user',
                    'One Click WhatsApp' => '✅',
                    'SMS Integration' => '✅',
                    'WhatsApp API Integration' => '✅',
                    'Auto Dialer' => '₹1500/user/Monthly',
                    'Field Force Tracking' => '✅',
                    'WhatsApp API Reporting' => '✅',
                    'Leave Management' => '✅',
                    'Holidays' => '✅',
                    'Salary' => '✅',
                    'HR Documents' => '✅',
                    'Chat Support' => '✅',
                    'Call / Video Call Support' => '✅'
                ],
                'status' => 'active',
                'trial_period_days' => 14,
                'grace_period_days' => 7,
            ],
        ];

        foreach ($plans as $planData) {
            Plan::updateOrCreate(['slug' => $planData['slug']], $planData);
        }
    }
}
