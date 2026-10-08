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
        Schema::table('whatsapp_messages', function (Blueprint $table) {
            $table->index(['lead_id', 'box_id']);
            $table->index(['box_id', 'type']);
            $table->index('lead_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whatsapp_messages', function (Blueprint $table) {
            $table->dropIndex(['lead_id', 'box_id']);
            $table->dropIndex(['box_id', 'type']);
            $table->dropIndex(['lead_id']);
        });
    }
};
