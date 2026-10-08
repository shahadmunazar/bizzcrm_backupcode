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
        Schema::table('boxes', function (Blueprint $table) {
            $table->string('whatsapp_api_token')->nullable()->unique()->after('whatsapp_token');
            $table->string('whatsapp_webhook_url')->nullable()->after('whatsapp_api_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('boxes', function (Blueprint $table) {
            //
        });
    }
};
