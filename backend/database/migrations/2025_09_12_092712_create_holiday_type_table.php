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
        Schema::create('holiday_type', function (Blueprint $table) {
            $table->id();
        $table->unsignedBigInteger('box_id');

        $table->string('holiday_type')->nullable();
        $table->string('holiday_code')->nullable();
        $table->date('holiday_date')->nullable();
        $table->boolean('status')->default(0);
        $table->unsignedBigInteger('created_by')->nullable();
        $table->unsignedBigInteger('updated_by')->nullable();
        $table->unsignedBigInteger('deleted_by')->nullable();
        $table->softDeletes();
        $table->timestamps();

        // Foreign keys
        $table->foreign('box_id')
              ->references('id')->on('boxes')
              ->onDelete('restrict');

        $table->foreign('created_by')
              ->references('id')->on('users')
              ->onDelete('restrict');

        $table->foreign('updated_by')
              ->references('id')->on('users')
              ->onDelete('restrict');

        $table->foreign('deleted_by')
              ->references('id')->on('users')
              ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holiday_type');
    }
};
