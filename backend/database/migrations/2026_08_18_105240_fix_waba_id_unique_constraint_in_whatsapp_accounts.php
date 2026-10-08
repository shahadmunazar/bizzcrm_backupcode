<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('whats_app_accounts', function (Blueprint $table) {
            $indexes = Schema::getIndexes('whats_app_accounts');
            $hasOldIndex = false;
            foreach ($indexes as $index) {
                if ($index['name'] === 'whatsapp_business_accounts_waba_id_unique') {
                    $hasOldIndex = true;
                    break;
                }
            }

            if($hasOldIndex) {
                $table->dropUnique('whatsapp_business_accounts_waba_id_unique');
            }
            
            // Add a composite unique index so multiple boxes can share a WABA, but not duplicate it within the same box
            $table->unique(['box_id', 'waba_id'], 'whats_app_accounts_box_waba_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whats_app_accounts', function (Blueprint $table) {
            $indexes = Schema::getIndexes('whats_app_accounts');
            $hasNewIndex = false;
            foreach ($indexes as $index) {
                if ($index['name'] === 'whats_app_accounts_box_waba_unique') {
                    $hasNewIndex = true;
                    break;
                }
            }

            if($hasNewIndex) {
                $table->dropUnique('whats_app_accounts_box_waba_unique');
            }
        });
    }
};
