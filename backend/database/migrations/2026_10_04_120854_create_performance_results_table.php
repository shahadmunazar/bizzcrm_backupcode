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
        Schema::create('performance_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('target_assignment_id');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->decimal('target_value', 15, 2)->default(0);
            $table->decimal('actual_value', 15, 2)->default(0);
            $table->decimal('achievement_percentage', 8, 2)->default(0);
            $table->decimal('weighted_score', 8, 2)->default(0);
            $table->string('performance_status')->nullable();
            $table->timestamp('calculated_at')->useCurrent();
            $table->timestamps();

            $table->foreign('target_assignment_id')->references('id')->on('target_assignments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_results');
    }
};
