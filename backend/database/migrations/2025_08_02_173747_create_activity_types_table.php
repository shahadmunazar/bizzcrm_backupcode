<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('activity_setting_id')->nullable();
            $table->unsignedBigInteger('box_id')->nullable();
            $table->string('activity_type')->nullable();
            $table->boolean('status')->default(true);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('activity_setting_id')->references('id')->on('calls')->onDelete('restrict');
            $table->foreign('box_id')->references('id')->on('boxes')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_types');
    }
};
