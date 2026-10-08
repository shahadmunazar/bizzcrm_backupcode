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
        Schema::create('whatsapp_chatbots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_id');
            $table->string('name');
            $table->string('trigger_type')->default('keyword'); // keyword, exact_match, all_messages
            $table->string('trigger_value')->nullable();
            $table->longText('flow_data')->nullable(); // JSON of nodes and connections
            $table->longText('settings')->nullable(); // JSON of action variables
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('box_id')->references('id')->on('boxes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_chatbots');
    }
};
