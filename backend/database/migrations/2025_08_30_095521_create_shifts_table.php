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
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('box_id')->constrained('boxes')->onDelete('cascade');
            // Shift Details
            $table->string('code')->nullable();
            $table->string('shift_name',)->nullable();
            $table->time('in_time')->nullable();
            $table->time('out_time')->nullable();
            $table->time('shift_hours')->nullable();
            $table->boolean('shift_enabled')->default(false)->nullable();

            // Rest Time
            $table->boolean('rest_enabled')->default(false)->nullable();
            $table->time('lunch_out')->nullable();
            $table->time('lunch_in')->nullable();
            $table->time('lunch_hours')->nullable();

            // Short Break
            $table->boolean('short_break_enabled')->default(false)->nullable();
            $table->time('short_break_out')->nullable();
            $table->time('short_break_in')->nullable();
            $table->time('short_break_hours')->nullable();
            // Half Day
            $table->boolean('half_day_enabled')->default(false)->nullable();
            $table->time('half_day_start_time')->nullable();
            $table->time('half_day_end_time')->nullable();
            $table->time('half_day_hours')->nullable();
            // Deduction Settings
            $table->time('absent_below')->nullable();
            $table->time('half_day_below')->nullable();
            $table->time('short_leave_below')->nullable();
            $table->time('overtime_allowed')->nullable();
            // Weekly Offs
            $table->enum('weekly_off_1', ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'])->nullable();
            $table->enum('weekly_off_2', ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'])->nullable();
            // Saturday offs
            $table->json('saturday_offs')->nullable();
            $table->json('saturday_half_days')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
