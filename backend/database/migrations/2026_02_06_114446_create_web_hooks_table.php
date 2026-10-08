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
        Schema::create('web_hooks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_id');
            $table->unsignedBigInteger('lead_api_box_id');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('query')->nullable();
            $table->string('company_name')->nullable();
            $table->json('params')->nullable();
            $table->string('status')->default('Pending'); // Imported, Updated, Skipped
            $table->unsignedBigInteger('lead_id')->nullable();
            $table->timestamps();

            $table->foreign('box_id')->references('id')->on('boxes')->onDelete('cascade');
            $table->foreign('lead_api_box_id')->references('id')->on('box_crm_integrations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_hooks');
    }
};
