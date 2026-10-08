<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PackageFeature;
use Illuminate\Support\Str;

class PackageFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $features = [
            'No of Users',
            'Attendance',
            'Custom Fields',
            'Lead Integration',
            'Reminders',
            'Meetings',
            'Call Records',
            'One Click WhatsApp',
            'SMS Integration',
            'WhatsApp API Integration',
            'Auto Dialer',
            'Field Force Tracking',
            'WhatsApp API Reporting',
            'Leave Management',
            'Holidays',
            'Salary',
            'HR Documents',
            'Chat Support',
            'Call / Video Call Support',
        ];

        foreach ($features as $featureName) {
            PackageFeature::firstOrCreate(
                ['name' => $featureName],
                ['slug' => Str::slug($featureName), 'is_active' => true]
            );
        }
    }
}
