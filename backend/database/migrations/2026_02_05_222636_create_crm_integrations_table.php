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
        Schema::create('crm_integrations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('system_name')->unique();
            $table->string('status')->default('Enabled');
            $table->json('params')->nullable();
            $table->string('icon')->nullable();
            $table->string('icon_type')->default('img'); // img or icon
            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_integrations');
    }
};
