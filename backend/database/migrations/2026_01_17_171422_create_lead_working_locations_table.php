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
        Schema::create('lead_working_locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_id');
            $table->unsignedBigInteger('box_user_id');
            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('state_id');
            $table->unsignedBigInteger('city_id');
            $table->unsignedBigInteger('division_id')->nullable();
            $table->json('location_data')->nullable(); // Stores the array of points/radii
            $table->enum('status', ['Enable', 'Disable'])->default('Enable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_working_locations');
    }
};
