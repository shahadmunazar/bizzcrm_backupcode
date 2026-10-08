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
        Schema::create('facebook_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('box_id')->constrained('boxes')->restrictOnDelete();
            $table->string('page_id')->unique(); // Facebook Page ID
            $table->string('name')->nullable(); // Page name
            $table->text('access_token')->nullable(); // Page access token
            $table->enum('webhook_enabled', ['Yes', 'No'])->default('No');
            $table->string('verification_code')->nullable(); // For webhook verification
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facebook_pages');
    }
};
