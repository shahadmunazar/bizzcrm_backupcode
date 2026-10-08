<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salary_id')->constrained('salaries')->onDelete('cascade');
            $table->foreignId('box_user_id');
            $table->foreignId('box_id');
            $table->foreignId('changed_by')->nullable();
            $table->enum('change_type', ['create','update','increment','revert'])->default('update');
            $table->text('remarks')->nullable();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->foreignId('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_history');
    }
};
