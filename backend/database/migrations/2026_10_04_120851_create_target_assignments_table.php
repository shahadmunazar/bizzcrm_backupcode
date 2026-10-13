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
        Schema::create('target_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('target_template_id');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->decimal('calculated_target', 15, 2)->nullable();
            $table->decimal('salary_snapshot', 15, 2)->nullable();
            $table->decimal('salary_multiplier', 8, 2)->nullable();
            $table->string('status')->default('Active');
            $table->timestamps();

            $table->foreign('target_template_id')->references('id')->on('target_templates')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('target_assignments');
    }
};
