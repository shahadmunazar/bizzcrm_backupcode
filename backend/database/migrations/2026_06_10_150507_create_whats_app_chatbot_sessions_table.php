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
        Schema::create('whatsapp_chatbot_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_id');
            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('whatsapp_chatbot_id');
            $table->string('current_node_id')->nullable();
            $table->json('data')->nullable(); // To store user answers
            $table->timestamps();

            // Foreign keys
            $table->foreign('box_id')->references('id')->on('boxes')->onDelete('cascade');
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
            $table->foreign('whatsapp_chatbot_id')->references('id')->on('whatsapp_chatbots')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_chatbot_sessions');
    }
};
