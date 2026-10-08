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
        Schema::create('whats_app_projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_id');
            $table->string('name');
            $table->unsignedBigInteger('whatsapp_business_account_id')->nullable();
            $table->unsignedBigInteger('whatsapp_phone_number_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whats_app_projects');
    }
};
