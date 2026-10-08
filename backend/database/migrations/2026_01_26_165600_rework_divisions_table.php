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
        // We will drop the table if it exists to ensure a clean slate matching structure
        // Or modify it. Dropping is safer if we are disregarding old structure 'state_id'.
        Schema::dropIfExists('divisions');

        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('state_id')->nullable();
            
            $table->unsignedBigInteger('box_id')->nullable()->index();
            $table->foreign('box_id')->references('id')->on('boxes')->onDelete('cascade');

            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            $table->string('name');
            $table->boolean('auto_map_location')->default(false); // User said auto_map_loaction, but English is location
            $table->boolean('limit_member')->default(false);
            $table->integer('max_members')->default(0);
            
            $table->unsignedBigInteger('added_by')->nullable()->index();
            $table->foreign('added_by')->references('id')->on('users')->onDelete('cascade');

            $table->enum('status', ['Enable', 'Disable'])->default('Enable')->index();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('divisions');
    }
};
