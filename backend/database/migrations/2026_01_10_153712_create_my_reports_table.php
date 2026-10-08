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
        Schema::create('my_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('name')->nullable();
            $table->boolean('is_public')->nullable();
            $table->string('sort_by')->nullable();
            $table->string('sort_dir')->nullable();
            $table->boolean('is_default')->nullable();
            $table->string('page_length')->nullable();
            $table->string('display_start')->nullable();
            $table->boolean('show_in_report')->nullable();
            $table->boolean('is_system')->nullable();
            $table->json('filters')->nullable();
            $table->json('columns')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('my_reports');
    }
};
