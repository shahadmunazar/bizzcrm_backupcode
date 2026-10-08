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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_id');
            $table->string('asset_id')->nullable()->comment('Generated Asset ID like AST-001');
            $table->unsignedBigInteger('asset_type_id')->nullable();
            $table->string('asset_name')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('vendor')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('cost', 15, 2)->nullable();
            $table->date('warranty_end_date')->nullable();
            $table->string('status')->default('available')->comment('available, assigned, damaged, lost');
            $table->text('description')->nullable();
            
            // Assignment details
            $table->unsignedBigInteger('assigned_to')->nullable()->comment('User ID');
            $table->dateTime('assigned_date')->nullable();
            $table->dateTime('return_date')->nullable();

            $table->unsignedBigInteger('added_by')->nullable();
            $table->unsignedBigInteger('last_updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
