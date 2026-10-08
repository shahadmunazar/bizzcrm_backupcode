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
        Schema::table('whatsapp_chatbots', function (Blueprint $table) {
            $table->unsignedBigInteger('whatsapp_project_id')->nullable()->after('box_id');
            
            $table->foreign('whatsapp_project_id')
                  ->references('id')
                  ->on('whats_app_projects')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whatsapp_chatbots', function (Blueprint $table) {
            $table->dropForeign(['whatsapp_project_id']);
            $table->dropColumn('whatsapp_project_id');
        });
    }
};
