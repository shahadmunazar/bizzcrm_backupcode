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
        Schema::create('employee_bank_statutories_', function (Blueprint $table) {
            $table->id();

            $table->foreignId('box_user_id')->constrained('box_users')->restrictOnDelete();
            $table->foreignId('box_id')->constrained('boxes')->restrictOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->enum('salary_basis', ['Weekly', 'Monthly', 'Annualy'])->nullable();
            $table->string('salary_amount')->nullable();
            $table->enum('payment_type', ['Cash', 'Debit Card', 'Mobile Payment'])->nullable();

            $table->enum('pf_contribution', ['Employee Contribution', 'Employer Contribution', 'Provident Fund Interest'])->nullable();
            $table->string('pf_no')->nullable();
            $table->string('pf_rate')->nullable();
            $table->enum('pf_additional', ['ESI', 'EPS', 'EPF'])->nullable();
            $table->string('pf_total')->nullable();

            $table->enum('esi_contribution', ['Employee Contribution', 'Employer Contribution', 'Maternity Benefit'])->nullable();
            $table->string('esi_no')->nullable();
            $table->string('esi_rate', 5, 2)->nullable();
            $table->enum('esi_additional', ['ESI', 'EPS', 'EPF'])->nullable();
            $table->string('esi_total')->nullable(); // increase scale for totals

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_bank_statutories_');
    }
};
