<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salary_id')->constrained('salaries')->onDelete('cascade');
            $table->foreignId('box_user_id');
            $table->foreignId('box_id');

            $table->decimal('tds', 12, 2)->default(0);
            $table->decimal('esi', 12, 2)->default(0);
            $table->decimal('pf', 12, 2)->default(0);
            $table->decimal('leave', 12, 2)->default(0);
            $table->decimal('prof_tax', 12, 2)->default(0);
            $table->decimal('labour_welfare', 12, 2)->default(0);
            $table->decimal('othersdeductions', 12, 2)->default(0);

            $table->foreignId('created_by')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->foreignId('deleted_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_deductions');
    }
};
