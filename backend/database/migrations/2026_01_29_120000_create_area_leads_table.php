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
        Schema::create('area_leads', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('division_id')->nullable();
            $table->foreign('division_id')->references('id')->on('divisions')->onDelete('set null');

            // Assuming 'division_areas' table exists or will be created
            $table->unsignedBigInteger('division_area_id')->nullable();
            $table->foreign('division_area_id')->references('id')->on('division_areas')->onDelete('set null');

            $table->unsignedBigInteger('area_lead_id')->nullable();
            $table->foreign('area_lead_id')->references('id')->on('leads')->onDelete('set null');

            $table->unsignedBigInteger('box_id')->nullable();
            $table->foreign('box_id')->references('id')->on('boxes')->onDelete('cascade');

            $table->unsignedBigInteger('box_user_id')->nullable();
            $table->foreign('box_user_id')->references('id')->on('box_users')->onDelete('cascade');

            $table->enum('status', ['Active', 'Deleted'])->default('Active');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('area_leads');
    }
};
