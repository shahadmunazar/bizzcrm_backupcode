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
        Schema::create('customer_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id')->unique(); // 1-to-1 relationship with leads table
            $table->string('company_image')->nullable();
            $table->string('customer_level')->nullable();
            $table->string('parent_name')->nullable();
            $table->string('whatsapp_number', 20)->nullable();
            $table->string('customer_type')->nullable();
            $table->string('territory')->nullable();
            $table->text('payment_terms')->nullable();
            $table->decimal('credit_limit', 10, 2)->default(0);
            $table->decimal('outstanding_amount', 10, 2)->default(0);
            $table->unsignedBigInteger('box_id')->nullable();
            $table->unsignedBigInteger('boxuser_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign key constraint
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_details');
    }
};
