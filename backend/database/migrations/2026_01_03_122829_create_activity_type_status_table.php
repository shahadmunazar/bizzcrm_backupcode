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
        Schema::create('activity_type_status', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('activity_type_id');
            $table->unsignedBigInteger('activity_status_id');
            $table->timestamps();

            // Foreign keys
            $table->foreign('activity_type_id')->references('id')->on('activity_types')->onDelete('cascade');
            $table->foreign('activity_status_id')->references('id')->on('activity_statuses')->onDelete('cascade');

            // Unique constraint to prevent duplicates
            $table->unique(['activity_type_id', 'activity_status_id'], 'activity_type_status_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_type_status');
    }
};
