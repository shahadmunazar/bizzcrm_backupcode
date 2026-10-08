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
        Schema::table('whats_app_templates', function (Blueprint $table) {
            $table->string('whatsapp_number_id')->nullable()->after('waba_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whats_app_templates', function (Blueprint $table) {
            $table->dropColumn('whatsapp_number_id');
        });
    }
};
