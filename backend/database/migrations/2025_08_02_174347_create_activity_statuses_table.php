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
        Schema::create('activity_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('activity_setting_id')->nullable();
            $table->unsignedBigInteger('activity_type_id')->nullable();
            $table->unsignedBigInteger('box_id')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->string('activity_status')->nullable();
            $table->boolean('status')->default(true);
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('activity_setting_id')->references('id')->on('calls')->onDelete('restrict');
            $table->foreign('activity_type_id')->references('id')->on('activity_types')->onDelete('restrict');
            $table->foreign('box_id')->references('id')->on('boxes')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_statuses');
    }
};
