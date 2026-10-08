<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PackageFeature;
use Illuminate\Support\Str;

class WhatsAppFeatureSeeder extends Seeder
{
    public function run()
    {
        $features = [
            '14-Day Money-Back Guarantee',
            'Free WhatsApp Business API',
            'Free WhatsApp Blue Tick Application',
            'AI Template message generator',
            'Unlimited Broadcasting & Retargeting',
            'Unlimited Free Service Conversations',
            'Click to WhatsApp Ads Manager (3-5x leads)',
            'Upload & Manage Contacts',
            'Create tags & attributes',
            'Upto 10 Tags',
            'Upto 5 Custom Attributes',
            'Create template messages',
            'Live Chat Dashboard',
            '1 Owner + 5 FREE Agents included',
            'Additional agents at ₹750/month per agent',
            'Smart Audience Segregation',
            'Template Message APIs',
            'Multi-Agent Live Chat',
            'Agent Transfer & Manager Monitoring',
            'ZipZapGo Marketplace Integrations',
            'Shopify & WooCommerce Integrations',
            'Shared Team Inbox',
            'WhatsApp Payments',
            'ZipZapGo Pro Monthly Plan',
            'FREE 500 Ad Credits (one-time)',
            'Everything in Basic Plan+',
            'Upto 100 Tags',
            'Upto 20 Custom Attributes',
            'Campaign Scheduler',
            'Campaign Click Tracking',
            'Smart Agent Routing',
            'Campaign Budget Analytics',
            'Project APIs',
            'Custom Agent Rules',
            'Carousel Template Click Tracking',
            'CSV Campaign Scheduler',
            'User Access Control',
            'Automatic Failed Message Retry'
        ];

        foreach ($features as $f) {
            PackageFeature::firstOrCreate([
                'name' => $f,
                'feature_type' => 'whatsapp'
            ], [
                'slug' => Str::slug($f),
                'is_active' => true,
            ]);
        }
    }
}
