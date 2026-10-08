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
        Schema::create('hr_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_id')->nullable();
            $table->unsignedBigInteger('box_user_id')->nullable();
            $table->foreign('box_user_id')->references('id')->on('box_users')->onDelete('cascade');
            $table->string('name');
            $table->string('category')->nullable();
            $table->string('file_path');
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_documents');
    }
};
