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
            // Core User & CRM Management
            'No of Users',
            'User Management',
            'Manage Targets & Performance',
            'General Settings',

            // Leads & Integrations
            'All Leads Management',
            'Lead Integration',
            'Lead System Settings',
            'Custom Fields',
            'Reminders',
            'Meetings',
            'One Click WhatsApp',

            // Field Tracking & Operations
            'Field Force Tracking',
            'Location Tracking',
            'Tour Plans',

            // Telephony & Communications
            'Call Records',
            'SMS Integration',
            'WhatsApp API Integration',
            'Auto Dialer',
            'WhatsApp API Reporting',

            // HRM & Workforce
            'Attendance',
            'Leave Management',
            'Holidays',
            'Salary',
            'HR Documents',

            // Accounts & Finance
            'Products & Catalog',
            'Finance & Expenses',

            // Support
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
