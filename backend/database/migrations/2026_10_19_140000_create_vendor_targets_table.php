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
        if (!Schema::hasTable('vendor_targets')) {
            Schema::create('vendor_targets', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('vendor_id')->index();
                $table->string('period_type', 30)->default('monthly')->index(); // monthly, quarterly, half_yearly, yearly, custom
                $table->decimal('target_amount', 14, 2)->default(0);
                $table->date('start_date')->index();
                $table->date('end_date')->nullable()->index();
                $table->unsignedInteger('effective_months')->nullable(); // e.g. 3 for 3-month duration
                $table->string('notes')->nullable();
                $table->timestamps();

                $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_targets');
    }
};
