<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salary_id')->constrained('salaries')->onDelete('cascade');
            $table->foreignId('box_user_id');
            $table->foreignId('box_id');

            $table->decimal('basic', 12, 2)->default(0);
            $table->decimal('da', 12, 2)->default(0);
            $table->decimal('hra', 12, 2)->default(0);
            $table->decimal('conveyance', 12, 2)->default(0);
            $table->decimal('allowance', 12, 2)->default(0);
            $table->decimal('medical_allowance', 12, 2)->default(0);
            $table->decimal('others', 12, 2)->default(0);

            $table->foreignId('created_by')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->foreignId('deleted_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_earnings');
    }
};
