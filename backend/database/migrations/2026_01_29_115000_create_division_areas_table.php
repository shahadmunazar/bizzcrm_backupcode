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
        Schema::create('division_areas', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('division_id');
            $table->foreign('division_id')->references('id')->on('divisions')->onDelete('cascade');
            
            $table->string('name')->nullable();
            $table->enum('status', ['Active', 'Deleted'])->default('Active');
            
            $table->unsignedBigInteger('box_id')->nullable();
            $table->foreign('box_id')->references('id')->on('boxes')->onDelete('cascade');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('division_areas');
    }
};
