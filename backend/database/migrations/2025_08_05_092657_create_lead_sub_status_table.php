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
        Schema::create('lead_sub_status', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('leads_status_id')->nullable();
            $table->unsignedBigInteger('box_id')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->string('name')->nullable();
            $table->boolean('status')->default(true);
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('leads_status_id')->references('id')->on('contact_stage')->onDelete('restrict');
            $table->foreign('box_id')->references('id')->on('boxes')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_sub_status');
    }
};
