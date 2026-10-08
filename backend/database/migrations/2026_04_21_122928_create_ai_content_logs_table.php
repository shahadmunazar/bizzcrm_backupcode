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
        Schema::create('ai_content_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_id');
            $table->unsignedBigInteger('user_id');
            $table->text('prompt');
            $table->text('result');
            $table->string('style')->nullable();
            $table->string('optimize')->nullable();
            $table->string('type')->default('whatsapp_template');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_content_logs');
    }
};
