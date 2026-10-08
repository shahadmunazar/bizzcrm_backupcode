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
        Schema::create('attendance_corrections', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('box_id')->nullable();
    $table->unsignedBigInteger('attendance_id')->nullable();
    $table->unsignedBigInteger('box_user_id')->nullable();
    $table->boolean('punch_in')->default(false);
    $table->time('punch_in_time')->nullable();
    $table->boolean('punch_out')->default(false);
    $table->time('punch_out_time')->nullable();
    $table->string('reason')->nullable();
    $table->enum('status', ['pending', 'approved', 'rejected', 'hold'])->default('pending');
    $table->unsignedBigInteger('created_by')->nullable();
    $table->unsignedBigInteger('updated_by')->nullable();
    $table->unsignedBigInteger('deleted_by')->nullable();
    $table->timestamps();
    $table->softDeletes();
    $table->foreign('attendance_id')->references('id')->on('attendances')->onDelete('cascade');
    $table->foreign('box_user_id')->references('id')->on('box_users')->onDelete('cascade');
    $table->foreign('box_id')->references('id')->on('boxes')->onDelete('cascade');
    $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
    $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
    $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_corrections');
    }
};
