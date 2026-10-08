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
        Schema::create('table_column_preferences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_id');
            $table->string('table_name');
            $table->string('column_name');
            $table->integer('order')->default(0);
            $table->boolean('visible')->default(true);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['box_id', 'table_name', 'column_name'], 'box_table_column_unique');
            $table->foreign('box_id')->references('id')->on('boxes')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_column_preferences');
    }
};
