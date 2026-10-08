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
        Schema::create('attendance_break', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_user_id');
            $table->unsignedBigInteger('attendance_id');
            $table->unsignedBigInteger('box_id');
            $table->timestamp('break_start')->nullable();
            $table->timestamp('break_end')->nullable();
            $table->time('duration')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('break_status')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('box_user_id')->references('id')->on('box_users')->onDelete('cascade');
            $table->foreign('attendance_id')->references('id')->on('attendances')->onDelete('cascade');
            $table->foreign('box_id')->references('id')->on('boxes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_break');
    }
};
