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
        Schema::create('biometric_machines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_id')->index();
            $table->foreign('box_id')->references('id')->on('boxes')->onDelete('cascade');
            $table->string('machine_no')->nullable();
            $table->string('machine_type')->nullable();
            $table->string('serial_no')->nullable();
            $table->string('server_port_no')->nullable();
            $table->string('machine_name')->nullable();
            $table->unsignedBigInteger('country_id')->index()->nullable();
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
            $table->unsignedBigInteger('state_id')->index()->nullable();
            $table->foreign('state_id')->references('id')->on('states')->onDelete('set null');
            $table->unsignedBigInteger('city_id')->index()->nullable();
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null');
            $table->string('branch_name')->nullable();
            $table->string('timezone')->nullable();
            $table->string('access_control')->nullable();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
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
        Schema::dropIfExists('biometric_machines');
    }
};
