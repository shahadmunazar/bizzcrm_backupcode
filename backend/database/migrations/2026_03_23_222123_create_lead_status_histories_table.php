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
        Schema::create('lead_status_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_id')->nullable();
            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('old_status_id')->nullable();
            $table->unsignedBigInteger('new_status_id');
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->timestamps();

            // Set up foreign keys (assuming leads, contact_stage, and users are the related tables)
            $table->foreign('box_id')->references('id')->on('boxes')->onDelete('cascade');
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
            $table->foreign('old_status_id')->references('id')->on('contact_stage')->onDelete('set null');
            $table->foreign('new_status_id')->references('id')->on('contact_stage')->onDelete('cascade');
            $table->foreign('changed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_status_histories');
    }
};
