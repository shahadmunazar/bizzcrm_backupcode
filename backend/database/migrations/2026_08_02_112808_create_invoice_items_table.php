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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_id')->index();
            $table->unsignedBigInteger('invoice_id')->index();
            $table->unsignedBigInteger('lead_id')->index();
            $table->unsignedBigInteger('package_id')->index();
            $table->decimal('amount', 15, 2);
            $table->decimal('msp', 15, 2);
            $table->integer('qty');
            $table->decimal('sgst', 15, 2);
            $table->decimal('cgst', 15, 2);
            $table->decimal('igst', 15, 2);
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
