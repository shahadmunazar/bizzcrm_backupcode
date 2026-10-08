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
        Schema::create('expense_approval_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('box_id')->constrained('boxes')->cascadeOnDelete();
            $table->decimal('min_amount', 10, 2)->default(0);
            $table->decimal('max_amount', 10, 2)->nullable();
            $table->json('required_roles');
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_approval_rules');
    }
};
