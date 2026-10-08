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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('box_id')->constrained('boxes')->cascadeOnDelete();
            $table->foreignId('boxuser_id')->constrained('box_users')->cascadeOnDelete();
            $table->foreignId('customer_check_in_id')->nullable()->constrained('customer_check_ins')->nullOnDelete();
            $table->foreignId('expense_category_id')->constrained('expense_categories')->restrictOnDelete();
            $table->date('expense_date');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method')->nullable();
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->string('status')->default('Draft');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
