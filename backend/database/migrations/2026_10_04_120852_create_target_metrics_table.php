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
        Schema::create('target_metrics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('target_template_id');
            $table->string('metric_type');
            $table->string('metric_name');
            $table->decimal('target_value', 15, 2)->nullable(); // Might be calculated
            $table->string('unit')->nullable();
            $table->decimal('weight', 5, 2);
            $table->string('calculation_direction')->default('higher_is_better');
            $table->timestamps();

            $table->foreign('target_template_id')->references('id')->on('target_templates')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('target_metrics');
    }
};
