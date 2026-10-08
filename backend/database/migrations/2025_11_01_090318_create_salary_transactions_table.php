<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salary_report_id')->constrained('salary_reports')->onDelete('cascade');
            $table->foreignId('user_bank_account_id')
                ->nullable()
                ->constrained('employee_bank_details')
                ->nullOnDelete();
            $table->date('payment_date');
            $table->decimal('amount', 12, 2);
            $table->string('payment_method')->nullable();
            $table->string('transaction_ref')->nullable();
            $table->string('status')->default('pending');
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_transactions');
    }
};
