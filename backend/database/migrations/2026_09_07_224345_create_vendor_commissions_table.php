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
        Schema::create('vendor_commissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
            $table->unsignedBigInteger('box_id');
            $table->decimal('transaction_amount', 12, 2)->default(0);
            $table->decimal('commission_percentage', 5, 2)->default(0);
            $table->decimal('commission_amount', 12, 2)->default(0);
            $table->string('status')->default('pending'); // pending, paid, cancelled
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
            $table->foreign('box_id')->references('id')->on('boxes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_commissions');
    }
};
