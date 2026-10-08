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
        Schema::create('leave_transaction', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_id');
            $table->unsignedBigInteger('box_user_id');
            $table->unsignedBigInteger('leave_type_id');
            $table->date('date')->nullable();
            $table->decimal('credit_leave', 5, 2)->default(0)->nullable();
            $table->decimal('debit_leave', 5, 2)->default(0)->nullable();
            $table->decimal('balance_leave', 5, 2)->default(0)->nullable();
            $table->decimal('total_leave', 5, 2)->default(0)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('box_id')->references('id')->on('boxes')->onDelete('cascade');
            $table->foreign('box_user_id')->references('id')->on('box_users')->onDelete('cascade');
            $table->foreign('leave_type_id')->references('id')->on('leave_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_transaction');
    }
};
