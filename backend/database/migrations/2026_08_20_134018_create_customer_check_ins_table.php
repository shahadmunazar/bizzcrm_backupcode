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
        Schema::create('customer_check_ins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->comment('maps to leads table');
            $table->unsignedBigInteger('box_id')->nullable();
            $table->unsignedBigInteger('boxuser_id')->nullable();
            
            $table->text('remarks')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->json('images')->nullable();
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('customer_id');
            $table->index('box_id');
            $table->index('boxuser_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_check_ins');
    }
};
