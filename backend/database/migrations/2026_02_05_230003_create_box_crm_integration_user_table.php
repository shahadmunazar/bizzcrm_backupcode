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
        Schema::create('box_crm_integration_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_crm_integration_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('box_crm_integration_id', 'bc_integration_fk')->references('id')->on('box_crm_integrations')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('box_crm_integration_user');
    }
};
