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
        Schema::create('assigned_assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_id');
            $table->unsignedBigInteger('box_user_id');
            $table->unsignedBigInteger('box_id');
            $table->dateTime('assigned_date')->nullable();
            $table->dateTime('returned_date')->nullable();
            $table->unsignedBigInteger('added_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            // Relationships
            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
            $table->foreign('box_user_id')->references('id')->on('box_users')->onDelete('restrict');
            $table->foreign('box_id')->references('id')->on('boxes')->onDelete('restrict');
            $table->foreign('added_by')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assigned_assets');
    }
};
