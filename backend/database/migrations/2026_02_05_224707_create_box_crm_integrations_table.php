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
        Schema::create('box_crm_integrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_api_id');
            $table->unsignedBigInteger('box_id');
            $table->string('status')->default('Enabled'); // Enabled, Disabled
            $table->string('connection_status')->nullable(); // Connected, Disconnected
            $table->string('last_checked_date')->nullable();
            $table->text('error_log')->nullable();
            $table->json('params')->nullable();
            $table->string('last_lead_date')->nullable();
            $table->string('token')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('lead_api_id')->references('id')->on('crm_integrations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('box_crm_integrations');
    }
};
