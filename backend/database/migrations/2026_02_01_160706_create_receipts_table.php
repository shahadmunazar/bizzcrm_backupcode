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
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_id')->index();
            $table->unsignedBigInteger('box_user_id')->nullable()->index();
            $table->unsignedBigInteger('lead_id')->nullable()->index();
            $table->unsignedBigInteger('company_id')->index();
            $table->date('receipt_date');
            $table->string('invoice_number')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('payment_mode');
            $table->unsignedBigInteger('bank_id')->nullable()->index();
            $table->unsignedBigInteger('division_id')->nullable()->index();
            $table->string('reference')->nullable();
            $table->boolean('status')->default(1)->comment('1: Active, 0: Inactive');
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
