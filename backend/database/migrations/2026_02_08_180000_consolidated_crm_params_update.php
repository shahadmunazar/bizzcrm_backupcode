<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // ---------------------------------------------------------------------
        // SINGLE MIGRATION OF RECORD (Production Ready)
        // ---------------------------------------------------------------------

        // India Mart
        DB::table('crm_integrations')->where('name', 'India Mart')->update(['params' => json_encode(['mobile' => 'Mobile Number', 'key' => 'Activation Code'])]);
        
        // Pharma Khabar (Key based)
        DB::table('crm_integrations')->where('name', 'Pharma Khabar')->update(['params' => json_encode(['key' => 'API Key'])]);
        
        // Trade India (User ID, Profile ID, API Key)
        DB::table('crm_integrations')->where('name', 'Trade India')->update(['params' => json_encode(['user_id' => 'User ID', 'profile_id' => 'Profile ID', 'key' => 'API Key'])]);
        
        // Trade XL (User ID, API Key)
        DB::table('crm_integrations')->where('name', 'Trade XL')->update(['params' => json_encode(['user_id' => 'User ID', 'key' => 'API Key'])]);
        
        // 99 Acers (Username, Password)
        DB::table('crm_integrations')->where('name', '99 Acers')->update(['params' => json_encode(['username' => 'Username', 'password' => 'Password'])]);
        
        // Pharma Hopers (Username, Password)
        DB::table('crm_integrations')->where('name', 'Pharma Hopers')->update(['params' => json_encode(['username' => 'Username', 'password' => 'Password'])]);
        
        // Pharma Vends (Username, Password)
        DB::table('crm_integrations')->where('name', 'Pharma Vends')->update(['params' => json_encode(['username' => 'Username', 'password' => 'Password'])]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Optional: Revert to defaults if needed
        // DB::table('crm_integrations')->update(['params' => null]);
    }
};
