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
        Schema::create('employee_allowances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('box_id')->constrained('boxes')->cascadeOnDelete();
            $table->foreignId('boxuser_id')->constrained('box_users')->cascadeOnDelete();
            $table->foreignId('allowance_type_id')->constrained('allowance_types')->cascadeOnDelete();
            $table->date('business_date');
            $table->decimal('amount', 10, 2);
            $table->text('calculation_details')->nullable();
            $table->string('status')->default('Pending');
            $table->timestamps();
            $table->unique(['boxuser_id', 'allowance_type_id', 'business_date'], 'emp_allow_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_allowances');
    }
};
