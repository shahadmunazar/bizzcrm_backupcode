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
            $table->unsignedBigInteger('whatsapp_chatbot_id')->nullable()->after('status');
            $table->foreign('whatsapp_chatbot_id')->references('id')->on('whatsapp_chatbots')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whatsapp_campaigns', function (Blueprint $table) {
            $table->dropForeign(['whatsapp_chatbot_id']);
            $table->dropColumn('whatsapp_chatbot_id');
        });
    }
};
