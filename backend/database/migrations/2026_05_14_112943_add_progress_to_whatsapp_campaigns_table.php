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
        Schema::table('whatsapp_campaigns', function (Blueprint $table) {
            $table->integer('total_rows')->default(0);
            $table->integer('processed_rows')->default(0);
            $table->string('file_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whatsapp_campaigns', function (Blueprint $table) {
            $table->dropColumn(['total_rows', 'processed_rows', 'file_path']);
        });
    }
};
