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
        Schema::create('device_registers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('boxes_id')->nullable();
            $table->string('device_id')->nullable();
            $table->string('device_name')->nullable();
            $table->string('platform')->nullable();
            $table->string('os_version')->nullable();
            $table->string('app_version')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->text('public_key')->nullable();
            $table->string('key_algorithm')->nullable();
            $table->string('challenge_id')->nullable();
            $table->text('signed_payload')->nullable();
            $table->text('signature')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_registers');
    }
};
