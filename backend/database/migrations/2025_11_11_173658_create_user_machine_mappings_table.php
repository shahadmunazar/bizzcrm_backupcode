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
        Schema::create('user_machine_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_user_id');
            $table->unsignedBigInteger('box_id');
            $table->unsignedBigInteger('biometric_machine_id');
            $table->string('machine_user_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('box_id')->references('id')->on('boxes')->onDelete('cascade');
            $table->foreign('box_user_id')->references('id')->on('box_users')->onDelete('cascade');
            $table->foreign('biometric_machine_id')->references('id')->on('biometric_machines')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_machine_mappings');
    }
};
