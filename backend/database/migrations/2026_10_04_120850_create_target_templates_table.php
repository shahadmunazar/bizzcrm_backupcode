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
        Schema::create('target_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_id');
            $table->string('name');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('target_type');
            $table->string('calculation_method');
            $table->string('frequency');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('salary_multiplier', 8, 2)->nullable();
            $table->string('status')->default('Active');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('box_id')->references('id')->on('boxes')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('target_templates');
    }
};
