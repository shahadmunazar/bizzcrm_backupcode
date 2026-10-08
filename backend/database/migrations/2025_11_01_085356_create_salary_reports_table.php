<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salary_id')->constrained('salaries')->onDelete('cascade');
            $table->foreignId('box_user_id')->constrained('box_users')->onDelete('cascade');
            $table->foreignId('box_id')->constrained('boxes')->onDelete('cascade');
            $table->year('year');
            $table->unsignedTinyInteger('month');
            $table->decimal('gross_salary', 12, 2)->default(0);
            $table->decimal('total_earnings', 12, 2)->default(0);
            $table->decimal('total_deductions', 12, 2)->default(0);
            $table->decimal('net_salary', 12, 2)->default(0);
            $table->json('earnings_breakdown')->nullable();
            $table->json('deductions_breakdown')->nullable();
            $table->boolean('is_locked')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_reports');
    }
};
