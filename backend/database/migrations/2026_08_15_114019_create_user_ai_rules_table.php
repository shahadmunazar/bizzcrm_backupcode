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
        Schema::create('user_ai_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('box_id')->nullable();
            $table->foreign('box_id')->references('id')->on('boxes')->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->string('module')->index();
            $table->string('trigger')->nullable()->index();
            $table->json('conditions')->nullable();
            $table->json('actions')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_ai_rules');
    }
};
