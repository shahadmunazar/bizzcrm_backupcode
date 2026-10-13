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
        Schema::create('target_actuals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('target_assignment_id');
            $table->unsignedBigInteger('metric_id');
            $table->date('period')->nullable();
            $table->decimal('actual_value', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('target_assignment_id')->references('id')->on('target_assignments')->onDelete('cascade');
            $table->foreign('metric_id')->references('id')->on('target_metrics')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('target_actuals');
    }
};
