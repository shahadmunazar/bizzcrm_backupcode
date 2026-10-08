<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class CrmIntegrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public static function getSeedData(): array
    {
        return [
            [
                "id" => 5,
                "name" => "99 Acers",
                "system_name" => "99acers",
                "status" => "Enabled",
                "params" => json_encode(["username" => "Username", "password" => "Password"]),
                "icon" => "fa-solid fa-house-chimney",
                "icon_type" => "icon",
                "created_at" => "2021-05-06 23:00:00",
                "updated_at" => Carbon::now()
            ],
            [
                "id" => 1,
                "name" => "Facebook",
                "system_name" => "facebook",
                "status" => "Enabled",
                "params" => null,
                "icon" => "fa-brands fa-facebook-f",
                "icon_type" => "icon",
                "created_at" => "2023-04-26 10:30:00",
                "updated_at" => Carbon::now()
            ],
            [
                "id" => 2,
                "name" => "India Mart",
                "system_name" => "indiaMart",
                "status" => "Enabled",
                "params" => json_encode(["mobile" => "Mobile Number", "code" => "Activation Code"]),
                "icon" => "assets/img/iconsapi/indiamart-icon.png",
                "icon_type" => "img",
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                "id" => 3,
                "name" => "Pharmahopers",
                "system_name" => "pharmahopers",
                "status" => "Enabled",
                "params" => json_encode(["username" => "Username", "password" => "Password"]),
                "icon" => "assets/img/iconsapi/p-hooper-icon.png",
                "icon_type" => "img",
                "created_at" => "2021-12-28 10:30:00",
                "updated_at" => Carbon::now()
            ],
            [
                "id" => 7,
                "name" => "Pharma Khabar",
                "system_name" => "pharmaKhabar",
                "status" => "Enabled",
                "params" => json_encode(["api_key" => "API Key"]),
                "icon" => "assets/img/iconsapi/pk-icon.png",
                "icon_type" => "img",
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                "id" => 4,
                "name" => "Pharmavends",
                "system_name" => "pharmavends",
                "status" => "Enabled",
                "params" => json_encode(["apitoken" => "API Token"]),
                "icon" => "assets/img/iconsapi/p-vends-icon.png",
                "icon_type" => "img",
                "created_at" => "2023-04-26 10:30:00",
                "updated_at" => Carbon::now()
            ],
            [
                "id" => 9,
                "name" => "Trade India",
                "system_name" => "tradeIndia",
                "status" => "Enabled",
                "params" => json_encode(["userid" => "User ID", "profile_id" => "Profile ID", "key" => "API Key"]),
                "icon" => "assets/img/iconsapi/trade-india-icon.png",
                "icon_type" => "img",
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                "id" => 8,
                "name" => "Trade XL",
                "system_name" => "tradeXl",
                "status" => "Enabled",
                "params" => json_encode(["uid" => "User ID", "key" => "API Key"]),
                "icon" => "assets/img/iconsapi/t-xl-icon.png",
                "icon_type" => "img",
                "created_at" => "2020-02-14 11:30:00",
                "updated_at" => Carbon::now()
            ],
            [
                "id" => 6,
                "name" => "Web Hook",
                "system_name" => "webhook",
                "status" => "Enabled",
                "params" => json_encode(["source_name" => "Source Name"]),
                "icon" => "fa-solid fa-circle-nodes",
                "icon_type" => "icon",
                "created_at" => "2021-05-06 23:00:00",
                "updated_at" => Carbon::now()
            ],
            [
                "id" => 10,
                "name" => "Gmail",
                "system_name" => "gmail",
                "status" => "Enabled",
                "params" => null,
                "icon" => "fa-brands fa-google",
                "icon_type" => "icon",
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                "id" => 11,
                "name" => "Just Dial",
                "system_name" => "justDial",
                "status" => "Enabled",
                "params" => json_encode(["api_key" => "API Key"]),
                "icon" => "assets/img/iconsapi/jd-icon.png",
                "icon_type" => "img",
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $integrations = self::getSeedData();

        foreach ($integrations as $integration) {
            $exists = DB::table('crm_integrations')->where('system_name', $integration['system_name'])->exists();
            if (!$exists) {
                try {
                    DB::table('crm_integrations')->insert($integration);
                }
                catch (\Exception $e) {
                    unset($integration['id']);
                    DB::table('crm_integrations')->insert($integration);
                }
            }
        }
    }
}
