<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterIndustry;

class MasterIndustrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $industries = [
            // Technology & Digital
            'Technology',
            'Software Development',
            'Information Technology (IT)',
            'SaaS (Software as a Service)',
            'Artificial Intelligence (AI)',
            'Cybersecurity',
            'Cloud Computing',
            'Data Analytics',

            // Healthcare & Life Sciences
            'Healthcare',
            'Pharmaceuticals',
            'Biotechnology',
            'Medical Devices',
            'Diagnostics / Laboratories',
            'Telemedicine',

            // Retail & E-commerce
            'Retail',
            'E-commerce',
            'Wholesale',
            'Consumer Goods',
            'Fashion & Apparel',

            // Finance & Banking
            'Banking',
            'Financial Services',
            'Insurance',
            'FinTech',
            'Investment / Asset Management',

            // Manufacturing & Industrial
            'Manufacturing',
            'Industrial Goods',
            'Automotive',
            'Electronics Manufacturing',
            'Machinery',

            // Construction & Real Estate
            'Construction',
            'Real Estate',
            'Property Management',
            'Infrastructure',

            // Transportation & Logistics
            'Logistics',
            'Transportation',
            'Shipping',
            'Supply Chain',

            // Education & Training
            'Education',
            'E-learning',
            'Training & Development',
            'EdTech',

            // Media & Entertainment
            'Media',
            'Entertainment',
            'Film & Television',
            'Gaming',
            'Content Creation',

            // Telecommunications
            'Telecommunications',
            'Internet Service Providers',
            'Mobile Networks',

            // Energy & Utilities
            'Energy',
            'Oil & Gas',
            'Renewable Energy',
            'Utilities',

            // Agriculture & Food
            'Agriculture',
            'Farming',
            'Food & Beverage',
            'AgriTech',

            // Hospitality & Tourism
            'Hospitality',
            'Travel & Tourism',
            'Hotels & Resorts',
            'Food Services',

            // Business & Professional Services
            'Consulting',
            'Marketing & Advertising',
            'Legal Services',
            'Accounting',
            'Human Resources',

            // Government & Public Sector
            'Government',
            'Public Administration',
            'Defense',
            'Non-Profit / NGO',

            // Other Emerging Industries
            'Blockchain / Crypto',
            'Web3',
            'Metaverse',
            'IoT (Internet of Things)',
            'Robotics',
            'Other'
        ];

        foreach ($industries as $name) {
            MasterIndustry::firstOrCreate(
                ['name' => $name],
                ['status' => true]
            );
        }
    }
}
