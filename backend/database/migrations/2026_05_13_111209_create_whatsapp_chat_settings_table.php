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
        Schema::create('whatsapp_chat_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_id');
            $table->unsignedBigInteger('lead_id');
            $table->string('disappearing_duration')->nullable(); // off, 24_hours, 7_days, 90_days
            $table->timestamp('muted_until')->nullable();
            $table->unsignedBigInteger('added_by')->nullable();
            $table->unsignedBigInteger('last_updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['box_id', 'lead_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_chat_settings');
    }
};
